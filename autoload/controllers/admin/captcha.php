<?php

	// Captcha

	class Captcha extends AdminModule
	{
		static
			$TITLE  = 'Captcha',
			$IDENT  = 'captcha';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/info.png',
				'LARGE' => 'icon.large/info.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Questions',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'admin.captcha';


		static function Model($id = NULL)
		{
			return Model::Captcha($id);
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['answer']->Details('Comma separated accepted values');

			return $fields;
		}
	}

?>