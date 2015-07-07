<?php

	// SettingsLicense

	class SettingsLicense extends AdminPage
	{
		static
			$TITLE = 'License';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/lock.png',
				'LARGE' => 'icon.large/lock.png',
			);

		static
			$AUTH = 'admin.license';


		static function Get()
		{
			$settings = Array();

			$input = new Input('admin.license');
			$input->Type(Input::F_LONGTEXT)
					->Title('License')
					->Context('SETTINGS')
					->Details('Copy/Paste the license file contents<br>or upload the license file below');
			$settings[ $input->name ] = $input; //->Export();

			$input = new Input('admin.license.file');
			$input->Type(Input::F_FILE)
					->Title('License File')
					->Context('SETTINGS');
			$settings[ $input->name ] = $input; //->Export();

			UI::set('SETTINGS', $settings);

			UI::set('LICENSE', self::License());

			UI::set('CONTENT',
				UI::Render('admin/settings.license.php').
				UI::Render('admin/settings.general.php'));

			parent::Get();
		}

		static function License()
		{
			$encrypted = Model::Settings( 'admin.license' )->value;

			if (!$encrypted) {
				//
				return FALSE;
			}

			$RSA = new RSA();
			$decrypted = $RSA->decrypt($encrypted, Conf::get('LICENSE:KEY'));

			$license = explode("\n", $decrypted);

			if (count($license) !== 4) {
				return FALSE;
			}

			if (trim($license[3]) != '.') {
				//
				return FALSE;
			}

			$license = Array(
				'company' => $license[0],
				'issued' => strtotime($license[1]),
				'duration' => $license[2],
				'expires' => strtotime(str_replace("\r", '', $license[1] .' +'. $license[2])),
			);

			return $license;
		}

		static function Post()
		{
			$params = Request::POST($context = 'SETTINGS');

			if (!$params[ 'admin.license' ]) {
				//
				$files = Request::FILES($context = 'SETTINGS');

				if ($files['tmp_name']['admin.license.file']) {
					//
					if ( file_exists($files['tmp_name']['admin.license.file']) ) {
						//
						$params[ 'admin.license' ] = file_get_contents( $files['tmp_name']['admin.license.file'] );
					}
				}
			}

			$model = Model::Settings( 'admin.license' );

			$model->name = 'admin.license';
			$model->value = $params[ 'admin.license' ];

			$model->save();

			UI::set('MESSAGE.SUCCESS', 'Your settings have been saved!');

			parent::Post();
		}

	}

?>