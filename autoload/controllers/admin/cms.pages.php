<?php

	// CmsPages

	class CmsPages extends CmsTemplates
	{
		static
			$TITLE  = 'Pages',
			$IDENT  = 'cms.pages';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/page.full.png',
				'LARGE' => 'icon.large/page.full.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Pages',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'cms.pages';

		static
			$TYPE = 'cms.page';


		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['name']->Title('Page Name');

			return $fields;
		}

		static function FilterForm()
		{
			$fields = parent::FilterForm();

			$fields['name']->Title('Page Name');

			return $fields;
		}

	}

?>