<?php

	// CrmAddresses

	class CrmAddresses extends AdminModule
	{
		static
			$TITLE  = 'Address',
			$IDENT  = 'crm.addresses';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/sitemap.png',
				'LARGE' => 'icon.large/network.png',
			);

		/*
		static
			$PERMISSION = Array(
				'list' 		=> 'List Addresses',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'crm.addresses';
		*/


		static function Model($id = NULL)
		{
			return Model::CrmAddress($id);
		}

		static function GET_New()
		{
			$fields = static::EditForm( static::Model() );

			if (intval( Request::URL('id') )) {
				//
				$fields['idcontact']->Value( intval( Request::URL('id') ) );
			}

			UI::set('TITLE', 'New Record');
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		/*
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
		*/

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

			$fields['idcontact']->Type(Input::F_HIDDEN);

			$fields['country']->Type(Input::F_SELECT)->Options( Conf::Get('COUNTRIES') );

			return $fields;
		}

	}

?>