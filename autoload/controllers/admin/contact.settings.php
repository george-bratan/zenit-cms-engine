<?php

	// ContactSettings

	class ContactSettings extends AdminPage
	{
		static
			$TITLE = 'Settings';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'contact.settings';


		static
			$SETTINGS = Array();

		static function onLoad()
		{
			$input = new Input('contact.url');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Message Thread URL')
					->Details('This URL will be sent to the contact\'s email address when you update a message thread.<br />'.
						'Accepts the <strong>@uid</strong> URL code.<br />'.
						'Example: <strong>http://example.com/contact/@uid</strong>');
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

			UI::set('MESSAGE.SUCCESS', 'Your settings have been saved!');

			parent::Post();
		}

	}

?>