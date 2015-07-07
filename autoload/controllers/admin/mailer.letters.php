<?php

	// MailerLetters

	class MailerLetters extends CmsTemplates
	{
		static
			$TITLE  = 'Letters',
			$IDENT  = 'mailer.letter';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/page.full.png',
				'LARGE' => 'icon.large/page.full.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Letters',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'mailer.letter';

		static
			$TYPE = 'mm.letter';


		static function GET_Details()
		{


			parent::GET_Details();
		}

		static function Toolbar()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('TOOLBAR.send', array(
					'url' => Request::$URL . '/send/' . $model->id,
					'rel' => 'modal',
					'icon' => 'icon.small/email.send.png',
					'title' => 'Send'
				)
			);

			UI::set('TOOLBAR.test', array(
					'url' => Request::$URL . '/test/' . $model->id,
					'rel' => 'modal',
					'icon' => 'icon.small/email.error.png',
					'title' => 'Test'
				)
			);

			UI::set('TABBAR', array(
			    	'preview' => 'Preview',
					'html' => 'HTML',
				)
			);
		}

		static function GET_Send()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$recipients = Admin::RecipientFeed();

			$input = new Input('list');
			$input->Type(Input::F_SELECT)->Title('Recipient List')->Context('VALUES')->Options($recipients);
			$fields = array( $input );

			UI::set('TITLE', 'Send Newsletter');
			UI::set('TARGET', Request::$URL . '/send/' . $model->id);
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Send()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$result = $model->html;

			$result = preg_replace_callback('/\{(EDITABLE )(.*)\}/iU',
				function($matches){ return ''; }, $result);

			$result = preg_replace_callback('/\{(BLOCK )(.*)\}/iU',
				function($matches){ return Admin::HtmlFeed($matches[2]); }, $result);

			// replace all links with proxy
			$result = preg_replace_callback('/\<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>/iU',
				function($matches){ return str_replace($matches[2], 'http://'.Request::$HOST.'/admin/mailer/proxy?m={$MESSAGE.ID}&u='.urlencode($matches[2]), $matches[0]); }, $result);

			// SETUP CAMPAIGN
			$campaign = Model::MailerCampaign();
			$campaign->defaults();

			$campaign->idtemplate = $model->id;
			$campaign->subject = $model->meta_title;
			$campaign->template = $result;
			$campaign->feed = Request::POST('VALUES.list');

			$campaign->save();

			$FEED = Admin::RecipientFeed( $campaign->feed );

			//print 'FEED:'.$campaign->idlist; die();

			foreach ($FEED['RESULT'] as $recipient) {
				//
				$message = Model::MailerMessage();
				$message->defaults();

				$message->idcampaign = $campaign->id;
				$message->name = $recipient['name'];
				$message->email = $recipient['email'];

				$message->save();
			}
		}

		static function GET_Test()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$input = new Input('email');
			$input->Type(Input::F_TEXT)->Title('Test Email')->Context('VALUES')->Value( Session::Get('ACCOUNT.EMAIL') );
			$fields = array( $input );

			UI::set('TITLE', 'Test Current Version');
			UI::set('TARGET', Request::$URL . '/test/' . $model->id);
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Test()
		{
			if (!Request::POST('VALUES.email')) {
				//
				UI::Set('MESSAGE.WARNING', 'Please enter an email address where you want to receive a campaign test');
				UI::Serve('admin/.shared.alerts.php');
				return;
			}

			$model = static::Model( intval( Request::URL('id') ) );

			$result = $model->html;

			$result = preg_replace_callback('/\{(EDITABLE )(.*)\}/iU',
				function($matches){ return ''; }, $result);

			$result = preg_replace_callback('/\{(BLOCK )(.*)\}/iU',
				function($matches){ return Admin::HtmlFeed($matches[2]); }, $result);

			if ( Mail::Send(Request::POST('VALUES.email'), 'Test: '.$model->meta_title, $result) ) {
				//
				UI::Set('MESSAGE.SUCCESS', 'A test email has been sent to: '.Request::POST('VALUES.email'));
				UI::Serve('admin/.shared.alerts.php');
			}
			else {
				//
				UI::Set('MESSAGE.ERROR', Mail::$ERROR ? Mail::$ERROR : 'Mail Server Error.');
				UI::Serve('admin/.shared.alerts.php');
			}
		}

	}

?>