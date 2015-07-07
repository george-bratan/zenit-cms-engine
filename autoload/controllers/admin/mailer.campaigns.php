<?php

	// MailerCampaigns

	class MailerCampaigns extends AdminList
	{
		static
			$TITLE  = 'History',
			$IDENT  = 'mailer.campaigns';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/email.png',
				'LARGE' => 'icon.large/email.png',
			);

		/*
		static
			$PERMISSION = Array(
				'list' 		=> 'List Recipients',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);
		*/

		static
			$AUTH = 'mailer.campaigns';

		static
			$STATUS = Array(
				0 => 'Pending',
				1 => 'Sent',
				2 => 'Errors',
				3 => 'Bounced',
			);

		static
			$OPENED = Array(
				0 => 'Not Opened',
				1 => 'Opened',
				2 => 'Clicked',
			);


		static function Model($id = NULL)
		{
			return Model::MailerCampaign($id);
		}

		static function GET_List($page = 0)
		{
			//if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			//}

			//UI::set('TOOLBAR.new', FALSE);

			parent::GET_List($page);
		}

		static function GET_Details($feed = NULL, $params = NULL)
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('STATUS', self::$STATUS);
			UI::set('OPENED', self::$OPENED);
			UI::set('NEWSLETTER', $model->record());
			UI::set('RECIPIENTS', $model->messages->export());

			UI::set('STATS', Array(
				'processed'   => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status > 0, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'sent' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status = 1, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'errors' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status = 2, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'bounced' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status > 2, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'open' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(open = 1, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'clicked' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(open = 2, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
			));

			UI::set('TOOLBAR.more', array(
					'url' => 'javascript:void(0);',
					'rel' => '#',
					'id' => 'btn_more',
					'title' => 'Hide &raquo;'
				)
			);

			UI::set('TOOLBAR.send', array(
					'url' => Request::$URL . '/send/' . $model->id,
					'rel' => '#',
					'id' => 'btn_send',
					'icon' => 'icon.small/email.send.png',
					'title' => 'Send'
				)
			);

			UI::set('PANELS.recipients', Array(
				'TITLE' => '&nbsp;',
				'TABBAR' => array('recp' => 'Recipients', 'report' => 'Report'),
				/*
				'TOOLBAR' => array('hide' => array(
					'url' => 'javascript:void(0);',
					'rel' => '#',
					'id' => 'btn_hide',
					'title' => 'Hide &raquo;'
				)),
				*/
				'CONTENT' => UI::Render('admin/mailer.campaign.recipients.php'),
			));

			UI::set('CONTENT', UI::Render('admin/mailer.campaign.details.php'));

			UI::set('SECTION', 'Campaign Details: ' . $model->subject . ' (' . date(Conf::Get('FORMAT:DATE:SHORT'), strtotime($model->date)) . ')');

			static::Wrapper($columns = 'two');
		}

		static function GET_Stats()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$STATS = Array(
				'processed' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status > 0, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'sent' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status = 1, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'errors' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status > 1, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'bounced' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(status > 2, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'open' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(open > 0, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
				'clicked' => $model->messages->found() ?
					intval(DB::Fetch('SELECT COUNT(IF(open = 2, 1, NULL)) * 100 / COUNT(*) FROM mailer_messages WHERE idcampaign = ? AND status > -1', array($model->id))) : 0,
			);

			print json_encode($STATS);
		}

		static function GET_Send()
		{
			$model = Model::MailerCampaign( intval(Request::URL('id')) );

			$message = Model::MailerMessage();
			$message->where('idcampaign = ? AND status = 0', $model->id)
				->limit(50)->execute();

			if ($message->found()) {
				//
				$message->reset();
				while ($message->next()) {
					//
					$template = str_replace('{$MESSAGE.ID}', $message->id, $model->template);

					if ( Mail::Send($message->email, $model->subject, $template, $mailer = 'mailer') ) {
						//
						$message->status = 1;
						$message->save();
					}
					else {
						//
						$message->status = 2;
						$message->save();
					}
				}
			}
		}

		static function GET_Template()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			print $model->template;
		}

	}

?>