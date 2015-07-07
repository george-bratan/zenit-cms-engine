<?php

	// CmsArticles

	class CmsArticles extends AdminModule
	{
		static
			$TITLE  = 'Articles',
			$IDENT  = 'cms.articles';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/pencil.png',
				'LARGE' => 'icon.large/pencil.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Articles',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$HELP = 'cms.articles';

		static
			$AUTH = 'cms.articles';

		static
			$STATUS = Array(
				0 => 'Draft',
				1 => 'Published',
			);


		static function Model($id = NULL)
		{
			return Model::CmsArticle($id);
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

			UI::set('FORMAT.category', function($record) {
				//
				$category = Model::CmsCategory( $record['idcategory'] );
				return $category->name;
			});

			UI::set('FORMAT.comments', function($record) {
				//
				$num = DB::Fetch("SELECT COUNT(*) FROM cms_articles_comments WHERE idarticle = ?", array($record['id']));
				$new = DB::Fetch("SELECT COUNT(*) FROM cms_articles_comments WHERE idarticle = ? AND status = 0", array($record['id']));

				return ($new ? ' <span style="color:red">'. $new .'</span> / ' : '').
					intval($num);
			});

			UI::set('FORMAT.status', function($record) {
				//
				return '<span style="color:'.($record['status'] ? 'green' : 'red').'">' . CmsArticles::$STATUS[ $record['status'] ] . '</span>';
			});

			parent::GET_List($page);
		}

		static function GET_Details($feed = NULL, $html = NULL)
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('ARTICLE', $model->record());
			UI::set('ARTICLE.LABELS', $model->labels->export());
			UI::set('ARTICLE.IMAGES', $model->images->export());
			UI::set('ARTICLE.LINKS', $model->links->export());
			UI::set('ARTICLE.COMMENTS', $model->comments->export());

			$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM cms_categories WHERE status > -1 ORDER BY name ASC) ");
			UI::set('CATEGORIES', $categories);
			UI::set('STATUS', self::$STATUS);

			UI::set('FOOTER', UI::Render('admin/cms.article.comments.php'));
			UI::set('CONTENT', UI::Render('admin/cms.article.details.php'));

			UI::set('TOOLBAR.save', array(
					'id' => 'btn_save',
					'url' => Request::$URL . '/save/'. $model->id,
					'rel' => 'none',
					'icon' => 'icon.small/disk.png',
					'title' => 'Save'
				)
			);

			UI::set('SECTION', 'Article: '.$model->name);

			self::Wrapper();
		}

		/*
		static function GET_Edit()
		{
			parent::GET_Details();
		}
		*/

		static function POST_Save()
		{
			$model = parent::POST_Save();

			$model->stub = Util::URL( $model->title );
			$model->save();

			return $model;
		}

		static function GET_File()
		{
			$model = Model::CmsArticleFile( intval( Request::URL('id') ) );

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
			$model = Model::CmsArticleFile( intval( Request::URL('id') ) );

			$model->title = Request::POST('VALUES.title');
			$model->name = Request::POST('VALUES.name');
			$model->description = Request::POST('VALUES.description');

			$model->save();
		}

		static function GET_Detach()
		{
			$file = Model::CmsArticleFile( intval( Request::URL('id') ) );

			UI::set('TARGET', Request::$URL . '/detach/' . Request::URL('id'));
			UI::set('CONTENT', 'Are you sure you want to remove <strong>'.($file->name).'</strong> from the article gallery ?');

			UI::set('TITLE', 'Confirmation Required');
			UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

			parent::Popup();
		}

		static function POST_Detach()
		{
			$file = Model::CmsArticleFile( intval( Request::URL('id') ) );
			$file->status = -1;

			$file->save();
		}

		static function POST_Reorder()
		{
			$items = Request::POST('items');

			foreach ($items as $ord => $id) {
				//
				$file = Model::CmsArticleFile( $id );
				$file->ord = $ord;
				$file->save();
			}
		}

		static function POST_Files()
		{
			if (!is_dir(Conf::get('APP:UPLOAD') . 'articles/')) {
				Util::mkdir(Conf::get('APP:UPLOAD') . 'articles/');
			}

			ob_start();

			Request::$Params['POST']['currentpath'] = '/articles/';
			include( 'cms.documents.uploader.php' );

			$files = json_decode( ob_get_contents() );
			foreach ($files as $file) {
				//
				if ($file->url) {
					//
					$model = Model::CmsArticleFile();
					$model->defaults();

					$temp = array(
			        	'name' => $file->name,
			        	'ext'  => substr($file->name, strrpos($file->name, '.') + 1),
			        	'type' => $file->type,
			        	'size' => $file->size,
			        	'server_path' => Conf::get('APP:UPLOAD'),
			        	'server_name' => "articles/" . $file->name,
			        	'timestamp'   => time(),
			        );

					$model->idarticle = intval( Request::URL('id') );
					$model->name = $temp['name'];
					$model->disk = $temp['server_name'];
					$model->save();

					$perm = File::Permanent($temp, 'articles/file', $model->id);
					$model->name = $perm['name'];
					$model->disk = $perm['server_name'];
					$model->save();

					$file->url = Conf::get('WWW:UPLOAD') . $model->disk;
				}
			}

			ob_end_clean();

			print json_encode( $files );
		}

		static function GET_Links()
		{
			$model = Model::CmsArticle( intval( Request::URL('id') ) );

			UI::set('LINKS', $model->links->export());

			UI::set('TITLE', 'Manage Outgoing Links');
			UI::set('TARGET', Request::$URL . '/links/' . $model->id);
			UI::set('CONTENT', UI::Render('admin/cms.article.links.php'));

			parent::Popup();
		}

		static function POST_Links()
		{
			$model = Model::CmsArticle( intval( Request::URL('id') ) );

			for ($i = 0; $i < 5; $i++) {
				//
				if (Request::POST("LINKS.title.{$i}")) {
					//
					$link = Model::CmsArticleLink();
					$link->where('idarticle = ? AND ord = ?', $model->id, $i)
						->execute();

					$link->defaults();

					$link->idarticle = $model->id;
					$link->title = Request::POST("LINKS.title.{$i}");
					$link->url = Request::POST("LINKS.url.{$i}");
					$link->ord = $i;

					$link->save();
				}
			}
		}

		static function GET_Labels()
		{
			$model = self::Model( intval(Request::URL('id')) );

			$labels = Model::CmsLabel();
			$labels->where('status > -1')
				->execute();

			UI::Set('TARGET', Request::$URL . '/labels/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $labels->export());
			UI::Set('SELECTED', $model->labels->slice('id'));

			UI::Set('TITLE', 'Labels');
			UI::Set('CONTENT', 'Select labels:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Labels()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				$items = array();
			}

			$model = self::Model( intval(Request::URL('id')) );

			$sql = new SQL();
			$sql->delete('cms_articles_labels')
				->where('idarticle = ?', $model->id)
				->execute();

			foreach ($items as $label) {
				//
				$sql = new SQL();
				$sql->insert('cms_articles_labels')
					->set('idarticle = ?, idlabel = ?', $model->id, $label)
					->execute();
			}
		}

		static function GET_Contact()
		{
			if (Request::URL('id')) {
				//
				$model = static::Model( intval( Request::URL('id') ) );

				UI::set('ITEM', $model->record());

				UI::set('TITLE', 'Edit Contact Details');
				UI::set('FIELDS', static::ContactForm( $model ));
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function GET_Location()
		{
			if (Request::URL('id')) {
				//
				$model = static::Model( intval( Request::URL('id') ) );

				UI::set('ITEM', $model->record());

				UI::set('TITLE', 'Edit Location Details');
				UI::set('FIELDS', static::LocationForm( $model ));
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
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

		static function POST_CanComment()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('status') !== NULL) {
				//
				$model->cancomment = Request::POST('status');
			}
			else {
				//
				$model->cancomment = ($model->status == 1) ? 0 : 1;
			}

			$model->save();
		}

		static function POST_Comment()
		{
			$comment = Model::CmsArticleComment();
			$comment->defaults();

			$comment->idarticle = intval( Request::URL('id') );
			$comment->content = Request::POST('VALUES.content');
			$comment->name = Session::Get('ACCOUNT.NAME');
			$comment->email = Session::Get('ACCOUNT.EMAIL');
			$comment->status = 1;

			$comment->save();
		}

		static function POST_Accept()
		{
			$comment = Model::CmsArticleComment( intval( Request::URL('id') ) );
			$comment->status = 1;

			$comment->save();
		}

		static function POST_Reject()
		{
			$comment = Model::CmsArticleComment( intval( Request::URL('id') ) );
			$comment->status = -1;

			$comment->save();
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			foreach ($fields as $key => $field) {
				//
				if (!in_array($key, array('title', 'idcategory'))) {
					//
					unset($fields[ $key ]);
				}
			}

			$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM cms_categories WHERE status > -1 ORDER BY name ASC) ");
			$fields['idcategory']->Type( Input::F_SELECT )->Options( $categories );

			return $fields;
		}

		static function ContactForm( $model )
		{
			$fields = parent::EditForm( $model );

			foreach ($fields as $key => $field) {
				//
				if (!in_array($key, Util::split('contact|email|phone|url'))) {
					//
					unset($fields[ $key ]);
				}
			}

			return $fields;
		}

		static function LocationForm( $model )
		{
			$fields = parent::EditForm( $model );

			foreach ($fields as $key => $field) {
				//
				if (!in_array($key, Util::split('location|address|coords'))) {
					//
					unset($fields[ $key ]);
				}
			}

			$fields['address']->Type( Input::F_LONGTEXT )->Height( '60px' );
			$fields['coords']->Type( Input::F_MAP )->Alt( $model->address )->Width('98%');

			return $fields;
		}

		static function DataFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = array(
				'CMS.ARTICLE.DETAIL' => Array(
					'title' => 'Article Details',
					'hint' => 'URL requires <strong>@article</strong> containing the article title or <strong>@id</strong> containing the Article ID',
				),
				'CMS.ARTICLE.LIST' => Array(
					'title' => 'Article List',
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

			if ($feed == 'CMS.ARTICLE.DETAIL') {
				//
				$model = Model::CmsArticle();
				$model->where('status = 1');

				if (isset($filters['id'])) {
					//
					$model->where('id = ?', $filters['id']);
				}
				elseif (Request::URL('id')) {
					//
					$model->where('id = ?', Request::URL('id'));
				}
				elseif (Request::URL('article')) {
					//
					$model->where('stub = ?', Request::URL('article'));
				}
				$model->execute();

				// POSSIBLE FORM SUBMISSION

				$error = '';
				if (Request::POST('comment.submit')) {
					//
					if (!Request::POST('comment.name') || !Request::POST('comment.email') || !Request::POST('comment.content')) {
						$error = 'Please fill in all mandatory fields: Name, Email and Comment.';
					}
					if (intval(Request::POST('comment.captcha')) != 5) {
						$error = 'Please make sure you read and enter a value for the Human Validation Textbox.';
					}

					if (!$error) {
						//
						$comment = Model::CmsArticleComment();
						$comment->defaults();

						$comment->idarticle = $model->id;
						$comment->name = Request::POST('comment.name');
						$comment->email = Request::POST('comment.email');
						$comment->url = Request::POST('comment.website');
						$comment->content = Request::POST('comment.content');

						$comment->save();
						$error = 'Your comment has been submitted. Thank You!';
					}
				}

				$form = Array(
					'error' => $error,
					'name' => '<input id="comment-name" type="text" name="comment[name]" value="" />',
					'email' => '<input id="comment-email" type="text" name="comment[email]" value="" />',
					'website' => '<input id="comment-website" type="text" name="comment[website]" value="" />',
					'comment' => '<textarea id="comment-content" name="comment[content]"></textarea>',

					'captcha' => 'What is two plus three? <input id="comment-captcha" type="text" name="comment[captcha]" value="" />',
					'submit' => '<input id="comment-submit" type="submit" name="comment[submit]" value="Submit" />',
				);

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/cms.article.php');

				$RESULT['RESULT']['ARTICLE'] = $model->found() ? $model->record( $depth = 4 ) : FALSE;
				$RESULT['RESULT']['FORM'] = $form;

				$RESULT['PROPERTIES'] = Array(
					'ARTICLE.id' => 'Article ID',
					'ARTICLE.title' => 'Article Title',
					'ARTICLE.body' => 'Article Body',
					'ARTICLE.category' => 'Category',
					'ARTICLE.keywords' => 'Keywords',
					'ARTICLE.pubdate' => 'Publication Date',
					'ARTICLE.startdate' => 'Event Start Date',
					'ARTICLE.enddate' => 'Event End Date',
					'ARTICLE.location' => 'Event Location',
					'ARTICLE.address' => 'Address',
					'ARTICLE.contact' => 'Contact Name',
					'ARTICLE.email' => 'Contact Email',
					'ARTICLE.phone' => 'Phone',
					'ARTICLE.url' => 'Website',

					'{foreach $ARTICLE.images as $IMAGE}'."\n\t\n".'{/foreach}' => 'Loop Through Images',
					'IMAGE.url' => '- Image URL',
					'IMAGE.title' => '- Image Title',
					'IMAGE.description' => '- Image Description',

					'{foreach $ARTICLE.links as $LINK}'."\n\t\n".'{/foreach}' => 'Loop Through Links',
					'LINK.title' => '- Link Title',
					'LINK.url' => '- Link URL',

					'{foreach $ARTICLE.comments as $COMMENT}'."\n\t\n".'{/foreach}' => 'Loop Through Comments',
					'COMMENT.name' => '- Comment Name',
					'COMMENT.url' => '- Comment Website',
					'COMMENT.content' => '- Comment Content',

					'FORM.name' => 'Comment Form: Name',
					'FORM.email' => 'Comment Form: Email',
					'FORM.website' => 'Comment Form: Website',
					'FORM.captcha' => 'Comment Form: Captcha',
					'FORM.comment' => 'Comment Form: Comment',
					'FORM.submit' => 'Comment Form: Submit Button',
					'FORM.error' => 'Comment Form: Submission Result',
				);

				$RESULT['FILTERS'] = Array();

				$articles = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, title FROM cms_articles WHERE status = 1 ORDER BY pubdate DESC)");

				$input = new Input('id');
				$input->Type(Input::F_SELECT)->Options($articles)->Title('Article');
				if (isset($filters['id'])) {
					$input->Value($filters['id']);
				}
				$RESULT['FILTERS']['id'] = $input;

				return $RESULT;
			}

			if ($feed == 'CMS.ARTICLE.LIST') {
				//
				$model = Model::CmsArticle();
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
					$category = Model::CmsCategory();
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
					$model->where("body LIKE '%" . $filters['search'] . "%'");
				}
				elseif (Request::URL('search')) {
					$model->where("body LIKE '%" . Request::URL('search') . "%'");
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

				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/cms.article.list.php');

				$RESULT['RESULT']['ARTICLES'] = $model->export( $key = '', $depth = 4 );
				$RESULT['RESULT']['PAGE'] = Array(
					'count' => isset($filters['pagination']) ? intval($model->count() / $filters['pagination']) + ($model->count() % $filters['pagination'] ? 1 : 0) : 0,
					'page' => intval( Request::URL('page') ),
				);

				$RESULT['PROPERTIES'] = Array(
					'{foreach $ARTICLES as $ARTICLE}'."\n\t\n".'{/foreach}' => 'Loop Through Articles',
					'ARTICLE.id' => '- Article ID',
					'ARTICLE.title' => '- Article Title',
					'ARTICLE.stub' => '- URL friendly title',
					'ARTICLE.body' => '- Article Body',
					'ARTICLE.category' => '- Category',
					'ARTICLE.keywords' => '- Keywords',
					'ARTICLE.pubdate' => '- Publication Date',
					'ARTICLE.startdate' => '- Event Start Date',
					'ARTICLE.enddate' => '- Event End Date',
					'ARTICLE.location' => '- Event Location',
					'ARTICLE.address' => '- Address',
					'ARTICLE.contact' => '- Contact Name',
					'ARTICLE.email' => '- Contact Email',
					'ARTICLE.phone' => '- Phone',
					'ARTICLE.url' => '- Website',

					'{foreach $ARTICLE.images as $IMAGE}'."\n\t\n".'{/foreach}' => 'Loop Through Images',
					'IMAGE.url' => '- Image URL',
					'IMAGE.title' => '- Image Title',
					'IMAGE.description' => '- Image Description',

					'{foreach $ARTICLE.links as $LINK}'."\n\t\n".'{/foreach}' => 'Loop Through Links',
					'LINK.title' => '- Link Title',
					'LINK.url' => '- Link URL',

					'PAGE.count' => 'Number of Pages',
					'PAGE.page' => 'Current Page',
				);

				$RESULT['FILTERS'] = Array();

				$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM cms_categories WHERE status = 1 ORDER BY name ASC)");
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


			if ($feed == 'CMS.ARTICLE.DETAIL') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/cms.article.php', $data['RESULT']);
			}


			if ($feed == 'CMS.ARTICLE.LIST') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/cms.article.list.php', $data['RESULT']);
			}

		}

	}

?>