<?php

	// SocialFacebook

	class SocialFacebook extends AdminPage
	{
		static
			$TITLE  = 'Facebook',
			$IDENT  = 'social.facebook';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/facebook.png',
				'LARGE' => 'icon.large/facebook.png',
			);

		static
			$AUTH = 'social.facebook';


		static function onLoad()
		{
			Session::Set('FACEBOOK.access.token', Model::Settings('social.facebook.token')->value);
			Session::Set('FACEBOOK.page', Model::Settings('social.facebook.page')->value);

			if (!Session::Get('FACEBOOK.page')) {
				//
				Session::Set('FACEBOOK.page', 'me');
			}
		}

		static function GET()
		{
			static::CallHandler('list');
		}

		static function GET_List($page = 0)
		{
			$posts = array();

			if (Session::Get('FACEBOOK.access.token')) {
				//
				$api = SocialFacebookApi::Init( Conf::get('SOCIAL:FACEBOOK:KEY'), Conf::get('SOCIAL:FACEBOOK:SECRET') );

				//$friends = $api->api('me/friends', array('fields' => 'name, id, picture'));
				$feed = $api->api(Session::Get('FACEBOOK.page') . '/feed');

				//print '<pre>';
				//print_r($feed); die();

				foreach ($feed['data'] as $post) {
					//
					//if (isset($post['story']) || isset($post['message']) || isset($post['description'])) {
						//
						$posts[] = array(
							'id' => $post['id'],
							'user' => $post['from']['id'],
							'name' => $post['from']['name'],
							//'image' => isset($images[ $post['from']['id'] ]) ? $images[ $post['from']['id'] ] : '',
							'image' => "http://graph.facebook.com/{$post['from']['id']}/picture",
							'text' => isset($post['story']) ? $post['story'] : (isset($post['message']) ? $post['message'] : (isset($post['description']) ? $post['description'] : '')),
							'picture' => isset($post['picture']) ? $post['picture'] : '',
							'link' => isset($post['link']) ? $post['link'] : '',
							'title' => isset($post['name']) ? $post['name'] : '',
							'caption' => isset($post['caption']) ? $post['caption'] : '',
							'date' => $post['created_time'],
							'comments' => isset($post['comments']['data']) ? $post['comments']['data'] : array(),
						);
					//}
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
				'text' => 'Message',
				//'options' => 'Options',
			);
			UI::set('FIELDS', $fields);
			UI::set('FIXED', array());

			UI::set('FORMAT.user',
				function($record) {
					//
					return ($record['image'] ? '<img src="'.$record['image'].'" style="width:48px; float:left; margin-right:10px;" />' : '').
						'<strong>'.$record['name'].'</strong><br />'.
						'<a href="http://facebook.com/profile.php?id='.$record['user'].'" target="_blank">Visit Profile &raquo;</a><br />'.
						''.date(Conf::get('FORMAT:DATE:LONG'), strtotime($record['date']));
				}
			);

			UI::set('FORMAT.text',
				function($record) {
					//
					$result = $record['picture'] ? '<img src="'.$record['picture'].'" style="width:120px; float:left; margin-right:10px; border:1px solid #CCC; padding:3px;" />' : '';
					$result .= $record['link'] ? '<a href="'.$record['link'].'" target="_blank">'.$record['title'].'</a><br />'.$record['caption'].'<br /><br />' : '';
					$result .= $record['text'] ? Util::Links( $record['text'] ).'<br /><br />' : '';

					$result .= '<br />';

					if (count($record['comments'])) {
						//
						$result .= '<table style="width:100%" cellspacing=0 cellpadding=0>';

						foreach ($record['comments'] as $comment) {
							//
							$result .= '<tr style="border-top:1px solid #CCC"><td style="width:200px">'.
								'<img src="http://graph.facebook.com/'.$comment['from']['id'].'/picture" style="width:32px; float:left; margin-right:10px;" />'.
								'<strong>'.$comment['from']['name'].'</strong><br />'.
								date(Conf::get('FORMAT:DATE:LONG'), strtotime($comment['created_time'])).
								'</td><td> '.$comment['message'].'</td></tr>';
						}

						$result .= '</table>';
					}

					$result .= '<a rel="modal" href="'.Request::$URL.'/reply/'.$record['id'].'">Reply</a>';

					return $result;
				}
			);

			UI::set('OPTIONS.reply', Array(
				'handler' => 'reply',
				'rel' => 'modal',
				'icon' => 'icon.small/twitter.reply.png',
				'title' => 'Reply',
			));

			UI::nset('TOOLBAR.new', array(
					'url' => Request::$URL.'/new',
					'icon' => 'icon.small/facebook.png',
					'title' => 'New Post',
				)
			);

			UI::set('CONTENT', UI::Render('admin/.shared.list.php'));

			parent::Get();
		}

		static function POST_Save()
		{
			if (!Request::POST('message')) {
				//
				self::Error('You cannot submit an empty post.');
				return;
			}

			$api = SocialFacebookApi::Init( Conf::get('SOCIAL:FACEBOOK:KEY'), Conf::get('SOCIAL:FACEBOOK:SECRET') );

			$api->api(Session::Get('FACEBOOK.page') . '/feed', 'post', array(
			    'message' => Request::POST('message'),
			    /*
			    'name' => 'The name',
			    'description' => 'The description',
			    'caption' => 'The caption',
			    'picture' => 'http://i.imgur.com/yx3q2.png',
			    'link' => 'http://net.tutsplus.com/',
			    */
			));
		}

		static function GET_New()
		{
			$fields = array();

			$input = new Input('message');
			$input->Type(Input::F_LONGTEXT)->Title('What\'s on your mind ?')->Width('98%')->Value( '' );

			$fields['message'] = $input; //->Export();

			UI::set('TITLE', 'New Wall Post');
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function GET_Reply()
		{
			$fields = array();

			$input = new Input('message');
			$input->Type(Input::F_LONGTEXT)->Title('Comment')->Width('98%')->Value( '' );

			$fields['message'] = $input; //->Export();

			UI::set('TITLE', 'Reply');
			UI::set('FIELDS', $fields);

			UI::set('TARGET', Request::$URL . '/reply/' . Request::URL('id'));
			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Reply()
		{
			if (!Request::POST('message')) {
				//
				self::Error('You cannot submit an empty comment.');
				return;
			}

			$api = SocialFacebookApi::Init( Conf::get('SOCIAL:FACEBOOK:KEY'), Conf::get('SOCIAL:FACEBOOK:SECRET') );

			$api->api('/'.Request::URL('id').'/comments', 'post', array(
			    'message' => Request::POST('message'),
			));
		}

	}

?>