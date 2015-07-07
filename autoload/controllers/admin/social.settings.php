<?php

	// SocialSettings

	class SocialSettings extends AdminPage
	{
		static
			$TITLE = 'Settings';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$AUTH = 'social.settings';


		static function onLoad()
		{
			Session::Set('TWITTER.access.token', Model::Settings('social.twitter.token')->value);
			Session::Set('TWITTER.access.secret', Model::Settings('social.twitter.secret')->value);

			Session::Set('FACEBOOK.access.token', Model::Settings('social.facebook.token')->value);
			Session::Set('FACEBOOK.page', Model::Settings('social.facebook.page')->value);

			Session::Set('YOUTUBE.access.token', Model::Settings('social.youtube.token')->value);
			Session::Set('YOUTUBE.access.secret', Model::Settings('social.youtube.secret')->value);
		}

		static function GET()
		{
			static::CallHandler( $default = 'status' );
		}

		static function POST()
		{
			static::GET();
		}

		static function GET_Status()
		{
			UI::set('SOCIAL.facebook', Array(
				'name' => 'Facebook',
			));

			UI::set('SOCIAL.twitter', Array(
				'name' => 'Twitter',
			));

			UI::set('SOCIAL.youtube', Array(
				'name' => 'YouTube!',
			));

			if (Session::Get('FACEBOOK.access.token')) {
				//
				$api = SocialFacebookApi::Init( Conf::get('SOCIAL:FACEBOOK:KEY'), Conf::get('SOCIAL:FACEBOOK:SECRET') );

				$user = $api->api('me');

				if (isset($user->error)) {
					//
					UI::set('SOCIAL.facebook', Array(
						'name' => 'Facebook',
						'token' => $user->error,
					));
				}
				else {
					//
					$pages = array(0 => 'Profile Wall');
					$accounts = $api->api('me/accounts');
					foreach ($accounts['data'] as $account) {
						//
						$pages[ $account['id'] ] = ' - ' . $account['category'] . ': ' . $account['name'];
					}

					UI::set('SOCIAL.facebook', Array(
						'name' => 'Facebook',
						'account' => $user['name'],
						'profile' => $user['link'],
						'token' => Session::Get('FACEBOOK.access.token'),
						'options' => $pages,
						'value' => Session::Get('FACEBOOK.page'),
					));
				}
			}

			if (Session::GET('TWITTER.access.token')) {
				//
				$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
					Session::Get('TWITTER.access.token'), Session::Get('TWITTER.access.secret'));

				$user = $api->get('account/verify_credentials');

				if (isset($user->error)) {
					//
					UI::set('SOCIAL.twitter', Array(
						'name' => 'Twitter',
						'token' => $user->error,
					));
				}
				else {
					//
					UI::set('SOCIAL.twitter', Array(
						'name' => 'Twitter',
						'account' => $user->name,
						'profile' => 'http://twitter.com/' .$user->screen_name,
						'token' => Session::get('TWITTER.access.token'),
					));
				}
			}

			if (Session::GET('YOUTUBE.access.token')) {
				//
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
					Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

				$user = $api->get('https://gdata.youtube.com/feeds/api/users/default?v=2&alt=json');
				$user = json_decode( $user, $assoc = TRUE );

				if (isset($user->error)) {
					//
					UI::set('SOCIAL.youtube', Array(
						'name' => 'YouTube!',
						'token' => $user->error,
					));
				}
				else {
					//
					UI::set('SOCIAL.youtube', Array(
						'name' => 'YouTube!',
						'account' => $user['entry']['yt$username']['$t'],
						'profile' => 'https://www.youtube.com/profile?user=' . $user['entry']['yt$username']['$t'],
						'token' => Session::get('YOUTUBE.access.token'),
					));
				}
			}

			UI::set('CONTENT', UI::Render('admin/social.settings.php'));

			parent::Wrapper();
		}

		static function GET_Twitter()
		{
			if (Request::URL('id') == 'signin') {
				//
				$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET') );

				// Requesting authentication tokens, the parameter is the URL we will be redirected to
				$token = $api->getRequestToken( 'http://' . Request::$HOST . Request::$URL . '/twitter/token' );

				Session::Set('TWITTER.request.token', $token['oauth_token']);
				Session::Set('TWITTER.request.secret', $token['oauth_token_secret']);

				if($api->http_code == 200){
				    //
				    // Generate AUTH URL
				    $url = $api->getAuthorizeURL( Session::Get('TWITTER.request.token'), $sign_in_with_twitter = FALSE, $force_login = TRUE );

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

				Session::Set('TWITTER.request.verifier', Request::GET('oauth_verifier'));

				// TwitterOAuth instance, with two new parameters we got in twitter_login.php
				$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
					Session::Get('TWITTER.request.token'), Session::Get('TWITTER.request.secret') );

				// Let's request the access token
				$token = $api->getAccessToken( Session::Get('TWITTER.request.verifier') );

				// Save it in a session var
				Session::Set('TWITTER.access.token', $token['oauth_token']);
				Session::Set('TWITTER.access.secret', $token['oauth_token_secret']);

				$model = Model::Settings( 'social.twitter.token' );
				$model->name = 'social.twitter.token';
				$model->value = Session::Get('TWITTER.access.token');
				$model->save();

				$model = Model::Settings( 'social.twitter.secret' );
				$model->name = 'social.twitter.secret';
				$model->value = Session::Get('TWITTER.access.secret');
				$model->save();

				print '<script type="text/javascript">window.opener.$.fancybox.showActivity(); window.opener.location = window.opener.location; window.close(); </script>';
				return;
			}

			if (Request::URL('id') == 'signout') {
				//
				UI::set('TARGET', Request::$URL . '/twitter/signout');
				UI::set('CONTENT', 'Are you sure you want to sign out of Twitter ?<br />'.
					'You will no longer be able to use any Twitter related integrations.' );

				UI::set('TITLE', 'Sign out of Twitter');
				UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

				parent::Popup();
				return;
			}

			self::Error('Something Broken Error Happen');
		}

		static function POST_Twitter()
		{
			if (Request::URL('id') == 'signout') {
				//
				Session::Clear('TWITTER');

				$model = Model::Settings( 'social.twitter.token' );
				$model->name = 'social.twitter.token';
				$model->value = '';
				$model->save();

				$model = Model::Settings( 'social.twitter.secret' );
				$model->name = 'social.twitter.secret';
				$model->value = '';
				$model->save();
			}
		}

		static function GET_YouTube()
		{
			if (Request::URL('id') == 'signin') {
				//
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET') );

				// Requesting authentication tokens, the parameter is the URL we will be redirected to
				$token = $api->getRequestToken( 'http://' . Request::$HOST . Request::$URL . '/youtube/token',
					array( 'xoauth_displayname' => 'Zenit CMS', 'scope' => 'https://gdata.youtube.com' ) );

				Session::Set('YOUTUBE.request.token', $token['oauth_token']);
				Session::Set('YOUTUBE.request.secret', $token['oauth_token_secret']);

				if($api->http_code == 200){
				    //
				    // Generate AUTH URL
				    $url = $api->getAuthorizeURL( Session::Get('YOUTUBE.request.token') ); //$sign_in_with_twitter = FALSE, $force_login = TRUE

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

				Session::Set('YOUTUBE.request.verifier', Request::GET('oauth_verifier'));

				// TwitterOAuth instance, with two new parameters we got in twitter_login.php
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
					Session::Get('YOUTUBE.request.token'), Session::Get('YOUTUBE.request.secret') );

				// Let's request the access token
				$token = $api->getAccessToken( Session::Get('YOUTUBE.request.verifier') );

				// Save it in a session var
				Session::Set('YOUTUBE.access.token', $token['oauth_token']);
				Session::Set('YOUTUBE.access.secret', $token['oauth_token_secret']);

				$model = Model::Settings( 'social.youtube.token' );
				$model->name = 'social.youtube.token';
				$model->value = Session::Get('YOUTUBE.access.token');
				$model->save();

				$model = Model::Settings( 'social.youtube.secret' );
				$model->name = 'social.youtube.secret';
				$model->value = Session::Get('YOUTUBE.access.secret');
				$model->save();

				print '<script type="text/javascript">window.opener.$.fancybox.showActivity(); window.opener.location = window.opener.location; window.close(); </script>';
				return;
			}

			if (Request::URL('id') == 'signout') {
				//
				UI::set('TARGET', Request::$URL . '/youtube/signout');
				UI::set('CONTENT', 'Are you sure you want to sign out of YouTube ?<br />'.
					'You will no longer be able to use any YouTube related integrations.' );

				UI::set('TITLE', 'Sign out of YouTube');
				UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

				parent::Popup();
				return;
			}

			self::Error('Something Broken Error Happen');
		}

		static function POST_YouTube()
		{
			if (Request::URL('id') == 'signout') {
				//
				Session::Clear('YOUTUBE');

				$model = Model::Settings( 'social.youtube.token' );
				$model->name = 'social.youtube.token';
				$model->value = '';
				$model->save();

				$model = Model::Settings( 'social.youtube.secret' );
				$model->name = 'social.youtube.secret';
				$model->value = '';
				$model->save();
			}
		}

		static function GET_Facebook()
		{
			if (Request::URL('id') == 'signin') {
				//
				$api = SocialFacebookApi::Init( Conf::get('SOCIAL:FACEBOOK:KEY'), Conf::get('SOCIAL:FACEBOOK:SECRET') );

				$login = $api->getLoginUrl( array(
					'redirect_uri' => 'http://' . Request::$HOST . Request::$URL . '/facebook/token' ,
					'display' => 'popup',
					'scope' => 'offline_access, create_event, manage_pages, publish_stream, read_stream, read_requests, read_mailbox, manage_friendlists',
					'auth_type' => 'reauthenticate',
				) );

				Request::Redirect( $login );
			}

			if (Request::URL('id') == 'token') {
				//
				$api = SocialFacebookApi::Init( Conf::get('SOCIAL:FACEBOOK:KEY'), Conf::get('SOCIAL:FACEBOOK:SECRET') );

				Session::Set('FACEBOOK.access.token', $api->getAccessToken());

				$model = Model::Settings( 'social.facebook.token' );
				$model->name = 'social.facebook.token';
				$model->value = Session::Get('FACEBOOK.access.token');
				$model->save();

				print '<script type="text/javascript">window.opener.$.fancybox.showActivity(); window.opener.location = window.opener.location; window.close(); </script>';
				return;
			}

			if (Request::URL('id') == 'signout') {
				//
				UI::set('TARGET', Request::$URL . '/facebook/signout');
				UI::set('CONTENT', 'Are you sure you want to sign out of Facebook ?<br />'.
					'You will no longer be able to use any Facebook related integrations.' );

				UI::set('TITLE', 'Sign out of Facebook');
				UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

				parent::Popup();
				return;
			}

			self::Error('Something Broken Error Happen');
		}

		static function POST_Facebook()
		{
			if (Request::URL('id') == 'signout') {
				//
				Session::Clear('FACEBOOK');

				$model = Model::Settings( 'social.facebook.token' );
				$model->name = 'social.facebook.token';
				$model->value = '';
				$model->save();
			}

			if (Request::URL('id') == 'option') {
				//
				$model = Model::Settings( 'social.facebook.page' );
				$model->name = 'social.facebook.page';
				$model->value = Request::POST('VALUES.facebook.option');
				$model->save();
			}
		}

	}

?>