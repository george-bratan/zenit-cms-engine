<?php

	// MailerSettings

	class MailerSettings extends AdminPage
	{
		static
			$TITLE = 'Settings';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'mailer.settings';

		static
			$SETTINGS = array();


		static function onLoad()
		{
			$setting = new Input('mailer.host');
			static::$SETTINGS[] = $setting
					->Type(Input::F_TEXT)
					->Title('SMTP Server Host or IP')
					->Details('');

			$setting = new Input('mailer.port');
			static::$SETTINGS[] = $setting
					->Type(Input::F_TEXT)
					->Title('SMTP Server Port')
					->Details('Leave empty if unknown');

			$setting = new Input('mailer.ssl');
			static::$SETTINGS[] = $setting
					->Type(Input::F_RADIOGROUP)
					->Title('Use Secure Connection Protocol')
					->Details('Connect to the SMTP server over SSL / TLS')
					->Options(array("" => "None", "ssl" => "SSL", "tls" => "TLS"));

			$setting = new Input('mailer.smtp.auth');
			static::$SETTINGS[] = $setting
					->Type(Input::F_BOOL)
					->Title('Use SMTP Authentication')
					->Details('If selected, you must enter the SMTP Username and Password below');

			$setting = new Input('mailer.smtp.user');
			static::$SETTINGS[] = $setting
					->Type(Input::F_TEXT)
					->Title('Username')
					->Details('');

			$setting = new Input('mailer.smtp.pass');
			static::$SETTINGS[] = $setting
					->Type(Input::F_PASSWORD)
					->Title('Password')
					->Details('');
		}

		static function Get()
		{
			$settings = Array();
			// populate settings
			foreach (self::$SETTINGS as $input) {
				//
				$input->Value( Model::Settings( $input->name )->value );
				$input->Context('SETTINGS');

				$settings[ $input->name ] = $input; //->Export();
			}

			UI::set('SETTINGS', $settings);

			UI::set('CONTENT', UI::Render('admin/settings.general.php'));

			parent::Get();
		}

		static function Post()
		{
			$params = Request::POST($context = 'SETTINGS');

			foreach (self::$SETTINGS as $input) {
				//
				$model = Model::Settings( $input->name );

				$model->name = $input->name;
				$model->value = $params[ $input->name ];

				$model->save();
			}

			self::Test();

			UI::set('MESSAGE.SUCCESS', 'Your settings have been saved!');

			parent::Post();
		}

		static function Test()
		{
			$to = Session::Get('ACCOUNT.EMAIL');
			$subject = Model::Settings( 'general.website.name' )->value . ' Test Message';
			$message = 'CONGRATS!<br>You have successfully configured your application to send email messages.';

			if ( Mail::Send($to, $subject, $message) ) {
				//
				UI::set('MESSAGE.WARNING', 'A confirmation email has been sent to your address: ' . Session::Get('ACCOUNT.EMAIL'));
			}
			else {
				//
				UI::set('MESSAGE.ERROR', Mail::$ERROR ? Mail::$ERROR : 'Mail Server Error.');
			}

		}

	}

?>