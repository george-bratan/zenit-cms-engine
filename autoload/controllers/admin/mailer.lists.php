<?php

	// MailerLists

	class MailerLists extends AdminModule
	{
		static
			$TITLE  = 'Recipients',
			$IDENT  = 'mailer.lists';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.png',
				'LARGE' => 'icon.large/user.group.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Recipients',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'mailer.list';


		static function Model($id = NULL)
		{
			return Model::MailerList($id);
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

			parent::GET_List($page);
		}

		static function GET_Details($feed = NULL, $params = NULL)
		{
			$model = static::Model( intval( Request::URL('id') ) );

			// temporary store these values, without saving them
			if (Session::Get('TMP.RECIPIENTLIST.FEED') || Session::Get('TMP.RECIPIENTLIST.PARAMS')) {
				$model->feed = Session::Get('TMP.RECIPIENTLIST.FEED');
				$model->params = Session::Get('TMP.RECIPIENTLIST.PARAMS');

				Session::Clear('TMP.RECIPIENTLIST');
			}

			if ($feed || $params) {
				$model->feed = $feed;
				$model->params = $params;
			}

			UI::set('RECIPIENTLIST', $model->record());

			$feeds = Admin::RecipientFeed();
			unset($feeds[ self::$TITLE ]);
			UI::set('FEEDS', array_merge(array('' => '-'), $feeds));

			if ($model->feed) {
				//
				$feed = Admin::RecipientFeed( $model->feed, $model->filters );
				UI::set('FEED', $feed);
			}

			UI::set('CONTENT', UI::Render('admin/mailer.list.details.php'));
			UI::set('TOOLBAR.save', array(
					'id' => 'btn_save',
					'url' => Request::$URL . '/save/'. $model->id,
					'rel' => 'none',
					'icon' => 'icon.small/disk.png',
					'title' => 'Save'
				)
			);

			UI::set('SECTION', 'Recipient List: '.$model->name);

			self::Wrapper();
		}

		static function POST_Feed()
		{
			Session::Set('TMP.RECIPIENTLIST.FEED', Request::POST('VALUES.feed'));
			Session::Set('TMP.RECIPIENTLIST.PARAMS', '');
		}

		static function POST_Refresh()
		{
			Session::Set('TMP.RECIPIENTLIST.FEED', Request::POST('VALUES.feed'));
			Session::Set('TMP.RECIPIENTLIST.PARAMS', http_build_query( Request::POST('FILTERS') ));
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if (Request::POST('VALUES.feed') !== null) {
				$model->feed = Request::POST('VALUES.feed');
			}

			if (Request::POST('FILTERS')) {
				//
				$model->params = http_build_query( Request::POST('FILTERS') );
			}

			$model->save();

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			return $fields;
		}

		static function RecipientFeed( $feed = NULL, $filters = NULL )
		{
			if (!$feed) {
				//
				return DB::AssociativeColumn("SELECT CONCAT('MAILER.LIST.', id), name FROM mailer_lists WHERE status > -1");
			}

			$id = str_replace('MAILER.LIST.', '', $feed);

			$model = static::Model( intval( $id ) );

			return Admin::RecipientFeed($model->feed, $model->filters);
		}

	}

?>