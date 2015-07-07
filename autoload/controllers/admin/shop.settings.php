<?php

	// ShopSettings

	class ShopSettings extends AdminPage
	{
		static
			$TITLE = 'Settings';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'shop.settings';

		static
			$SETTINGS = array();


		static function onLoad()
		{
			$input = new Input('shop.currency');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Currency Symbol');

			$input = new Input('shop.paypal.user');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Paypal Username');

			$input = new Input('shop.paypal.pass');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Paypal Password');

			$input = new Input('shop.paypal.auth');
			self::$SETTINGS[] = $input
					->Type(Input::F_TEXT)
					->Title('Paypal Signature');

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