<?php

	// CmsMenus

	class CmsMenus extends AdminModule
	{
		static
			$TITLE  = 'Menus',
			$IDENT  = 'cms.menus';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/sitemap.png',
				'LARGE' => 'icon.large/network.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Menus',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'cms.menus';


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function Model($id = NULL)
		{
			return Model::CmsMenu($id);
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

			parent::GET_List($page);
		}

		static function GET_Details()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('MENU', $model->record());

			UI::set('MENU.ITEMS', $model->items->export());

			UI::set('CONTENT', UI::Render('admin/cms.menu.details.php'));

			UI::set('SECTION', 'Menu: '.$model->name);

			self::Wrapper();
		}

		static function GET_Edit()
		{
			parent::GET_Details();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			return $fields;
		}

		static function DataFeed( $feed = NULL )
		{
			if (!$feed) {
				return array(
					'CMS.MENU.ITEMS' => 'Menu Items',
				);
			}

			if ($feed == 'CMS.MENU.ITEMS') {
				//
				$model = Model::CmsMenu();
				if (isset($filters['id'])) {
					$model->where('id = ?', $filters['id']);
				}
				$model->execute();

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/cms.menu.php');

				$RESULT['RESULT']['MENU'] = $model->record( $depth = 4 );
				$RESULT['RESULT']['MENU']['items'] = $model->get_items(0)->export( $key ='', $depth = 4 );

				$RESULT['PROPERTIES'] = Array(
					'MENU.id' => 'Menu ID',
					'MENU.name' => 'Menu Name',

					'{foreach $MENU.items as $ITEM}'."\n\t\n".'{/foreach}' => 'Loop Through Menu Items',
					'ITEM.id' => 'Item ID',
					'ITEM.url' => 'Item URL',
					'ITEM.caption' => 'Item Caption',
					'ITEM.children' => 'Item Children',
				);

				$RESULT['FILTERS'] = Array();

				$menus = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM cms_menus WHERE status > -1 ORDER BY name ASC)");

				$input = new Input('id');
				$input->Type(Input::F_SELECT)->Options($menus)->Title('Menu');
				if (isset($filters['id'])) {
					$input->Value($filters['id']);
				}
				$RESULT['FILTERS']['id'] = $input;

				return $RESULT;
			}
		}

		static function HtmlFeed( $feed = NULL )
		{
			if (!$feed) {
				//
				return DB::AssociativeColumn("SELECT CONCAT('CMS.MENU.', id), name FROM cms_menus WHERE status > -1");
			}

			$id = str_replace('CMS.MENU.', '', $feed);

			$menu = Model::CmsMenu( $id );

			return self::Render( $menu->get_items(0) );
		}

		static function Render($items)
		{
			if (!$items->found()) {
				//
				return '';
			}

			$result = '<ul>';
			$items->reset();
			while ($items->next()) {
				//
				$result .= '<li><a href="'.($items->url ? $items->url : '#').'">'.$items->caption.'</a>'. (self::Render($items->children)) .'</li>';
			}
			$result .= '</ul>';

			return $result;
		}

	}

?>