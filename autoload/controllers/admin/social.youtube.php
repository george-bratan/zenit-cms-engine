<?php

	// SocialYoutube

	class SocialYoutube extends AdminPage
	{
		static
			$TITLE  = 'YouTube!',
			$IDENT  = 'social.youtube';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/youtube.png',
				'LARGE' => 'icon.large/youtube.png',
			);

		static
			$AUTH = 'social.youtube';


		static function onLoad()
		{
			Session::Set('YOUTUBE.access.token', Model::Settings('social.youtube.token')->value);
			Session::Set('YOUTUBE.access.secret', Model::Settings('social.youtube.secret')->value);
		}

		static function GET()
		{
			static::CallHandler('list');
		}

		static function GET_List($page = 0)
		{
			$posts = array();

			if (Session::Get('YOUTUBE.access.token')) {
				//
				$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
					Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

				$feed = $api->get('https://gdata.youtube.com/feeds/api/users/default/uploads?v=2&alt=json');
				$feed = json_decode( $feed, $assoc = TRUE );

				foreach ($feed['feed']['entry'] as $entry) {
					//
					$posts[] = array(
						'id' => $entry['media$group']['yt$videoid']['$t'],
						'title' => $entry['title']['$t'],
						'video' => isset($entry['content']['src']) ? $entry['content']['src'] : '',
						'description' => $entry['media$group']['media$description']['$t'],
						'keywords' => $entry['media$group']['media$keywords']['$t'],
						'duration' => $entry['media$group']['yt$duration'],
						'date' => $entry['media$group']['yt$uploaded']['$t'],
						'image' => $entry['media$group']['media$thumbnail'][0]['url'],
						'comments' => $entry['gd$comments']['gd$feedLink']['countHint'],
						'viewed' => isset($entry['yt$statistics']['viewCount']) ? $entry['yt$statistics']['viewCount'] : 0,
						'state' => isset($entry['app$control']['yt$state']['$t']) ? ucwords($entry['app$control']['yt$state']['name']) .': '. $entry['app$control']['yt$state']['$t'] : '',
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
				'video' => 'Video',
				'description' => 'Description',
				//'options' => 'Options',
			);
			UI::set('FIELDS', $fields);
			UI::set('FIXED', array());

			UI::set('FORMAT.video',
				function($record) {
					//
					return '<a rel="modal" href="'.Request::$URL.'/play/'.$record['id'].'"><img src="'.$record['image'].'" style="width:120px; float:left; margin-right:10px;" /></a>'.
						'<strong>'.
							(isset($record['duration']['hours']) ? $record['duration']['hours'].':' : '').
							(isset($record['duration']['minutes']) ? $record['duration']['minutes'].':' : '00:').
							(isset($record['duration']['seconds']) ? $record['duration']['seconds'].'s ' : '').
						'</strong><br /><br />'.
						''.$record['viewed'].' views<br />'.
						''.date(Conf::get('FORMAT:DATE:LONG'), strtotime($record['date'])).'<br />'.
						''.($record['video'] ? '<a rel="modal" href="'.Request::$URL.'/play/'.$record['id'].'">Watch Video</a>' : '<span style="color:red">'.$record['state'].'</span>');
				}
			);

			UI::set('FORMAT.description',
				function($record) {
					//
					$post = '';
					$post .= '<strong>'.$record['title'].'</strong><br />';
					$post .= '<p style="margin-bottom:15px;">'.$record['description'].'</p>';
					$post .= '<p>Tags: <em style="font-style:italic">'.$record['keywords'].'</em></p>';
					$post .= '<a rel="modal" href="'.Request::$URL.'/comments/'.$record['id'].'">'.($record['comments'] ? $record['comments'] : 'No').' '.(intval($record['comments'])==1 ? 'comment' : 'comments').'</a>';

					return $post;
				}
			);

			UI::set('OPTIONS.edit', Array(
				'handler' => 'edit',
				'rel' => 'modal',
				'icon' => 'icon.small/edit.png',
				'title' => 'Edit',
			));

			UI::set('OPTIONS.comments', Array(
				'handler' => 'comments',
				'rel' => 'modal',
				'icon' => 'icon.small/comments.png',
				'title' => 'Comments',
			));

			UI::nset('TOOLBAR.new', array(
					'rel' => "popup",
					'url' => Request::$URL.'/new',
					//'icon' => 'icon.small/twitter.bird.png',
					'title' => 'New Video',
					'attr' => array('popup-width' => 650, 'popup-height' => 470 ),
				)
			);

			UI::set('CONTENT', UI::Render('admin/.shared.list.php'));

			parent::Get();
		}

		static function GET_Play()
		{
			$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
					Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

			$video = $api->get('https://gdata.youtube.com/feeds/api/users/default/uploads/'.Request::URL('id').'?v=2&alt=json');
			$video = json_decode( $video, $assoc = TRUE );

			$title = $video['entry']['title']['$t'];
			$flash = $video['entry']['content']['src'];

			UI::set('TITLE', 'Watch: ' . $title);
			UI::set('FLASH', $flash);

			UI::set('CONTENT', UI::Render('admin/.shared.flash.php'));

			parent::Popup();
		}

		static function GET_Comments()
		{
			$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
				Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

			$feed = $api->get('https://gdata.youtube.com/feeds/api/videos/'.Request::URL('id').'/comments?v=2&alt=json');
			$feed = json_decode( $feed, $assoc = TRUE );

			$comments = array();
			if (isset($feed['feed']['entry']))
			foreach ($feed['feed']['entry'] as $entry) {
				//
				$comments[] = array(
					'user' => $entry['author'][0]['name']['$t'],
					'profile' => 'https://www.youtube.com/profile?user=' . $entry['author'][0]['name']['$t'],
					'content' => $entry['content']['$t'],
					'date' => $entry['published']['$t'],
				);
			}

			UI::set('VIDEO', Request::URL('id'));
			UI::set('COMMENTS', $comments);

			$fields = array();
			$input = new Input('comment');
			$input->Type(Input::F_LONGTEXT)->Title('Respond')->Width('98%');
			$fields['comment'] = $input;

			UI::set('FIELDS', $fields);

			UI::set('TITLE', 'Comments');
			UI::set('CONTENT', UI::Render('admin/social.youtube.comments.php'));

			parent::Popup();
		}

		static function GET_New()
		{
			UI::set('TITLE', 'Upload Video');
			UI::set('FIELDS', self::EditForm());

			UI::set('AJAX', FALSE);
			UI::set('TARGET', Request::$URL . '/save');
			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popout();
		}

		static function GET_Edit()
		{
			$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
				Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

			$video = $api->get('https://gdata.youtube.com/feeds/api/users/default/uploads/'.Request::URL('id').'?v=2&alt=json');
			$video = json_decode( $video, $assoc = TRUE );

			UI::set('TITLE', 'Edit Video');
			UI::set('FIELDS', self::EditForm($video));

			UI::set('TARGET', Request::$URL . '/save/' . Request::URL('id'));
			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function EditForm($video = NULL)
		{
			$fields = array();

			$input = new Input('title');
			$input->Type(Input::F_TEXT)->Title('Video Title')->Width('98%')->Value( $video ? $video['entry']['title']['$t'] : '' )->Context('VIDEO');
			$fields['title'] = $input;

			$input = new Input('description');
			$input->Type(Input::F_LONGTEXT)->Title('Description')->Width('98%')->Value( $video ? $video['entry']['media$group']['media$description']['$t'] : '' )->Context('VIDEO');
			$fields['description'] = $input;

			$cat = Util::split('Film|Autos|Music|Animals|Sports|Travel|Games|Comedy|People|News|Entertainment|Education|Howto|Nonprofit|Tech');
			$cat = array_combine($cat, $cat);

			$cat = array(
				'Film' => 'Film &amp; Animation',
				'Autos' => 'Autos &amp; Vehicles',
				'Music' => 'Music',
				'Animals' => 'Pets &amp; Animals',
				'Sports' => 'Sports',
				'Travel' => 'Travel &amp; Events',
				'Games' => 'Gaming',
				'Comedy' => 'Comedy',
				'People' => 'People &amp; Blogs',
				'News' => 'News &amp; Politics',
				'Entertainment' => 'Entertainment',
				'Education' => 'Education',
				'Howto' => 'Howto &amp; Style',
				'Nonprofit' => 'Nonprofits &amp; Activism',
				'Tech' => 'Science &amp; Technology',
			);

			$input = new Input('category');
			$input->Type(Input::F_SELECT)->Title('Category')->Value( $video ? $video['entry']['media$group']['media$category'][0]['$t'] : '' )->Context('VIDEO');
			$input->Options($cat);
			$fields['category'] = $input;

			$input = new Input('keywords');
			$input->Type(Input::F_TEXT)->Title('Keywords')->Value( $video ? $video['entry']['media$group']['media$keywords']['$t'] : '' )->Context('VIDEO');
			$fields['keywords'] = $input;

			return $fields;
		}

		static function POST_Save()
		{
			$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
				Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

			$videoxml =
'<?xml version="1.0"?>
<entry xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:yt="http://gdata.youtube.com/schemas/2007">
  <media:group>
    <media:title type="plain">'.Request::POST('VIDEO.title').'</media:title>
    <media:description type="plain">'.Request::POST('VIDEO.description').'</media:description>
    <media:category scheme="http://gdata.youtube.com/schemas/2007/categories.cat">'.Request::POST('VIDEO.category').'</media:category>
    <media:keywords>'.Request::POST('VIDEO.keywords').'</media:keywords>
  </media:group>
</entry>';

			if (Request::URL('id')) {
				//
				$post = $api->put('https://gdata.youtube.com/feeds/api/users/gbratan/uploads/'.Request::URL('id').'?v=2&alt=json&key='.Conf::get('SOCIAL:YOUTUBE:KEY'),
					array(), $videoxml);
			}
			else {
				//
				$post = $api->post('https://gdata.youtube.com/action/GetUploadToken?v=2&alt=json&key='.Conf::get('SOCIAL:YOUTUBE:KEY'),
					array(), $videoxml);

				//$post = json_decode($post, $assoc = TRUE);

				if (preg_match('/<url>(.*)<\/url>/', $post, $matches)) {
					//
					$url = $matches[1];
				}

				if (preg_match('/<token>(.*)<\/token>/', $post, $matches)) {
					//
					$token = $matches[1];
				}

				UI::set('URL', $url . '?nexturl=' . urlencode('http://' . Request::$HOST . Request::$URL . '/done'));
				UI::set('TOKEN', $token);
				UI::set('VIDEO', Request::POST('VIDEO'));

				UI::set('TITLE', 'Upload Video');
				UI::set('CONTENT', UI::Render('admin/social.youtube.upload.php'));

				parent::Popout();
			}
		}

		static function GET_Done()
		{
			print '<script type="text/javascript">window.opener.$.fancybox.showActivity(); window.opener.location = window.opener.location; window.close(); </script>';
		}

		static function POST_Comment()
		{
			$api = new SocialGoogleApi( Conf::get('SOCIAL:GOOGLE:KEY'), Conf::get('SOCIAL:GOOGLE:SECRET'),
				Session::Get('YOUTUBE.access.token'), Session::Get('YOUTUBE.access.secret'));

			$post = $api->post('https://gdata.youtube.com/feeds/api/videos/'.Request::URL('id').'/comments?v=2&alt=json&key='.Conf::get('SOCIAL:YOUTUBE:KEY'), array(),
'<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom" xmlns:yt="http://gdata.youtube.com/schemas/2007">
  <content>'.Request::POST('comment').'</content>
</entry>');

			//$post = json_decode($post, $assoc = TRUE);
		}

	}

?>