<?php

	// AdminBase

	class AdminBase
	{
		static
			$ROOT = '';

		// used for Main Menu, Module and Section Structure
		static
			$SITEMAP = Array();

		// used for Admin routing
		static
			$ROUTES = Array();


		static function onLoad()
		{
			//print 'adminbase/';
		}

		static function Routes($url)
		{
			$url = rtrim($url, '/');

			Admin::$ROOT = $url;

			Router::Route("GET {$url}/",
				function() {
					Application::Reroute(Admin::$ROOT.'/index');
				}
			);

			foreach (static::$ROUTES as $path => $class) {

				if (substr($path, strlen($path)-1, 1) == '@') {
					//
					$path = rtrim($path, '/@');

					Router::Map( $url.$path, $class );
					Router::Map( $url.$path.'/@handler', $class );
					Router::Map( $url.$path.'/@handler/@id', $class );
				}
				else {
					//
					$path = rtrim($path, '/');

					Router::Map( $url.$path, $class );
				}
			}
		}

		// return relative route for given admin controller
		static function GetRoute($controller)
		{
			foreach (static::$ROUTES as $path => $class) {
				//
				if ($class == $controller) {
					//
					return Admin::$ROOT . rtrim($path, '/@');
				}
			}

			return FALSE;
		}

		// returns default controller in given section
		static function GetDefault($controller)
		{
			foreach (static::$SITEMAP as $module => $sections) {
				//
				if ($module == $controller && is_array($sections)) {
					// found the section we're looking for
					foreach ($sections as $section) {
						// return the first child class that is permitted
						if (Auth::Grant($section::$AUTH)) {
							//
							return Admin::GetRoute($section);
						}
					}
				}
			}

			// return itself, this would cause an endless reload loop
			//return Admin::GetRoute($controller);

			return FALSE;
		}

		static function Menu()
		{
			$MENU = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				//module may be key or value, depending on whether it has children or not
				$module = is_array($sections) ? $module : $sections;

				$granted = FALSE;
				if ($module::$FORWARD) {
					if (count(self::Sections($module))) {
						$granted = true;
					}
				}
				else {
					$granted = Auth::Grant($module::$AUTH);
				}

				if ($granted) {
					//
					$route = Admin::GetRoute($module);

					if ($route) {
						//
						$MENU[ $route ] = Array(
							'url' => $route,
							'icon' => $module::$ICON['SMALL'],
							'title' => $module::$TITLE,
						);
					}
				}
			}

			return $MENU;
		}

		static function Help($controller)
		{
			if ($controller::$HELP) {
				//
				$route = static::$ROOT .'/help/'. $controller::$HELP;

				return Array(
					'url' => $route,
					'icon' => 'icon.large/help.png',
					'title' => 'Help',
				);
			}

			foreach (static::$SITEMAP as $module => $sections) {
				// skip this entry if not a module => sections
				if (!is_array($sections)) {
					continue;
				}

				if (in_array($controller, $sections)) {
					// found the section we need to list
					if ($module::$HELP) {
						//
						$route = static::$ROOT .'/help/'. $module::$HELP;

						return Array(
							'url' => $route,
							'icon' => 'icon.large/help.png',
							'title' => 'Help',
						);
					}
				}
			}

			return FALSE;
		}

		// list all sections of this module
		static function Sections($controller)
		{
			$SECTIONS = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				// skip this entry if not a module => sections
				if (!is_array($sections)) {
					continue;
				}

				if ($module == $controller || in_array($controller, $sections)) {
					// found the section we need to list
					foreach ($sections as $section) {
						//
						if (Auth::Grant($section::$AUTH)) {
							//
							$route = Admin::GetRoute($section);

							if ($route) {
								//
								$SECTIONS[ $route ] = Array(
									'url' => $route,
									'icon' => $section::$ICON['LARGE'],
									'title' => $section::$TITLE,
									'notification' => $section::Notification(),
								);
							}
						}
					}
				}
			}

			return $SECTIONS;
		}

		// list all sections that we're given as arguments
		static function ListSections(/* ... */)
		{
			$SECTIONS = Array();

			$controllers = func_get_args();
			foreach ($controllers as $controller) {
				//
				if (Auth::Grant($controller::$AUTH)) {
					//
					$route = Admin::GetRoute($controller);

					if ($route) {
						//
						$SECTIONS[ $route ] = Array(
							'url' => $route,
							'icon' => $controller::$ICON['LARGE'],
							'title' => $controller::$TITLE,
							'notification' => $controller::Notification(),
						);
					}
				}
			}

			return $SECTIONS;
		}

		// return module title
		static function Module($controller)
		{
			foreach (static::$SITEMAP as $module => $sections) {
				//
				if (is_array($sections)) {
					//
					if (in_array($controller, $sections)) {
						//
						return $module::$TITLE;
					}
				}
			}

			// return own title
			return $controller::$TITLE;
		}

		// return section title
		static function Section($controller)
		{
			return $controller::$TITLE;
		}

		static function DataFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				//
				$controllers = is_array($sections) ? $sections : array($sections);

				foreach ($controllers as $controller) {
					//
					// either user is not logged-in (public visitor) or he is allowed
					if (!Auth::LoggedIn() || Auth::Grant($controller::$AUTH)) {
						//
						$feeds = $controller::DataFeed();

						if ($feed) {
							if (isset($feeds[ $feed ])) {
								//
								return $controller::DataFeed( $feed, $filters );
							}
						}

						foreach ($feeds as $id => $name) {
							//
							$FEEDS[ $id ] = $name;
						}
					}
				}
			}

			return $FEEDS;
		}

		static function DataFeedEx( $feed = NULL, $filters = NULL )
		{
			$FEEDS = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				//
				$controllers = is_array($sections) ? $sections : array($sections);

				foreach ($controllers as $controller) {
					//
					// either user is not logged-in (public visitor) or he is allowed
					if (!Auth::LoggedIn() || Auth::Grant($controller::$AUTH)) {
						//
						$feeds = $controller::DataFeed();

						if ($feed) {
							if (isset($feeds[ $feed ])) {
								//
								return $controller::DataFeed( $feed, $filters );
							}
						}

						foreach ($feeds as $id => $name) {
							//
							$FEEDS[ $controller::$TITLE ][ $id ] = $name;
						}
					}
				}
			}

			return $FEEDS;
		}

		static function HtmlFeed( $feed = NULL )
		{
			$FEEDS = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				//
				$controllers = is_array($sections) ? $sections : array($sections);

				foreach ($controllers as $controller) {
					//
					// either user is not logged-in (public visitor) or he is allowed
					if (!Auth::LoggedIn() || Auth::Grant($controller::$AUTH)) {
						//
						$feeds = $controller::HtmlFeed();

						if ($feed) {
							if (isset($feeds[ $feed ])) {
								//
								return $controller::HtmlFeed( $feed );
							}
						}

						foreach ($feeds as $id => $name) {
							//
							$FEEDS[ $controller::$TITLE ][ $id ] = $name;
						}
					}
				}
			}

			return $FEEDS;
		}

		static function RecipientFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				//
				$controllers = is_array($sections) ? $sections : array($sections);

				foreach ($controllers as $controller) {
					//
					// either user is not logged-in (public visitor) or he is allowed
					if (!Auth::LoggedIn() || Auth::Grant($controller::$AUTH)) {
						//
						$feeds = $controller::RecipientFeed();

						if ($feed) {
							if (isset($feeds[ $feed ])) {
								//
								return $controller::RecipientFeed( $feed, $filters );
							}
						}

						foreach ($feeds as $id => $name) {
							//
							$FEEDS[ $controller::$TITLE ][ $id ] = $name;
						}
					}
				}
			}

			return $FEEDS;
		}

		static function Timeline( $feed = NULL )
		{
			$FEEDS = Array();

			foreach (static::$SITEMAP as $module => $sections) {
				//
				$controllers = is_array($sections) ? $sections : array($sections);

				foreach ($controllers as $controller) {
					//
					if (Auth::Grant($controller::$AUTH)) {
						//
						$feeds = $controller::Timeline();

						if ($feed) {
							if (isset($feeds[ $feed ])) {
								//
								return $controller::Timeline( $feed );
							}
						}

						foreach ($feeds as $id => $name) {
							//
							$FEEDS[ $id ] = $name;
						}
					}
				}
			}

			return $FEEDS;
		}

	}

?>