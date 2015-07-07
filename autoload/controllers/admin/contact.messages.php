<?php

	// ContactMessages

	class ContactMessages extends AdminModule
	{
		static
			$TITLE  = 'Messages',
			$IDENT  = 'contact.messages';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/email.png',
				'LARGE' => 'icon.large/email.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Messages',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'contact.messages';

		static
			$STATUS = Array(
				0 => 'Open',
				1 => 'Solved',
				2 => 'Closed',
			);


		static function Model($id = NULL)
		{
			return Model::ContactMessage($id);
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}

			UI::set('FORMAT.name', function($record) {
				//
				return $record['read'] ? $record['name'] : $record['name'].' <span style="color:red">(New)</span>';
			});

			UI::set('FORMAT.flags', function($record) {
				//
				$flags = $record['flags']->slice('color');

				$result = '';
				foreach ($flags as $color) {
					//
					$result .= '<img src="'.Conf::Get('WWW:ROOT').'/admin/images/icon.small/flag.'.$color.'.png" />';
				}
				return $result;
			});


			UI::set('FORMAT.status', function($record) {
				//
				return '<span style="color:'.($record['status'] == 0 ? 'red' : ($record['status'] == 1 ? 'green' : 'black')).'">'.
							ContactMessages::$STATUS[ $record['status'] ].'</span>';
			});

			parent::GET_List( $page );
		}

		static function GET_Details()
		{
			UI::set('STATUS', self::$STATUS);

			$message = static::Model( intval( Request::URL('id') ) );

			UI::set('MESSAGE', $message->record());
			UI::set('MESSAGE.COMMENTS', $message->comments->export());
			UI::set('MESSAGE.FLAGS', $message->flags->export());

			$replies = Model::ContactReply();
			$replies->where('status > -1')->order('name ASC')
				->execute();

			UI::set('REPLIES', $replies->export());

			UI::set('FOOTER', UI::Render('admin/contact.message.comments.php'));
			UI::set('CONTENT', UI::Render('admin/contact.message.details.php'));

			UI::set('SECTION', 'Message from '.$message->name.' (#'.$message->id.')');

			$message->read = 1;
			$message->save();

			self::Wrapper();
		}

		static function GET_Flags()
		{
			$model = self::Model( intval(Request::URL('id')) );

			$flags = Model::ContactFlag();
			$flags->where('status > -1')
				->execute();

			UI::Set('TARGET', Request::$URL . '/flags/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $flags->export());
			UI::Set('SELECTED', $model->flags->slice('id'));

			UI::Set('TITLE', 'Flags');
			UI::Set('CONTENT', 'Select Flags:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Flags()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				$items = array();
			}

			$model = self::Model( intval(Request::URL('id')) );

			$sql = new SQL();
			$sql->delete('contact_messages_flags')
				->where('idmessage = ?', $model->id)
				->execute();

			foreach ($items as $flag) {
				//
				$sql = new SQL();
				$sql->insert('contact_messages_flags')
					->set('idmessage = ?, idflag = ?', $model->id, $flag)
					->execute();
			}
		}

		static function POST_Comment()
		{
			$model = Model::ContactMessage( intval(Request::URL('id')) );

			$comment = Model::ContactComment();
			$comment->defaults();

			$comment->idadmin = \Session::Get('ACCOUNT.ID');
			$comment->idmessage = $model->id;
			$comment->content = Request::POST('VALUES.content');

			$comment->save();

			// NOTIFICATION

			$DATA = array(
				'MESSAGE' => $model->record(),
				'COMMENTS' => $model->comments->export(),
				'URL' => Model::Settings('contact.url')->value,
			);

			$subject = 'A new message has been posted on your ticket: '.$model->uid;
			$message = UI::Render('admin/contact.notification.php', $DATA);

			if ( Mail::Send($model->email, $subject, $message) ) {
				//
			}
			else {
				//
				$info = '<p>A notification could not be sent to <strong>'.$model->name.'</strong>.<br />'.
					'Please use an alternative method to send your message to <a href="mailto:'.$model->email.'">'.$model->email.'</a>';

				UI::Set('MESSAGE.ERROR', (Mail::$ERROR ? Mail::$ERROR : 'Mail Server Error.').$info);

				UI::Serve('admin/.shared.alerts.php');
			}
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('status') !== NULL) {
				//
				$model->status = Request::POST('status');
			}
			else {
				//
				$model->status = ($model->status == 1) ? 0 : 1;
			}

			$model->save();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			$model->uid = md5($model->id . $model->email);
			$model->save();

			$comment = Model::ContactComment();
			$comment->defaults();

			$comment->idadmin = 0;
			$comment->idmessage = $model->id;
			$comment->content = Request::POST('VALUES.message');

			$comment->save();

			return $model;
		}

		static function FilterForm()
		{
			$filters = parent::FilterForm();

			$filters['date']->Type(Input::F_DATERANGE);

			return $filters;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$subjects = DB::AssociativeColumn("SELECT id, name FROM contact_subjects WHERE status > -1");

			$fields['idsubject']->Type( Input::F_SELECT )
				->Options(array_merge(array('-'), $subjects));

			$fields['message']->Type( Input::F_RICHTEXT );

			return $fields;
		}

		static function Notification()
		{
			$num = DB::Fetch('SELECT COUNT(*) FROM contact_messages WHERE `read` = 0 AND `status` > -1');

			return $num;
		}

		static function DataFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = array(
				'CONTACT.FORM' => Array(
					'title' => 'Contact Form',
					'hint' => '',
				),
				'CONTACT.MESSAGE.THREAD' => Array(
					'title' => 'Message Thread',
					'hint' => 'URL requires <strong>@uid</strong> containing the message <strong>Unique ID</strong>',
				),
			);

			if (!$feed) {
				//
				return $FEEDS;
			}

			if ($feed == 'CONTACT.FORM') {
				//
				$error = '';
				if (Request::POST('contact.submit')) {
					//
					if (!Request::POST('contact.name') || !Request::POST('contact.email') || !Request::POST('contact.message')) {
						$error = 'Please fill in all mandatory fields: Name, Email and Message.';
					}
					if (Request::POST('contact.captcha') !== NULL) {
						if (intval(Request::POST('contact.captcha')) != 5) {
							$error = 'Please make sure you read and enter a value for the Human Validation Textbox.';
						}
					}

					if (!$error) {
						//
						$model = Model::ContactMessage();
						$model->defaults();

						$model->idsubject = Request::POST('contact.subject');
						$model->name = Request::POST('contact.name');
						$model->email = Request::POST('contact.email');
						$model->save();

						$model->uid = md5($model->id . $model->email);
						$model->save();

						$comment = Model::ContactComment();
						$comment->defaults();

						$comment->idmessage = $model->id;
						$comment->content = Request::POST('contact.message');

						$comment->save();

						$error = 'Your message has been submitted. Thank You!';
					}
				}

				$subjects = DB::AssociativeColumn('SELECT id, name FROM contact_subjects WHERE status = 1');
				$options = '';
				foreach ($subjects as $id => $subject) {
					//
					$options .= '<option value="'.$id.'">'.$subject.'</option>';
				}

				$form = Array(
					'error' => $error,
					'name' => '<input id="contact-name" type="text" name="contact[name]" value="" />',
					'email' => '<input id="contact-email" type="text" name="contact[email]" value="" />',
					'subject' => '<select id="contact-subject" name="contact[subject]">'.$options.'</subject>',
					'message' => '<textarea id="contact-message" name="contact[message]"></textarea>',

					'captcha' => 'What is two plus three? <input id="contact-captcha" type="text" name="contact[captcha]" value="" />',
					'submit' => '<input id="contact-submit" type="submit" name="contact[submit]" value="Submit" />',
				);

				$subjects = Model::ContactSubject();
				$subjects->where('status = 1')
					->execute();

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/contact.form.php');

				$RESULT['RESULT']['SUBJECTS'] = $subjects->export();
				$RESULT['RESULT']['FORM'] = $form;

				$RESULT['PROPERTIES'] = Array(
					'FORM.name' => 'Contact Form: Name',
					'FORM.email' => 'Contact Form: Email',
					'FORM.captcha' => 'Contact Form: Captcha',
					'FORM.message' => 'Contact Form: Message',
					'FORM.submit' => 'Contact Form: Submit Button',
					'FORM.error' => 'Contact Form: Submission Error',

					'{foreach $SUBJECTS as $SUBJECT}'."\n\t\n".'{/foreach}' => 'Loop Through Subjects',
					'SUBJECT.id' => '- Subject ID',
					'SUBJECT.name' => '- Subject Name',
				);

				$RESULT['FILTERS'] = Array();

				return $RESULT;
			}

			if ($feed == 'CONTACT.MESSAGE.THREAD') {
				//
				$model = Model::ContactMessage();
				$model->where('status > -1');

				if (Request::URL('uid')) {
					//
					$model->where('uid = ?', Request::URL('uid'));
				}
				$model->execute();

				if (!$model->found()) {
					//
					$err = Request::URL('uid') ? 'There is no record with UID #'.Request::URL('uid').'. Please check your page URL and make sure the code @uid is placed properly.' :
						'No UID was specified in the URL and no contact thread was found in the database. Please make sure you entered the @uid code in your page URL.';

					Application::Error(500, 'There was an error retrieving {'.$feed.'}: '.$err);
					return;
				}

				// POSSIBLE FORM SUBMISSION

				$error = '';
				if (Request::POST('comment.submit')) {
					//
					if ( !Request::POST('comment.content')) {
						$error = 'Please fill in all mandatory fields: Comment.';
					}

					if (!$error) {
						//
						$comment = Model::ContactComment();
						$comment->defaults();

						$comment->idmessage = $model->id;
						$comment->content = Request::POST('comment.content');

						$comment->save();
						$error = 'Your comment has been submitted. Thank You!';
					}
				}

				$form = Array(
					'error' => $error,
					'comment' => '<textarea id="comment-content" name="comment[content]"></textarea>',
					'submit' => '<input id="comment-submit" type="submit" name="comment[submit]" value="Submit" />',
				);

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/contact.thread.php');

				$RESULT['RESULT']['MESSAGE'] = $model->record( $depth = 4 );
				$RESULT['RESULT']['FORM'] = $form;

				$RESULT['PROPERTIES'] = Array(

					'MESSAGE.subject' => 'Message Subject',
					'MESSAGE.date' => 'Message Date',
					'MESSAGE.name' => 'Contact Name',
					'MESSAGE.email' => 'Contact Email',
					'MESSAGE.uid' => 'Unique ID',

					'{foreach $MESSAGE.comments as $COMMENT}'."\n\t\n".'{/foreach}' => 'Loop Through Comments',
					'COMMENT.date' => '- Comment Date',
					'COMMENT.content' => '- Comment Content',
					'COMMENT.idadmin' => '- Flag: Is Administration Reply',
					'COMMENT.admin' => '- Administrator Name',
					'COMMENT.contact' => '- Contact Name',

					'FORM.comment' => 'Comment Form: Message',
					'FORM.submit' => 'Comment Form: Submit Button',
					'FORM.error' => 'Comment Form: Submission Errors',
				);

				$RESULT['FILTERS'] = Array();

				return $RESULT;
			}
		}

		static function HtmlFeed( $feed = NULL )
		{
			$FEEDS = self::DataFeed();

			if (!$feed) {
				//
				return $FEEDS;
			}

			if ($feed == 'CONTACT.FORM') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/contact.form.php', $data['RESULT']);
			}

			if ($feed == 'CONTACT.MESSAGE.THREAD') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/contact.thread.php', $data['RESULT']);
			}

		}

	}

?>