<?php

	// SocialTwitter

	class SocialTwitter extends AdminPage
	{
		static
			$TITLE  = 'Twitter',
			$IDENT  = 'social.twitter';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/twitter.png',
				'LARGE' => 'icon.large/twitter.png',
			);

		static
			$AUTH = 'social.twitter';


		static function onLoad()
		{
			Session::Set('TWITTER.access.token', Model::Settings('social.twitter.token')->value);
			Session::Set('TWITTER.access.secret', Model::Settings('social.twitter.secret')->value);

			Session::Set('FACEBOOK.access.token', Model::Settings('social.facebook.token')->value);
		}

		static function GET()
		{
			static::CallHandler('list');
		}

		static function GET_List($page = 0)
		{
			$posts = array();

			if (Session::Get('TWITTER.access.token')) {
				//
				$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
					Session::Get('TWITTER.access.token'), Session::Get('TWITTER.access.secret'));

				$feed = $api->get('statuses/home_timeline');

				//print_r($feed); die();

				foreach ($feed as $post) {
					//
					$posts[] = array(
						'id' => $post->id_str,
						'user' => $post->user->screen_name,
						'name' => $post->user->name,
						'image' => $post->user->profile_image_url,
						'text' => $post->text,
						'date' => $post->created_at,
					);
				}
			}

			UI::nset('LIST', $posts);

			/*
			UI::nset('PAGES', array(
				'total' => $model->count(),
				'count' => $model->limit() ? ceil($model->count() / $model->limit()) : 1,
				'index' => $page,
			));
			*/

			// setup fields that are visible in the table (user selected)
			$fields = array(
				'user' => 'User',
				'text' => 'Tweet',
				//'options' => 'Options',
			);
			UI::set('FIELDS', $fields);
			UI::set('FIXED', array());

			UI::set('FORMAT.user',
				function($record) {
					//
					return '<img src="'.$record['image'].'" style="width:48px; float:left; margin-right:10px;" />'.
						'<strong>'.$record['name'].'</strong><br />'.
						'<a href="http://twitter.com/'.$record['user'].'" target="_blank">'.$record['user'].' &raquo;</a><br />'.
						''.date(Conf::get('FORMAT:DATE:LONG'), strtotime($record['date']));
				}
			);

			UI::set('FORMAT.text',
				function($record) {
					//
					$tweet = $record['text'];
					$tweet = Util::Links( $tweet ); //preg_replace('/http:\/\/([a-zA-Z0-9\.\/]+)/', '<a href="$0" target="_blank">$0</a>', $tweet);
					$tweet = preg_replace('/#(\w+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank">$0</a>', $tweet);
					$tweet = preg_replace('/@(\w+)/', '<a href="http://twitter.com/$1" target="_blank">$0</a>', $tweet);

					return $tweet . '<br /><br />'.
						'<a rel="modal" href="'.Request::$URL.'/retweet/'.$record['id'].'">Retweet</a> - <a rel="modal" href="'.Request::$URL.'/reply/'.$record['id'].'">Reply</a>';
				}
			);

			UI::set('OPTIONS.retweet', Array(
				'handler' => 'retweet',
				'rel' => 'modal',
				'icon' => 'icon.small/twitter.retweet.png',
				'title' => 'Retweet',
			));

			UI::set('OPTIONS.reply', Array(
				'handler' => 'reply',
				'rel' => 'modal',
				'icon' => 'icon.small/twitter.reply.png',
				'title' => 'Reply',
			));

			UI::nset('TOOLBAR.new', array(
					'url' => Request::$URL.'/new',
					'icon' => 'icon.small/twitter.bird.png',
					'title' => 'New Tweet',
				)
			);

			UI::set('CONTENT', UI::Render('admin/.shared.list.php'));

			parent::Get();
		}

		static function POST_Save()
		{
			if (!Request::POST('tweet')) {
				//
				self::Error('You cannot submit an empty tweet.');
				return;
			}

			$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
				Session::Get('TWITTER.access.token'), Session::Get('TWITTER.access.secret'));

			$api->post('statuses/update', array('status' => Request::POST('tweet')));
		}

		static function GET_New()
		{
			$fields = array();

			$input = new Input('tweet');
			$input->Type(Input::F_LONGTEXT)->Title('What\'s happening ?')->Width('98%')->Value( '' );

			$fields['tweet'] = $input; //->Export();

			UI::set('TITLE', 'New Tweet');
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function GET_Reply()
		{
			$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
				Session::Get('TWITTER.access.token'), Session::Get('TWITTER.access.secret'));

			$tweet = $api->get('statuses/show/' . Request::URL('id'));

			$fields = array();

			$input = new Input('tweet');
			$input->Type(Input::F_LONGTEXT)->Title('What\'s happening ?')->Width('98%')->Value( '@' . $tweet->user->screen_name . ' ' );

			$fields['tweet'] = $input; //->Export();

			UI::set('TITLE', 'Reply');
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function GET_Retweet()
		{
			$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
				Session::Get('TWITTER.access.token'), Session::Get('TWITTER.access.secret'));

			$tweet = $api->get('statuses/show/' . Request::URL('id'));

			UI::set('TARGET', Request::$URL . '/retweet/' . $tweet->id_str);
			UI::set('CONTENT', 'Are you sure you want to retweet this post ?<br /><br />' . $tweet->text);

			UI::set('TITLE', 'Retweet');
			UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

			parent::Popup();
		}

		static function POST_Retweet()
		{
			$api = new SocialTwitterApi( Conf::get('SOCIAL:TWITTER:KEY'), Conf::get('SOCIAL:TWITTER:SECRET'),
				Session::Get('TWITTER.access.token'), Session::Get('TWITTER.access.secret'));

			$tweet = $api->post('statuses/retweet/' . Request::URL('id'));
		}

	}

?>