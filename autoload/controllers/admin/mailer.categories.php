<?php

	// MailerCategories

	class MailerCategories extends AdminModule
	{
		static
			$TITLE  = 'Categories',
			$IDENT  = 'mailer.categories';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/star.png',
				'LARGE' => 'icon.large/star.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Categories',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'mailer.categories';


		static function Model($id = NULL)
		{
			return Model::MailerCategory($id);
		}

	}

?>