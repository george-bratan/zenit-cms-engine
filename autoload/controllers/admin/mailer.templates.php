<?php

	// MailerTemplates

	class MailerTemplates extends CmsTemplates
	{
		static
			$TITLE  = 'Templates',
			$IDENT  = 'mailer.templates';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/page.png',
				'LARGE' => 'icon.large/page.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Templates',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'mailer.template';

		static
			$TYPE = 'mm.template';


		static function GET_Details()
		{


			parent::GET_Details();
		}

		static function Toolbar()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('TABBAR', array(
			    	'preview' => 'Preview',
					'html' => 'HTML',
				)
			);
		}

	}

?>