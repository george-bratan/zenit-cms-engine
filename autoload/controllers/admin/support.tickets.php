<?php

	// SupportTickets

	class SupportTickets extends AdminModule
	{
		static
			$TITLE  = 'Tickets',
			$IDENT  = 'support.tickets';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/comments.png',
				'LARGE' => 'icon.large/comments.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Tickets',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'support.tickets';

		static
			$TYPES = Array(
				0 => '-',
				1 => 'Support',
				2 => 'Bug',
				3 => 'Change Request',
				4 => 'New Development',
				5 => 'Quotation',
				6 => 'Accounts',
			);

		static
			$PRIORITY = Array(
				'0' => 'Low',
				'1' => 'Medium',
				'2' => 'High',
				'3' => 'Urgent',
			);

		static
			$STATUS = Array(
				'0' => 'Open',
				'1' => 'Solved',
				'2' => 'Closed',
			);

		static
			$QSTATUS = Array(
				'0' => 'Pending',
				'1' => 'Accepted',
				'2' => 'Rejected',
			);


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function Model($id = NULL)
		{
			return Model::SupportTicket($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('BULK.enable', 'Solved');
			UI::set('BULK.disable', 'Open');

			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}


			UI::set('FORMAT.subject', function($record) {
				//
				$read = DB::Fetch("SELECT COUNT(*) FROM support_tickets_viewed WHERE idticket = ? AND iduser = ?", array($record['id'], Session::Get('SUPPORT.ID')));
				return $record['subject'] . ($read ? '' : '<span style="color:red"> (New)</span>');
			});

			UI::set('FORMAT.id', function($record) {
				//
				return '#'.$record['id'];
			});

			UI::set('FORMAT.comments', function($record) {
				//
				return ($record['new'] ? ' <span style="color:red">'.$record['new'].'</span> / ' : '').
					$record['comments'];
			});

			UI::set('FORMAT.type', function($record) {
				//
				return SupportTickets::$TYPES[ $record['type'] ];
			});

			UI::set('FORMAT.priority', function($record) {
				//
				if ($record['priority']) {
					return '<span style="color:'.($record['priority'] == 1 ? 'green' : 'red').'">'.
							SupportTickets::$PRIORITY[ $record['priority'] ].'</span>';
				}

				return SupportTickets::$PRIORITY[ $record['priority'] ];
			});

			UI::set('FORMAT.status', function($record) {
				//
				return '<span style="color:'.($record['status'] ? ($record['status'] == 1 ? 'green' : 'black') : 'red').'">'.
							SupportTickets::$STATUS[ $record['status'] ].'</span>';
			});

			parent::GET_List($page);
		}

		static function POST_Save()
		{
			$ticket = Model::SupportTicket( intval( Request::URL('id') ) );

			if (!Request::URL('id')) {
				$ticket->defaults();
			}

			$params = Request::POST('VALUES');
			$ticket->subject = $params['subject'];
			$ticket->details = $params['details'];
			$ticket->type = $params['type'];
			$ticket->companies = $params['company'] ? array(Session::Get('SUPPORT.COMPANY.ID'), $params['company']) : array(Session::Get('SUPPORT.COMPANY.ID'));

			$ticket->save();

			if (!Request::URL('id')) {
				// public
				$thread = Model::SupportThread();
				$thread->defaults();
				$thread->idticket = $ticket->id;
				$thread->companies = array();

				$thread->save();

				// private
				$thread = Model::SupportThread();
				$thread->defaults();
				$thread->idticket = $ticket->id;
				$thread->companies = array( $ticket->idcompany );

				$thread->save();

				self::POST_File( $ticket->id );
			}
		}

		static function GET_File()
		{
			$fields = Array();
			$model = static::Model();

			// FILE
			$input = new Input('file');
			$input->Type(Input::F_FILE)->Title( $model::$schema['file'] )->Context('');

			$fields['file'] = $input; //->Export();

			UI::set('FIELDS', $fields);

			UI::Set('TITLE', 'Attach File');
			UI::Set('TARGET', Request::$URL . '/file/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/.shared.edit.php'));

			self::Popup();
		}

		static function POST_File($ticket = NULL)
		{
			if (!$ticket) {
				//
				$ticket = intval( Request::URL('id') );
			}

			// file
			$file = self::Upload('file');

			if ($file) {
				//
				$sql = new SQL();
				$sql->insert('support_tickets_files')
					->set('idticket = ?', $ticket)
					->set('idfile = ?', $file->id)
					->execute();
			}
		}

		static function Upload($file)
		{
			$temp = File::Temporary($file, 'support/tmp');

			if ($temp) {
				//
				$file = Model::SupportFile();
				$file->defaults();

				$file->original = $temp['name'];
				$file->file = $temp['server_name'];
				$file->save();

				$perm = File::Permanent($temp, 'support/file', $file->id);
				$file->original = $perm['name'];
				$file->file = $perm['server_name'];
				$file->save();

				return $file;
			}

			return NULL;
		}

		static function GET_Download()
		{
			$file = Model::SupportFile( intval(Request::URL('id')) );

			File::Send( $file->path, $file->original );
		}

		static function GET_Details()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$ticket = $model->record();
			$ticket['type'] = self::$TYPES[ $model->type ];

			UI::set('TICKET', $ticket);

			$threads = Model::SupportThread();
			$threads->where('status = 1 AND idticket = ?', $model->id)
				->execute();

			UI::set('THREADS', $threads->export());

			$quotes = Model::SupportQuote();
			$quotes->where('status > -1 AND idticket = ? AND (idfrom = ? OR idto = ?)', $model->id, Session::Get('SUPPORT.COMPANY.ID'), Session::Get('SUPPORT.COMPANY.ID'))
				->execute();
			UI::set('QUOTES', $quotes->export());
			UI::set('QSTATUS', self::$QSTATUS);

			$times = Model::SupportTime();
			$times->where('status > -1 AND idticket = ? AND idcompany = ?', $model->id, Session::Get('SUPPORT.COMPANY.ID'))
				->execute();
			UI::set('TIMES', $times->export());

			UI::set('SUPPORT', Session::Get('SUPPORT'));
			UI::set('STATUS', self::$STATUS);
			UI::set('PRIORITY', self::$PRIORITY);

			UI::set('FOOTER', UI::Render('admin/support.ticket.threads.php'));
			UI::set('CONTENT', UI::Render('admin/support.ticket.details.php'));

			UI::set('SECTION', 'Ticket #'.$model->id);

			self::Viewed($model->id);

			self::Wrapper();
		}

		static function Viewed($ticket)
		{
			$sql = new SQL();
			$sql->delete('support_tickets_viewed')
				->where('idticket = ? AND iduser = ?', $ticket, Session::Get('SUPPORT.ID'))
				->execute();

			$sql = new SQL();
			$sql->insert('support_tickets_viewed')
				->set('idticket = ?, iduser = ?, date = NOW()', $ticket, Session::Get('SUPPORT.ID'))
				->execute();
		}

		static function POST_Comment()
		{
			$thread = Model::SupportThread( intval(Request::URL('id')) );

			$comment = Model::SupportComment();
			$comment->defaults();

			$comment->idthread = $thread->id;
			$comment->content = Request::POST('VALUES.content');

			$file = self::Upload('file');

			if ($file) {
				//
				$comment->idfile = $file->id;
			}

			$comment->save();

			// NOTIFICATION

			$DATA = array(
				'TICKET' => $thread->ticket->record(),
				'COMMENTS' => $thread->comments,
				'URL' => 'http://'.Request::$HOST.'/admin/support/tickets/details/'. $thread->ticket->id,
			);

			$subject = 'Ticket #'. ($thread->ticket->id) .': '. ($thread->ticket->subject);
			$message = UI::Render('admin/support.ticket.notification.php', $DATA);

			$users = Model::SupportUser();
			$users->where('status = 1 AND idcompany IN ??', $thread->ticket->companies)
				->execute();

			if ($users->found()) {
				//
				$users->reset();
				while ($users->next()) {
					//
					if ($users->notifications && $users->email != Session::Get('SUPPORT.EMAIL')) {
						//
						Mail::Send($users->email, $subject, $message);
					}
				}
			}
		}

		static function GET_NewThread()
		{
			$ticket = self::Model( intval(Request::URL('id')) );

			$companies = Model::SupportCompany();
			$companies->where('id IN ??', $ticket->companies)->execute();

			UI::Set('TARGET', Request::$URL . '/newthread/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $companies->export());

			UI::Set('TITLE', 'New Thread');
			UI::Set('CONTENT', 'Select the companies who will have access to this thread:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_NewThread()
		{
			$items = Request::POST('items');

			$thread = Model::SupportThread();
			$thread->defaults();
			$thread->idticket = intval(Request::URL('id'));
			$thread->companies = array_merge( $items, array(Session::Get('SUPPORT.COMPANY.ID')) );

			$thread->save();
		}

		static function GET_Invite()
		{
			$ticket = self::Model( intval(Request::URL('id')) );

			$companies = Model::SupportCompany();
			$companies->execute();

			UI::Set('TARGET', Request::$URL . '/invite/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $companies->export());
			UI::Set('SELECTED', $ticket->companies);

			UI::Set('TITLE', 'Invite other companies');
			UI::Set('CONTENT', 'Select the companies you want to allow access:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Invite()
		{
			$items = Request::POST('items');

			$ticket = self::Model( intval(Request::URL('id')) );
			$ticket->companies = $items ? array_merge( array(Session::Get('SUPPORT.COMPANY.ID')), $items ) : array(Session::Get('SUPPORT.COMPANY.ID'));

			$ticket->save();

			$comment = Model::SupportComment();
			$comment->defaults();
			$comment->idthread = $ticket->public;
			$comment->content = 'Companies invited: '.$ticket->companynames;
			$comment->iduser = 0;
			$comment->idcompany = 0;
			$comment->save();
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('status') !== NULL) {
				//
				$model->status = Request::POST('status');

				if ($model->status == 1) {
					$model->solved = '@NOW()';
				}

				if ($model->status == 2) {
					$model->closed = '@NOW()';
				}
			}
			else
				$model->status = ($model->status == 1) ? 0 : 1;

			$model->save();

			$comment = Model::SupportComment();
			$comment->defaults();
			$comment->idthread = $model->public;
			$comment->content = 'Status: '.self::$STATUS[ $model->status ];
			$comment->iduser = 0;
			$comment->idcompany = 0;
			$comment->save();
		}

		static function MultiSetStatus($items, $status)
		{
			$model = static::Model();
			$model->where("id IN ??", $items)
				->execute();

			$model->reset();
			while ($model->next()) {
				//
				$model->status = $status;

				if ($model->status == 1) {
					$model->solved = '@NOW()';
				}

				if ($model->status == 2) {
					$model->closed = '@NOW()';
				}

				$model->save();
			}
		}

		static function POST_Delivery()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('delivery') !== NULL) {
				//
				$model->delivery = Request::POST('delivery');
			}

			$model->save();

			$comment = Model::SupportComment();
			$comment->defaults();
			$comment->idthread = $model->public;
			$comment->content = 'New Delivery Date: '.$model->delivery;
			$comment->iduser = 0;
			$comment->idcompany = 0;
			$comment->save();
		}

		static function POST_Priority()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('priority') == 'increase' && isset(self::$PRIORITY[ $model->priority+1 ])) {
				//
				$model->priority = $model->priority + 1;
			}
			if (Request::POST('priority') == 'decrease' && isset(self::$PRIORITY[ $model->priority-1 ])) {
				//
				$model->priority = $model->priority - 1;
			}

			$model->save();

			$comment = Model::SupportComment();
			$comment->defaults();
			$comment->idthread = $model->public;
			$comment->content = 'Priority Changed: '.self::$PRIORITY[ $model->priority ];
			$comment->iduser = 0;
			$comment->idcompany = 0;
			$comment->save();
		}

		static function POST_Accept()
		{
			$quote = Model::SupportQuote( intval( Request::URL('id') ) );
			$quote->status = intval( Request::POST('status') );
			$quote->save();
		}

		static function GET_Quote()
		{
			$fields = Array();

			$ticket = self::Model( intval(Request::URL('id')) );
			$companies = DB::AssociativeColumn("SELECT id, name FROM support_companies WHERE id IN ?? AND id != ?", array( $ticket->companies, Session::Get('SUPPORT.COMPANY.ID') ));

			// TO
			$input = new Input('idto');
			$input->Type(Input::F_SELECT)->Title( 'Send Quote To' )->Context('VALUES')->Options( $companies );

			$fields['idto'] = $input; //->Export();

			// AMOUNT
			$input = new Input('amount');
			$input->Type(Input::F_RANGE)->Title( 'Amount/Quantity' )->Context('VALUES')->Width('50px')->Details('hours/minutes');

			$fields['amount'] = $input; //->Export();

			// DETAILS
			$input = new Input('details');
			$input->Type(Input::F_LONGTEXT)->Title( 'Details' )->Context('VALUES');

			$fields['details'] = $input; //->Export();


			UI::set('FIELDS', $fields);

			UI::Set('TITLE', 'Submit New Quote');
			UI::Set('TARGET', Request::$URL . '/quote/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/.shared.edit.php'));

			self::Popup();
		}

		static function POST_Quote()
		{
			$quote = Model::SupportQuote();
			$quote->defaults();

			$quote->idticket = intval( Request::URL('id') );
			$quote->idfrom = Session::Get('SUPPORT.COMPANY.ID');
			$quote->idto = Request::POST('VALUES.idto');
			$quote->amount = Request::POST('VALUES.amount.0').':'.Request::POST('VALUES.amount.1');
			$quote->hours = Request::POST('VALUES.amount.0');
			$quote->minutes = Request::POST('VALUES.amount.1');
			$quote->details = Request::POST('VALUES.details');

			$quote->save();
		}

		static function GET_Time()
		{
			$fields = Array();

			// AMOUNT
			$input = new Input('amount');
			$input->Type(Input::F_RANGE)->Title( 'Amount/Quantity' )->Context('VALUES')->Width('50px')->Details('hours/minutes');

			$fields['amount'] = $input; //->Export();

			// DETAILS
			$input = new Input('details');
			$input->Type(Input::F_LONGTEXT)->Title( 'Details' )->Context('VALUES');

			$fields['details'] = $input; //->Export();


			UI::set('FIELDS', $fields);

			UI::Set('TITLE', 'Submit New Time Entry');
			UI::Set('TARGET', Request::$URL . '/time/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/.shared.edit.php'));

			self::Popup();
		}

		static function POST_Time()
		{
			$time = Model::SupportTime();
			$time->defaults();

			$time->idticket = intval( Request::URL('id') );
			$time->amount = Request::POST('VALUES.amount.0').':'.Request::POST('VALUES.amount.1');
			$time->hours = Request::POST('VALUES.amount.0');
			$time->minutes = Request::POST('VALUES.amount.1');
			$time->details = Request::POST('VALUES.details');

			$time->save();
		}

		static function EditForm( $model )
		{
			$fields = Array();

			// SUBJECT
			$input = new Input('subject');
			$input->Type(Input::F_TEXT)->Title( $model::$schema['subject'] )->Context('VALUES')->Value( $model->subject );

			$fields['subject'] = $input; //->Export();

			// TYPE
			$input = new Input('type');
			$input->Type(Input::F_SELECT)->Title( $model::$schema['type'] )->Context('VALUES')->Value( intval($model->type) )->Options( self::$TYPES );

			$fields['type'] = $input; //->Export();

			// COMPANY
			$companies = array(0 => '-');
			$company = Model::SupportCompany();
			$company->execute();

			if ($company->found()) {
				$company->reset();
				while ($company->next()) {
					$companies[ $company->id ] = $company->name;
				}
			}

			$input = new Input('company');
			$input->Type(Input::F_SELECT)->Title( $model::$schema['company'] )->Context('VALUES')
				->Value( count($model->companies) ? $model->companies[0] : 0 )->Options($companies);

			$fields['company'] = $input; //->Export();

			// CONTENT
			$input = new Input('details');
			$input->Type(Input::F_RICHTEXT)->Title( $model::$schema['details'] )->Context('VALUES')->Value( $model->details );

			$fields['details'] = $input; //->Export();

			// FILE
			$input = new Input('file');
			$input->Type(Input::F_FILE)->Title( $model::$schema['file'] )->Context('');

			$fields['file'] = $input; //->Export();


			return $fields;
		}

		static function FilterForm()
		{
			$filters = parent::FilterForm();


			$companies = array(0 => '-');
			$company = Model::SupportCompany();
			$company->execute();

			if ($company->found()) {
				$company->reset();
				while ($company->next()) {
					$companies[ $company->id ] = $company->name;
				}
			}

			$filters['idcompany']->Type(Input::F_SELECT)->Options( $companies );

			$filters['type']->Type(Input::F_SELECT)->Options( self::$TYPES );

			$filters['issued']->Type(Input::F_DATE);

			$filters['delivery']->Type(Input::F_DATE);

			$filters['priority']->Type(Input::F_CHECKGROUP)->Options( self::$PRIORITY );

			return $filters;
		}

		static function Notification()
		{
			$num = DB::Fetch('SELECT COUNT(DISTINCT T.id) FROM support_tickets T LEFT OUTER JOIN support_tickets_viewed V ON V.idticket = T.id AND V.iduser = ? WHERE '.
				'T.`status` > -1 AND V.idticket IS NULL AND T.id IN (SELECT idticket FROM support_tickets_companies WHERE idcompany = ?)', array(Session::Get('SUPPORT.ID'), Session::Get('SUPPORT.COMPANY.ID')));

			return $num;
		}

		static function Timeline( $feed = NULL )
		{
			if (!$feed) {
				return array(
					'tickets' => 'Number of tickets',
					'comments' => 'Number of comments',
				);
			}

			if ($feed == 'tickets') {
				//
				return DB::AssociativeColumn(
					"SELECT DATE(T.issued), COUNT(*) FROM support_tickets AS T ".
						"JOIN support_tickets_companies AS C ON C.idticket = T.id AND C.idcompany = ? ".
					"WHERE T.status > -1 GROUP BY DATE(T.issued) ORDER BY T.issued DESC LIMIT 10", array(Session::Get('SUPPORT.COMPANY.ID')));
			}

			if ($feed == 'comments') {
				//
				$tickets = DB::Column("SELECT id FROM support_tickets WHERE id IN (SELECT idticket FROM support_tickets_companies WHERE idcompany = ?)", array(Session::Get('SUPPORT.COMPANY.ID')));
				$threads = DB::Column("SELECT id FROM support_threads T
					WHERE idticket IN ?? AND
						((SELECT COUNT(*) FROM support_threads_companies C WHERE C.idthread = T.id) = 0
							OR
						(SELECT COUNT(*) FROM support_threads_companies C WHERE C.idthread = T.id AND C.idcompany = ?) > 0)",
					array($tickets, Session::Get('SUPPORT.COMPANY.ID')));

				return DB::AssociativeColumn(
					"SELECT DATE(date), COUNT(*) FROM support_comments ".
					"WHERE idthread in ?? GROUP BY DATE(date) ORDER BY date DESC LIMIT 10", array($threads));
			}
		}

	}

?>