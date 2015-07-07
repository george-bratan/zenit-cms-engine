<?php

	// CmsSettings

	class CmsSettings extends AdminPage
	{
		static
			$TITLE = 'Settings';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'cms.settings';

		static
			$SETTINGS = array();


		static function onLoad()
		{
			Session::Set('ANALYTICS.access.token', Model::Settings('cms.analytics.token')->value);
			Session::Set('ANALYTICS.access.secret', Model::Settings('cms.analytics.secret')->value);

			$pages = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM cms_templates WHERE type = 'cms.page' AND status > -1 AND version > 0 ORDER BY name ASC)");

			$input = new Input('cms.index');
			self::$SETTINGS[] = $input->Type( Input::F_SELECT )
					->Options( $pages )
					->Title( 'Home Page' );

			$input = new Input('cms.notfound');
			self::$SETTINGS[] = $input->Type( Input::F_SELECT )
					->Options( $pages )
					->Title( 'Page Not Found (404)' );

			$input = new Input('cms.closed');
			self::$SETTINGS[] = $input->Type( Input::F_SELECT )
					->Options( $pages )
					->Title( 'Site is Closed / Maintenance' );

		}

		static function Get()
		{
			static::CallHandler( $default = 'settings' );
		}

		static function Post()
		{
			static::CallHandler( $default = 'settings' );
		}

		static function GET_Status()
		{
			$STATUS = Model::Settings('cms.status')->value;

			if ($STATUS == 'OPEN') {
				//
				UI::set('TARGET', Request::$URL . '/close');
				UI::set('CONTENT', 'Are you sure you want to <strong>CLOSE</strong> the public website ?<br><br>Your visitors will not be able to browse your website anymore.');
			}
			else {
				//
				UI::set('TARGET', Request::$URL . '/open');
				UI::set('CONTENT', 'Are you sure you want to <strong>OPEN</strong> the public website ?<br><br>Your visitors will be able to browse your website.');
			}

			UI::set('TITLE', 'Confirmation Required');
			UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

			parent::Popup();
		}

		static function POST_Open()
		{
			$STATUS = Model::Settings('cms.status');
			$STATUS->name = 'cms.status';
			$STATUS->value = 'OPEN';

			$STATUS->save();
		}

		static function POST_Close()
		{
			$STATUS = Model::Settings('cms.status');
			$STATUS->name = 'cms.status';
			$STATUS->value = 'CLOSED';

			$STATUS->save();
		}

		static function GET_Settings()
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
			UI::set('STATUS', Model::Settings('cms.status')->value);

			UI::set('SERVICE', Array(
				'id' => 'analytics',
				'name' => 'Google Analytics',
			));

			if (Session::GET('ANALYTICS.access.token')) {
				//
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
					Session::Get('ANALYTICS.access.token'), Session::Get('ANALYTICS.access.secret'));

				$user = $api->get('https://www.google.com/analytics/feeds/accounts/default?v=2&alt=json');
				$user = json_decode( $user, $assoc = TRUE );

				if (isset($user->error)) {
					//
					UI::set('SERVICE', Array(
						'id' => 'analytics',
						'name' => 'Google Analytics',
						'token' => $user->error,
					));
				}
				else {
					//
					$segments = array();
					foreach ($user['feed']['dxp$segment'] as $segment) {
						//
						$segments[ $segment['dxp$definition']['$t'] ] = $segment['name'];
					}

					$feeds = array();
					foreach ($user['feed']['entry'] as $entry) {
						//
						//$feeds[ $entry['id']['$t'] ] = $entry['title']['$t'];
						$feeds[ $entry['dxp$tableId']['$t'] ] = $entry['title']['$t'];
					}

					UI::set('SERVICE', Array(
						'id' => 'analytics',
						'name' => 'Google Analytics',
						'account' => substr($user['feed']['id']['$t'], strrpos($user['feed']['id']['$t'], '/')+1),
						'profile' => 'http://google.com/analytics/',
						'token' => Session::get('ANALYTICS.access.token'),
						'feeds' => $feeds,
						'segments' => $segments,
						'feed' => Model::Settings('cms.analytics.feed')->value,
					));
				}
			}

			UI::set('CONTENT', UI::Render('admin/cms.settings.php'));

			parent::Get();
		}

		static function POST_Settings()
		{
			$params = Request::POST($context = 'SETTINGS');

			foreach (self::$SETTINGS as $input) {
				//
				$model = Model::Settings( $input->name );

				$model->name = $input->name;
				$model->value = $params[ $input->name ];

				$model->save();
			}

			if (isset($params[ 'cms.analytics.feed' ])) {
				//
				$model = Model::Settings( 'cms.analytics.feed' );

				$model->name = 'cms.analytics.feed';
				$model->value = $params[ 'cms.analytics.feed' ];

				$model->save();
			}

			UI::set('MESSAGE.SUCCESS', 'Your settings have been saved!');

			self::GET_Settings();
		}

		static function GET_Analytics()
		{
			if (Request::URL('id') == 'signin') {
				//
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET') );

				// Requesting authentication tokens, the parameter is the URL we will be redirected to
				$token = $api->getRequestToken( 'http://' . Request::$HOST . Request::$URL . '/analytics/token',
					array(
						'xoauth_displayname' => 'Zenit CMS',
						'scope' => 'https://www.google.com/analytics/feeds/',
						'max_auth_age' => '0', )
					);

				Session::Set('ANALYTICS.request.token', $token['oauth_token']);
				Session::Set('ANALYTICS.request.secret', $token['oauth_token_secret']);

				if($api->http_code == 200){
				    //
				    // Generate AUTH URL
				    $url = $api->getAuthorizeURL( Session::Get('ANALYTICS.request.token') ); //$sign_in_with_twitter = FALSE, $force_login = TRUE

				    Request::Redirect( $url );
				}
				else {
					//
				    self::Error('Something Broken Error Happen ('.$api->http_code.')');
				    return;
				}
			}

			if (Request::URL('id') == 'token') {
				//
				if (!Request::GET('oauth_verifier')) {
					print 'No OAUTH_VERIFIER received';
				}

				Session::Set('ANALYTICS.request.verifier', Request::GET('oauth_verifier'));

				// TwitterOAuth instance, with two new parameters we got in twitter_login.php
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
					Session::Get('ANALYTICS.request.token'), Session::Get('ANALYTICS.request.secret') );

				// Let's request the access token
				$token = $api->getAccessToken( Session::Get('ANALYTICS.request.verifier') );

				// Save it in a session var
				Session::Set('ANALYTICS.access.token', $token['oauth_token']);
				Session::Set('ANALYTICS.access.secret', $token['oauth_token_secret']);

				$model = Model::Settings( 'cms.analytics.token' );
				$model->name = 'cms.analytics.token';
				$model->value = Session::Get('ANALYTICS.access.token');
				$model->save();

				$model = Model::Settings( 'cms.analytics.secret' );
				$model->name = 'cms.analytics.secret';
				$model->value = Session::Get('ANALYTICS.access.secret');
				$model->save();

				print '<script type="text/javascript">window.opener.$.fancybox.showActivity(); window.opener.location = window.opener.location; window.close(); </script>';
				return;
			}

			if (Request::URL('id') == 'signout') {
				//
				UI::set('TARGET', Request::$URL . '/analytics/signout');
				UI::set('CONTENT', 'Are you sure you want to sign out of Google Analytics ?<br />'.
					'You will no longer be able to use any Google Analytics related integrations.' );

				UI::set('TITLE', 'Sign out of Google Analytics');
				UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

				parent::Popup();
				return;
			}

			self::Error('Something Broken Error Happen');
		}

		static function POST_Analytics()
		{
			if (Request::URL('id') == 'signout') {
				//
				Session::Clear('ANALYTICS');

				$model = Model::Settings( 'cms.analytics.token' );
				$model->name = 'cms.analytics.token';
				$model->value = '';
				$model->save();

				$model = Model::Settings( 'cms.analytics.secret' );
				$model->name = 'cms.analytics.secret';
				$model->value = '';
				$model->save();
			}
		}

		static function Timeline( $feed = NULL )
		{
			if (!Session::GET('ANALYTICS.access.token')) {
				//
				return array();
			}

			if (!$feed) {
				return array(
					'cms.views' => 'Google Analytics: Page Views',
					'cms.visits' => 'Google Analytics: Unique Visitors',
				);
			}

			$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
				Session::Get('ANALYTICS.access.token'), Session::Get('ANALYTICS.access.secret'));

			if ($feed == 'cms.views' || $feed == 'cms.visits') {
				//
				$result = $api->get('https://www.google.com/analytics/feeds/data?v=2&alt=json',
					array(
						'ids' => Model::Settings( 'cms.analytics.feed' )->value,
						'dimensions' => 'ga:date',
						'metrics' => 'ga:visits,ga:pageviews',
						'sort' => 'ga:date',
						//'filters' => 'ga:name operator expression',
						//'&segment' => 'gaid::10 OR dynamic::ga:medium==referral',
						'start-date' => date('Y-m-d', strtotime('-10 days')),
						'end-date' => date('Y-m-d', strtotime('today')),
						'start-index' => '1',
						'max-results' => '100',
					));

				$result = json_decode( $result, $assoc = TRUE );

				$visits = $views = array();
				if (isset($result['feed']['entry'])) {
					//
					foreach ($result['feed']['entry'] as $entry) {
						//
						$date = date('Y-m-d', strtotime($entry['dxp$dimension'][0]['value']));
						$visit = $entry['dxp$metric'][0]['value'];
						$view = $entry['dxp$metric'][1]['value'];

						$visits[ $date ] = $visit;
						$views[ $date ] = $view;
					}
				}
			}

			if ($feed == 'cms.views') {
				//
				return count($views) ? $views : FALSE;
			}

			if ($feed == 'cms.visits') {
				//
				return count($visits) ? $visits : FALSE;
			}

			return parent::Timeline();
		}

	}

?>