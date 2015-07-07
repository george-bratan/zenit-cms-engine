<?php

	// SupportUsers

	class SupportUsers extends AdminModule
	{
		static
			$TITLE  = 'Users',
			$IDENT  = 'support.users';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.png',
				'LARGE' => 'icon.large/user.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Users',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
				'access'	=> 'View Access Rights',
			);

		static
			$AUTH = 'support.users';


		static function Model($id = NULL)
		{
			return Model::SupportUser($id);
		}

		static function Where($model)
		{
			if (Session::Exists('SUPPORT.COMPANY.ID')) {
				$model->where('idcompany = ?', Session::Get('SUPPORT.COMPANY.ID'));
			}

			return $model;
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.access', Array(
					'rel' => 'modal',
					'handler' => 'access',
					'icon' => 'icon.small/key.png',
					'title' => 'Access Rights',
				));
			}

			parent::GET_List($page);
		}

		static function GET_Access()
		{
			if (!Request::URL('id'))
			{
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');

				UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

				parent::Popup();
				return;
			}


			$used = array();
			$permission = array();

			foreach (Admin::$SITEMAP as $key => $value)
			{
				//class may be key or value, depending on whether it has children or not
				$class = (is_array($value)) ? $key : $value;

				if ($class::$AUTH) {
					if (!in_array($class::$AUTH, $used)) {
						$permission[ $class::$TITLE ][ $class::$AUTH ] = $class::$TITLE;
						$used[] = $class::$AUTH;

						if (isset($class::$PERMISSION)) {
							//
							foreach ($class::$PERMISSION as $key => $title) {
								//
								if ($key)
									$permission[ $class::$TITLE ][ $class::$AUTH .'.'. $key ] = $title;
							}
						}
					}
				}

				// test if children are also a set of controllers

				if (is_array($value)) {
					//
					foreach ($value as $subclass) {
						//
						if ($subclass::$AUTH) {
							if (!in_array($subclass::$AUTH, $used)) {
								$permission[ $class::$TITLE ][ $subclass::$AUTH ] = $subclass::$TITLE;
								$used[] = $subclass::$AUTH;

								if (isset($subclass::$PERMISSION)) {
									//
									foreach ($subclass::$PERMISSION as $key => $title) {
										//
										if ($key)
											$permission[ $class::$TITLE.': '.$subclass::$TITLE ][ $subclass::$AUTH .'.'. $key ] = $title;
									}
								}
							}
						}
					}
				}
			}

			// USER
			$model = static::Model( intval( Request::URL('id') ) );

            // TOKEN
            $token = Util::split($model->token);

			// UI
			UI::set('USER', $model->record());

			UI::set('TITLE', 'Manage Permission Rights: '.$model->name);
			UI::set('ACCESS', $permission);
			UI::set('TOKEN', $token);


			UI::set('CONTENT', UI::Render('admin/zenit.permission.access.php'));

			parent::Popup();
		}

		static function POST_Save()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (!Request::URL('id')) {
				$model->defaults();
			}

			if (Request::POST('VALUES.pass')) {
				//
				if (Request::POST('VALUES.pass') != Request::POST('VALUES.passc')) {
					//
					UI::Set('MESSAGE.WARNING', 'The two passwords do not match');
				}

				if (strlen(Request::POST('VALUES.pass')) < 5) {
					//
					UI::Set('MESSAGE.WARNING', 'The minimum password length is 5 characters.');
				}
			}

			if (!Request::POST('VALUES.token') && !Request::POST('VALUES.email')) {
				//
				UI::Set('MESSAGE.WARNING', 'You must provide an email for login');
			}

			if (!Request::POST('VALUES.token') && Request::POST('VALUES.email') != $model->email && !Request::POST('VALUES.pass')) {
				//
				UI::Set('MESSAGE.WARNING', 'You must provide a password when changing your email.');
			}

			if (UI::Get('MESSAGE.WARNING')) {
				//
				UI::Serve('admin/.shared.alerts.php');
				return;
			}

			$params = Request::POST('VALUES');
			foreach (Request::POST('VALUES') as $field => $value) {
				//
				if ($field == 'token') {
					//
					$permission = Util::FilterChecked($value);
					$value = implode('|', $permission);
				}

				if (in_array($field, array_keys($model::$schema))) {
					//
					$model->$field = $value;
				}
			}

			if (Request::POST('VALUES.pass')) {
				//
				$model->pass = md5( $model->email .':'. $model->pass );
			}

			$model->save();
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			// we don't need no token around here, boy
			unset($fields['token']);


			/*
			// COMPANY
			$companies = array(0 => '-');
			$company = Model::SupportCompany();
			$company->execute();

			if ($company->found()) {
				$company->reset();
				while ($company->next()) {
					$companies[ $company->id ] = $company->name;
				}
			}

			$input = new Input('idcompany');
			$input->Type(Input::F_SELECT)->Title( $model::$schema['idcompany'] )->Context('VALUES')->Value(intval($model->idcompany))->Options($companies);
			$fields['idcompany'] = $input; //->Export();
			*/

			$input = new \Input('pass');
			$input->Type(\Input::F_PASSWORD)->Title($model::$schema['pass'])->Context('VALUES')->Align('right');
			$fields['pass'] = $input; //->Export();

			$input = new \Input('passc');
			$input->Type(\Input::F_PASSWORD)->Title('Retype Password')->Context('VALUES')->Align('right');
			$fields['passc'] = $input; //->Export();


			return $fields;
		}
	}

?>