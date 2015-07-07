<?php

	// Models\SupportTicket

	namespace Models;

	class SupportTicket extends \Model {

		static
			$public = Array(
				'id' => 'Ref. #',
				'subject' => 'Subject',
				'company' => 'Company',
				'type' => 'Type',
				'priority' => 'Priority',
				'issued' => 'Issue Date',
				'delivery' => 'Delivery Date',
				'solved' => 'Solve Date',
				'closed' => 'Close Date',
				'comments' => 'Comments',
			);

		static
			$default = Array(
				'subject', 'company', 'type', 'priority', 'issued', 'comments'
			);

		static
			$filters = Array(
				'subject' => 'Subject',
				'idcompany' => 'Company',
				'type' => 'Type',
				'issued' => 'Issue Date',
				'delivery' => 'Delivery Date',
				'priority' => 'Priority',
			);

		static
			$schema = Array(
				'subject' => 'Subject',
				'type' => 'Type',
				'company' => 'Submit Ticket to',
				'details' => 'Details',
				'priority' => 'Priority',
				'file' => 'Attach File',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('support_tickets');

			$this->def('user', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM support_users WHERE id = iduser)");
			$this->def('company', '(SELECT name FROM support_companies WHERE id = idcompany)');

			$this->def('new');
			$this->def('comments');
			$this->def('files');
			$this->def('companies');
			$this->def('companynames');
			$this->def('public');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_name()
		{
			return $this->subject;
		}

		function get_new()
		{
			/*
			$threads = new SupportThread();
			$threads->where('status = 1 AND idticket = ?', $this->id)
				->execute();
			$threads = Util::Column($threads->export(), 'id');
			*/

			$threads = \DB::Column('SELECT id FROM support_threads WHERE '.
				' (id IN (SELECT idthread FROM support_threads_companies WHERE idcompany = ?) OR (SELECT COUNT(*) FROM support_threads_companies WHERE idthread = id) = 0)'.
				' AND status = 1 AND idticket = ? ', array(\Session::Get('SUPPORT.COMPANY.ID'), $this->id));
			$viewed = \DB::Fetch('SELECT date FROM support_tickets_viewed WHERE idticket = ? AND iduser = ?', array($this->id, \Session::Get('SUPPORT.ID')));

			return $viewed ?
				\DB::Fetch('SELECT COUNT(*) FROM support_comments WHERE idthread IN ?? AND date > ?', array($threads, $viewed)) :
				\DB::Fetch('SELECT COUNT(*) FROM support_comments WHERE idthread IN ??', array($threads));
		}

		function get_comments()
		{
			$threads = \DB::Column('SELECT id FROM support_threads WHERE '.
				' (id IN (SELECT idthread FROM support_threads_companies WHERE idcompany = ?) OR (SELECT COUNT(*) FROM support_threads_companies WHERE idthread = id) = 0)'.
				' AND status = 1 AND idticket = ? ', array(\Session::Get('SUPPORT.COMPANY.ID'), $this->id));

			return \DB::Fetch('SELECT COUNT(*) FROM support_comments WHERE idthread IN ??', array($threads));
		}

		function get_companies($refresh = false)
		{
			if (!isset($this->custom[$this->index]['companies'])) {
				$this->custom[$this->index]['companies'] =
					\DB::Column('SELECT idcompany FROM support_tickets_companies WHERE idticket = ?', array($this->id));
			}

			if ($refresh) {
				return \DB::Column('SELECT idcompany FROM support_tickets_companies WHERE idticket = ?', array($this->id));
			}

			return $this->custom[$this->index]['companies'];
		}

		function set_companies($value)
		{
			$this->custom[$this->index]['companies'] = array_unique($value);
		}

		function get_companynames()
		{
			return \DB::Fetch("SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM support_companies WHERE id IN ??", array($this->companies));
		}

		function get_public()
		{
			return \DB::Fetch('SELECT id FROM support_threads WHERE idticket = ? AND (SELECT COUNT(*) FROM support_threads_companies WHERE idthread = id) = 0', array($this->id));
		}

		function get_files()
		{
			$files = new SupportFile();
			$files->where('id IN ??', \DB::Column("SELECT idfile FROM support_tickets_files WHERE idticket = ?", array($this->id)))
				->execute();

			return $files->export();
		}

		function defaults()
		{
			parent::defaults();

			$this->iduser = \Session::Get('SUPPORT.ID');
			$this->idcompany = \Session::Get('SUPPORT.COMPANY.ID');

			$this->issued = '@NOW()';
			$this->status = 0;
		}

		function save($delayed = false)
		{
			parent::save($delayed);

			if ($this->companies != $this->get_companies($refresh = true)) {
				//
				$sql = new \SQL();
				$sql->delete('support_tickets_companies')
					->where('idticket = ?', $this->id)->execute();

				foreach ($this->companies as $company) {
					//
					$sql = new \SQL();
					$sql->insert('support_tickets_companies')
						->set('idticket = ?', $this->id)
						->set('idcompany = ?', $company)
						->execute();
				}
			}
		}

		function filter()
		{
			parent::filter();

			if (\Session::Exists('SUPPORT.COMPANY.ID')) {
				$this->where('id IN (SELECT idticket FROM support_tickets_companies WHERE idcompany = ?)', \Session::Get('SUPPORT.COMPANY.ID'));
			}

			return $this;
		}

	}

?>