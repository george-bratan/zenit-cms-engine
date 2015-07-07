<?php

	// AdminPage, to be extended

	class AdminPage extends Page
	{
        static
			$TITLE = 'AdminPage';

		// for use on menus
		static
			$ICON = Array(
				'SMALL' => '',
				'LARGE' => '',
			);

		static
			$HELP = FALSE;

		// access required to access this section
		static
			$AUTH = '';

		// if TRUE forwards request to first child in this section
		static
			$FORWARD = FALSE;


        static function onBeforeRoute()
        {
        	if (!Auth::LoggedIn())
			{
				Application::Reroute(Admin::$ROOT . '/login');
				return FALSE;
			}

        	// test if we have a default handler
        	if (static::$FORWARD) {
        		//
        		$DEFAULT = Admin::GetDefault( get_called_class() );

        		if ($DEFAULT) {
	        		Application::EmulateRequest(Request::$METHOD.' '.($DEFAULT), Request::$Params);
					Application::Run();
				}
				else {
					Application::Error(500, 'No default controller found for "'.get_called_class().'"');
				}

				return FALSE;
        	}

			// test access
			if (!Auth::Grant(static::$AUTH))
			{
				Application::Error(403);
				return FALSE;
			}
        }

        static function onAfterRoute()
        {
        	//
        }

		static function onLoad()
		{
			// init when class is included in current scope
		}

		// call handler (class method) if available
		static function CallHandler( $default )
		{
			$class = get_called_class();

			$handler = isset(Request::$Params['URL']['handler']) ? Request::$Params['URL']['handler'] : $default;

			// check permissions
			if (isset(static::$PERMISSION)) {
				//
				if (isset(static::$PERMISSION[ $handler ])) {
					//
					if (!Auth::Grant(static::$AUTH .'.'. $handler )) {
						//
						Application::Error(403);
						return;
					}
				}
			}

			//
			$func = Request::$METHOD . '_' . $handler;

			if (in_array(strtolower($func), array_map('strtolower', get_class_methods( $class )))) {
				//
				return call_user_func(array($class, $func));
			}

			// no handler found
			Application::Error(404);
		}


		static function Get()
		{
			static::CallHandler($default = 'default');
		}

		static function Post()
		{
			static::CallHandler($default = 'default');
		}

		static function GET_Default()
		{
			static::Wrapper();
		}

		static function POST_Default()
		{
			static::Wrapper();
		}

		static function Wrapper($columns = 'one')
		{
			$class = get_called_class();

			UI::nset('MODULE', Admin::Module( $class ));
			UI::nset('SECTION', Admin::Section( $class ));

			UI::nset('SECTIONS', Admin::Sections( $class ));
			UI::nset('HELP', Admin::Help( $class ));

			UI::set('CONTENT', UI::Render('admin/wrapper.inner.'.$columns.'.php'));

			UI::Serve('admin/wrapper.outer.php');
		}

		static function Popup()
		{
			//
			if (Application::$ERROR) {
				UI::set('TITLE', Application::$ERROR['title']);
				UI::set('CONTENT', Application::$ERROR['text']);
				UI::set('CONTENT', UI::Render('admin/.shared.message.php'));
			}

			UI::Serve('admin/wrapper.popup.php');
		}

		static function Popout()
		{
			//
			if (Application::$ERROR) {
				UI::set('TITLE', Application::$ERROR['title']);
				UI::set('CONTENT', Application::$ERROR['text']);
				UI::set('CONTENT', UI::Render('admin/.shared.message.php'));
			}

			UI::Serve('admin/wrapper.popout.php');
		}

		static function Error( $error )
		{
			UI::set('TITLE', 'Error');
			UI::set('MESSAGE.ERROR', $error);

			UI::set('CONTENT', UI::Render('admin/.shared.message.php'));

			static::Popup();
		}

		static function GET_Help()
		{
			if (!static::$HELP) {
				//
				return FALSE;
			}

			UI::set('TITLE', 'Help Context: ' . static::$TITLE);
			UI::set('CONTENT', UI::Render('admin/help.' . static::$HELP . '.php'));

			static::Popup();
		}

		static function Notification()
		{
			return 0;
		}

		static function DataFeed()
		{
			return array();
		}

		static function HtmlFeed()
		{
			return array();
		}

		static function Timeline()
		{
			return array();
		}

		static function RecipientFeed()
		{
			return array();
		}

	}

?>