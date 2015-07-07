<?php

	// ShopProducts

	class ShopProducts extends AdminModule
	{
		static
			$TITLE  = 'Products',
			$IDENT  = 'shop.products';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/apple.png',
				'LARGE' => 'icon.large/apple.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Products',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'shop.products';

		static
			$STOCK = Array(
				0 => 'Stock Empty',
				1 => 'Order Based',
				2 => 'In Stock',
			);

		static
			$STATUS = Array(
				0 => 'Disabled',
				1 => 'Enabled',
			);


		static function Model($id = NULL)
		{
			return Model::ShopProduct($id);
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}

			/*
			UI::set('FORMAT.category', function($record) {
				//
				$category = Model::ShopCategory( $record['idcategory'] );
				return $category->name;
			});
			*/

			UI::set('FORMAT.price', function($record) {
				//
				return number_format($record['price'], 2) . ' ' . Model::Settings('shop.currency')->value;
			});

			parent::GET_List( $page );
		}

		static function GET_Details($feed = NULL, $html = NULL)
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('PRODUCT', $model->record());
			UI::set('PRODUCT.IMAGES', $model->images->export());

			$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM shop_categories WHERE status > -1 ORDER BY name ASC) ");
			UI::set('CATEGORIES', $categories);
			UI::set('STOCK', self::$STOCK);
			UI::set('STATUS', self::$STATUS);

			//UI::set('FOOTER', UI::Render('admin/shop.product.comments.php'));
			UI::set('CONTENT', UI::Render('admin/shop.product.details.php'));

			UI::set('TOOLBAR.save', array(
					'id' => 'btn_save',
					'url' => Request::$URL . '/save/'. $model->id,
					'rel' => 'none',
					'icon' => 'icon.small/disk.png',
					'title' => 'Save'
				)
			);

			UI::set('SECTION', 'Product: '.$model->name);

			self::Wrapper();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			$model->stub = Util::URL( $model->name );
			$model->save();

			return $model;
		}

		static function GET_File()
		{
			$model = Model::ShopProductFile( intval( Request::URL('id') ) );

			$fields = array();

			foreach ($model::$schema as $field => $title) {
				//
				$input = new Input($field);
				$input->Type(Input::F_TEXT)->Title($title)->Context('VALUES')->Value( $model->$field );

				$fields[$field] = $input;
			}
			$fields['description']->Type( Input::F_RICHTEXT );

			UI::set('ITEM', $model->record());

			UI::set('TITLE', 'File: '.$model->name);
			UI::set('FIELDS', $fields);

			UI::set('TARGET', Request::$URL . '/file/' . $model->id);
			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_File()
		{
			$model = Model::ShopProductFile( intval( Request::URL('id') ) );

			$model->title = Request::POST('VALUES.title');
			$model->name = Request::POST('VALUES.name');
			$model->description = Request::POST('VALUES.description');

			$model->save();
		}

		static function GET_Detach()
		{
			$file = Model::ShopProductFile( intval( Request::URL('id') ) );

			UI::set('TARGET', Request::$URL . '/detach/' . Request::URL('id'));
			UI::set('CONTENT', 'Are you sure you want to remove <strong>'.($file->name).'</strong> from the product gallery ?');

			UI::set('TITLE', 'Confirmation Required');
			UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

			parent::Popup();
		}

		static function POST_Detach()
		{
			$file = Model::ShopProductFile( intval( Request::URL('id') ) );
			$file->status = -1;

			$file->save();
		}

		static function POST_Reorder()
		{
			$items = Request::POST('items');

			foreach ($items as $ord => $id) {
				//
				$file = Model::ShopProductFile( $id );
				$file->ord = $ord;
				$file->save();
			}
		}

		static function POST_Files()
		{
			if (!is_dir(Conf::get('APP:UPLOAD') . 'products/')) {
				Util::mkdir(Conf::get('APP:UPLOAD') . 'products/');
			}

			ob_start();

			Request::$Params['POST']['currentpath'] = '/products/';
			include( 'cms.documents.uploader.php' );

			$files = json_decode( ob_get_contents() );
			foreach ($files as $file) {
				//
				if ($file->url) {
					//
					$model = Model::ShopProductFile();
					$model->defaults();

					$temp = array(
			        	'name' => $file->name,
			        	'ext'  => substr($file->name, strrpos($file->name, '.') + 1),
			        	'type' => $file->type,
			        	'size' => $file->size,
			        	'server_path' => Conf::get('APP:UPLOAD'),
			        	'server_name' => "products/" . $file->name,
			        	'timestamp'   => time(),
			        );

					$model->idproduct = intval( Request::URL('id') );
					$model->name = $temp['name'];
					$model->disk = $temp['server_name'];
					$model->save();

					$perm = File::Permanent($temp, 'products/file', $model->id);
					$model->name = $perm['name'];
					$model->disk = $perm['server_name'];
					$model->save();

					$file->url = Conf::get('WWW:UPLOAD') . $model->disk;
				}
			}

			ob_end_clean();

			print json_encode( $files );
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('status') !== NULL) {
				//
				$model->status = Request::POST('status');
			}
			else {
				//
				$model->status = ($model->status == 1) ? 0 : 1;
			}

			$model->save();
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			foreach ($fields as $key => $field) {
				//
				if (!in_array($key, array('name', 'idcategory'))) {
					//
					unset($fields[ $key ]);
				}
			}

			$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM shop_categories WHERE status > -1 ORDER BY name ASC) ");
			$fields['idcategory']->Type( Input::F_SELECT )->Options( $categories );

			return $fields;
		}

		static function FilterForm()
		{
			$fields = parent::FilterForm();

			$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM shop_categories WHERE status > -1 ORDER BY name ASC) ");
			$fields['idcategory']->Type( Input::F_SELECT )->Options( $categories );

			return $fields;
		}

		static function DataFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = array(
				'SHOP.PRODUCT.DETAIL' => Array(
					'title' => 'Product Details',
					'hint' => 'URL requires <strong>@product</strong> containing the Product Title or <strong>@id</strong> containing the Product ID',
				),
				'SHOP.PRODUCT.LIST' => Array(
					'title' => 'Product List',
					'hint' => 'Accepts the following URL codes for filtering: '.
								'<ul style="margin-left: 20px;"><li><strong>@category</strong> containing the category name for filtering</li>'.
									'<li><strong>@keyword</strong> containing a keyword to be matched</li>'.
									//'<li><strong>@search</strong> containing a search query</li>'.
									'<li><strong>@page</strong> containing a page number</li>'.
									'<li><strong>?page=</strong> containing a page number (as GET parameter)</li>'.
								'</ul>',
				),
			);

			if (!$feed) {
				//
				return $FEEDS;
			}

			if ($feed == 'SHOP.PRODUCT.DETAIL') {
				//
				$model = Model::ShopProduct();
				$model->where('status = 1');

				if (isset($filters['id'])) {
					//
					$model->where('id = ?', $filters['id']);
				}
				elseif (Request::URL('id')) {
					//
					$model->where('id = ?', Request::URL('id'));
				}
				elseif (Request::URL('product')) {
					//
					$model->where('stub = ?', Request::URL('product'));
				}
				$model->execute();

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/shop.product.php');

				$RESULT['RESULT']['PRODUCT'] = $model->found() ? $model->record( $depth = 4 ) : FALSE;

				$RESULT['PROPERTIES'] = Array(
					'PRODUCT.id' => 'Product ID',
					'PRODUCT.name' => 'Product Name',
					'PRODUCT.description' => 'Product Description',
					'PRODUCT.category' => 'Category',
					'PRODUCT.keywords' => 'Keywords',
					'PRODUCT.price' => 'Price',

					'{foreach $PRODUCT.images as $IMAGE}'."\n\t\n".'{/foreach}' => 'Loop Through Images',
					'IMAGE.url' => '- Image URL',
					'IMAGE.title' => '- Image Title',
					'IMAGE.description' => '- Image Description',
				);

				$RESULT['FILTERS'] = Array();

				$products = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM shop_products WHERE status = 1 ORDER BY name ASC)");

				$input = new Input('id');
				$input->Type(Input::F_SELECT)->Options($products)->Title('Product');
				if (isset($filters['id'])) {
					$input->Value($filters['id']);
				}
				$RESULT['FILTERS']['id'] = $input;

				return $RESULT;
			}

			if ($feed == 'SHOP.PRODUCT.LIST') {
				//
				$model = Model::ShopProduct();
				$model->where('status = 1');

				if (isset($filters['category'])) {
					//
					if (intval($filters['category'])) {
						//
						$model->where('idcategory = ?', $filters['category']);
					}
				}
				elseif (Request::URL('category')) {
					//
					$category = Model::ShopCategory();
					$category->where('stub = ?', Request::URL('category'))->execute();

					if ($category->found()) {
						//
						$model->where('idcategory = ?', $category->id);
					}
					else {
						//
						$model->where('FALSE');
					}
				}

				if (isset($filters['keyword'])) {
					$model->where("keywords LIKE '%" . $filters['keyword'] . "%'");
				}
				elseif (Request::URL('keyword')) {
					$model->where("keywords LIKE '%" . Request::URL('keyword') . "%'");
				}

				if (isset($filters['search'])) {
					$model->where("description LIKE '%" . $filters['search'] . "%'");
				}
				elseif (Request::URL('search')) {
					$model->where("description LIKE '%" . Request::URL('search') . "%'");
				}

				if (isset($filters['pagination'])) {
					$model->limit(intval( $filters['pagination'] ));

					if (Request::URL('page') || Request::GET('page')) {
						//
						$page = Request::URL('page') ? Request::URL('page') : Request::GET('page');
						$model->offset( intval($page) * intval($filters['pagination']) );
					}
				}
				elseif (isset($filters['limit'])) {
					$model->limit(intval( $filters['limit'] ));
				}

				$model->execute();

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];

				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/shop.product.list.php');

				$RESULT['RESULT']['PRODUCTS'] = $model->export( $key = '', $depth = 4 );
				$RESULT['RESULT']['PAGE'] = Array(
					'count' => isset($filters['pagination']) ? intval($model->count() / $filters['pagination']) + ($model->count() % $filters['pagination'] ? 1 : 0) : 0,
					'page' => intval( Request::URL('page') ),
				);

				$RESULT['PROPERTIES'] = Array(
					'{foreach $PRODUCTS as $PRODUCT}'."\n\t\n".'{/foreach}' => 'Loop Through Products',
					'PRODUCT.id' => '- Product ID',
					'PRODUCT.name' => '- Product Name',
					'PRODUCT.description' => '- Product Description',
					'PRODUCT.category' => '- Category',
					'PRODUCT.keywords' => '- Keywords',
					'PRODUCT.price' => '- Price',

					'{foreach $PRODUCT.images as $IMAGE}'."\n\t\n".'{/foreach}' => 'Loop Through Images',
					'IMAGE.url' => '- Image URL',
					'IMAGE.title' => '- Image Title',
					'IMAGE.description' => '- Image Description',

					'PAGE.count' => 'Number of Pages',
					'PAGE.page' => 'Current Page',
				);

				$RESULT['FILTERS'] = Array();

				$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM shop_categories WHERE status = 1 ORDER BY name ASC)");
				$input = new Input('category');
				$input->Type(Input::F_SELECT)->Options($categories)->Title('Category');
				if (isset($filters['category'])) {
					$input->Value($filters['category']);
				}
				$RESULT['FILTERS']['category'] = $input;

				$input = new Input('keyword');
				$input->Type(Input::F_TEXT)->Title('Containing Keyword');
				if (isset($filters['keyword'])) {
					$input->Value($filters['keyword']);
				}
				$RESULT['FILTERS']['keyword'] = $input;

				$input = new Input('limit');
				$input->Type(Input::F_TEXT)->Title('Limit to')->Width('80px')->Details('results');
				if (isset($filters['limit'])) {
					$input->Value($filters['limit']);
				}
				$RESULT['FILTERS']['limit'] = $input;

				$input = new Input('pagination');
				$input->Type(Input::F_TEXT)->Title('Split into pages of')->Width('80px')->Details('results / page');
				if (isset($filters['pagination'])) {
					$input->Value($filters['pagination']);
				}
				$RESULT['FILTERS']['pagination'] = $input;


				return $RESULT;
			}
		}

		static function HtmlFeed( $feed = NULL )
		{
			$FEEDS = self::DataFeed();

			if (!$feed) {
				//
				return $FEEDS;
			}


			if ($feed == 'SHOP.PRODUCT.DETAIL') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/shop.product.php', $data['RESULT']);
			}


			if ($feed == 'SHOP.PRODUCT.LIST') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/shop.product.list.php', $data['RESULT']);
			}

		}

	}

?>