<?php

	// SettingsGeneral

	class SettingsGeneral extends AdminPage
	{
		static
			$TITLE = 'General';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'admin.settings';


		static
			$SETTINGS = Array();

		static function onLoad()
		{
			$input = new Input('general.website.name');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Website Name');

			$input = new Input('general.contact.name');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Administrator Name')
					->Details('Your contact name');

			$input = new Input('general.contact.email');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Administrator Email')
					->Details('Your default contact email');
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