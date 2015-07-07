<?php

	// CmsMenuItems

	class CmsMenuItems extends AdminModule
	{
		static
			$TITLE  = 'Menu Items',
			$IDENT  = 'cms.menu.items';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/sitemap.png',
				'LARGE' => 'icon.large/network.png',
			);

		/*
		static
			$PERMISSION = Array(
				'list' 		=> 'List Menu Items',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'cms.menuitems';
		*/


		static function Model($id = NULL)
		{
			return Model::CmsMenuItem($id);
		}

		static function GET_New()
		{
			$fields = static::EditForm( static::Model() );

			if (intval( Request::URL('id') )) {
				//
				$fields['idmenu']->Value( intval( Request::URL('id') ) );
			}

			UI::set('TITLE', 'New Record');
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function GET_Details()
		{
			if (Request::URL('id')) {
				//
				$model = static::Model( intval( Request::URL('id') ) );

				UI::set('ITEM', $model->record());

				UI::set('TITLE', 'Edit: '.$model->name);
				UI::set('FIELDS', static::EditForm( $model ));

				//$pages = DB::AssociativeColumn("SELECT id, name FROM cms_templates WHERE type = 'cms.page' AND version > 0 AND status > -1");
				//$fields['idpage']->Type(Input::F_SELECT)->Options($pages)->Align('right')->Multiple(TRUE);
				$pages = Model::CmsTemplate();
				$pages->where("type = 'cms.page' AND version > 0 AND status > -1")
					->execute();

				UI::set('PAGES', $pages->export());
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/cms.menu.item.php'));

			parent::Popup();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if (!$model->ord) {
				//
				$model->ord = $model->id;
				$model->save();
			}

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['idmenu']->Type(Input::F_HIDDEN);

			return $fields;
		}

		static function POST_Up()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				//
				static::Error('You must select at least one record.');
				return;
			}

			// reorder
			$items = DB::Column("SELECT id FROM cms_menu_items WHERE id IN ?? ORDER BY ord ASC", array($items));

			foreach ($items as $itemid) {
				//
				$item = Model::CmsMenuItem( $itemid );

				$pair = DB::Column('SELECT id FROM cms_menu_items WHERE idmenu = ? AND status > 0 AND ord <= ? ORDER BY ord DESC LIMIT 2', array($item->idmenu, $item->ord));

				if (count($pair) < 2) {
					//
					static::Error('The items you selected cannot be moved up any more');
					return;
				}

				self::SwapItems($pair[0], $pair[1]);
			}

			self::RebuildMenu( $item->idmenu );
		}

		static function POST_Down()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				//
				static::Error('You must select at least one record.');
				return;
			}

			// reorder
			$items = DB::Column("SELECT id FROM cms_menu_items WHERE id IN ?? ORDER BY ord DESC", array($items));

			foreach ($items as $itemid) {
				//
				$item = Model::CmsMenuItem( $itemid );

				$pair = DB::Column('SELECT id FROM cms_menu_items WHERE idmenu = ? AND status > 0 AND ord >= ? ORDER BY ord ASC LIMIT 2', array($item->idmenu, $item->ord));

				if (count($pair) < 2) {
					//
					static::Error('The items you selected cannot be moved down any more');
					return;
				}

				self::SwapItems($pair[0], $pair[1]);
			}

			self::RebuildMenu( $item->idmenu );
		}

		static function POST_Left()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				//
				static::Error('You must select at least one record.');
				return;
			}

			DB::Execute("UPDATE cms_menu_items SET level = level-1 WHERE id IN ??", array($items));

			$menu = DB::Fetch("SELECT idmenu FROM cms_menu_items WHERE id = ?", array($items[0]));
			self::RebuildMenu( $menu );
		}

		static function POST_Right()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				//
				static::Error('You must select at least one record.');
				return;
			}

			DB::Execute("UPDATE cms_menu_items SET level = level+1 WHERE id IN ??", array($items));

			$menu = DB::Fetch("SELECT idmenu FROM cms_menu_items WHERE id = ?", array($items[0]));
			self::RebuildMenu( $menu );
		}

		static function SwapItems($item0, $item1)
		{
			$item0 = Model::CmsMenuItem( $item0 );
			$item1 = Model::CmsMenuItem( $item1 );

			$ord = $item0->ord;
			$item0->ord = $item1->ord;
			$item1->ord = $ord;

			$item0->save();
			$item1->save();
		}

		static function RebuildMenu( $menu )
		{
			$items = DB::Column("SELECT id FROM cms_menu_items WHERE idmenu = ? AND status > 0 ORDER BY ord ASC", array($menu));

			$prev = FALSE;
			$parents = array(0);

			foreach ($items as $item) {
				//
				$item = Model::CmsMenuItem( $item );

				$maxlevel = $prev ? $prev->level + 1 : 0;

				$item->level = min($item->level, $maxlevel);
				$item->level = max($item->level, 0);

				$item->idparent = $parents[ $item->level ];

				$item->save();

				$parents[ $item->level+1 ] = $item->id;
				$prev = $item;
			}
		}

	}

?>