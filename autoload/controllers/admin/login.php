<?php

	// Login

	class Login extends Page
	{

		static function onLoad()
		{
			$permission = array();

			foreach (Admin::$SITEMAP as $key => $value)
			{
				//class may be key or value, depending on whether it has children or not
				$class = (is_array($value)) ? $key : $value;

				if ($class::$AUTH) {
					//
					$permission[] = $class::$AUTH;

					if (isset($class::$PERMISSION)) {
						//
						foreach ($class::$PERMISSION as $key => $title) {
							//
							if ($key)
								$permission[] = $class::$AUTH .'.'. $key;
						}
					}
				}


				if (is_array($value)) {
					//
					foreach ($value as $subclass) {
						//
						if ($subclass::$AUTH) {
							//
							$permission[] = $subclass::$AUTH;

							if (isset($subclass::$PERMISSION)) {
								//
								foreach ($subclass::$PERMISSION as $key => $title) {
									//
									if ($key)
										$permission[] = $subclass::$AUTH .'.'. $key;
								}
							}
						}
					}
				}
			}


			$user = Model::User();
			if ($user->count() == 0)
			{
				$user->pass = md5( 'admin:admin' ); //'admin';
				$user->email = 'admin';
				$user->firstname = 'Administrator';
				$user->lastname = '';
				$user->token = implode('|', $permission);
				$user->idgroup = 0;
				$user->status = 1;

				$user->save();
			}
		}

		static function Get()
		{
			UI::set('CONTENT', UI::Render('admin/login.php'));

			UI::Serve('admin/wrapper.login.php');
		}

		static function Post()
		{
			if (Auth::Login( Request::POST('username'), Request::POST('password') ))
			{
				Application::Reroute(Admin::$ROOT.'/index');
			}
			else
			{
				UI::set('ERROR', Lang::ERR_LoginInvalid);
				self::Get();
			}
		}

	}

?>
