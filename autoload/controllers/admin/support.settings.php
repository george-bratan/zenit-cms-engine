<?php

	// SupportSettings

	class SupportSettings extends AdminPage
	{
		static
			$TITLE = 'Settings';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'support.settings';

		static
			$SETTINGS = array();


		static function onLoad()
		{
			/*
			$input = new Input('support.currency');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Currency Symbol');

			$input = new Input('support.invoice.prefix');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Invoice Series Prefix');

			$input = new Input('support.invoice.index');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Invoice Current Index');
			*/
		}

		static function Get()
		{
			$model = Model::SupportUser( Session::Get('SUPPORT.ID') );

			$settings = Array();

			$input = new Input('notifications');
			$settings[] = $input->Type(Input::F_BOOL)
					->Context('SETTINGS')
					->Title('Email Notifications')
					->Details('Send me an email when tickets have new content')
					->Value($model->notifications ? TRUE : FALSE);

			UI::set('SETTINGS', $settings);

			UI::set('CONTENT', UI::Render('admin/settings.general.php'));

			parent::Get();
		}

		static function Post()
		{
			$model = Model::SupportUser( Session::Get('SUPPORT.ID') );

			$model->notifications = (Request::POST('SETTINGS.notifications') == 'true') ? 1 : 0;

			$model->save();

			UI::set('MESSAGE.SUCCESS', 'Your settings have been saved!');

			parent::Post();
		}

	}

?>