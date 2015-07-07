<?php

	// Models\SupportThread

	namespace Models;

	class SupportThread extends \Model {

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('support_threads');

			$this->def('name');
			$this->def('companies');
			$this->def('comments');
			$this->def('ticket');

			$this->def('new');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_new()
		{
			$viewed = \DB::Fetch('SELECT date FROM support_tickets_viewed WHERE idticket = ? AND iduser = ?', array($this->idticket, \Session::Get('SUPPORT.ID')));

			return $viewed ?
				\DB::Fetch('SELECT COUNT(*) FROM support_comments WHERE idthread = ? AND date > ?', array($this->id, $viewed)) :
				\DB::Fetch('SELECT COUNT(*) FROM support_comments WHERE idthread = ?', array($this->id));
		}

		function get_name()
		{
			$companies = $this->companies;
			if (count($companies) > 1) {
				$key = array_search(\Session::Get('SUPPORT.COMPANY.ID'), $companies);
				if ($key !== false) {
					unset($companies[$key]);
				}
			}

			$title = \DB::Fetch("SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM support_companies WHERE id IN ??", array($companies));
			if (!$title) {
				$title = 'Public Thread';
			}

			return $title;
		}

		function get_companies($refresh = false)
		{
			if (!isset($this->custom[$this->index]['companies'])) {
				$this->custom[$this->index]['companies'] =
					\DB::Column('SELECT idcompany FROM support_threads_companies WHERE idthread = ?', array($this->id));
			}

			if ($refresh) {
				return \DB::Column('SELECT idcompany FROM support_threads_companies WHERE idthread = ?', array($this->id));
			}

			return $this->custom[$this->index]['companies'];
		}

		function set_companies($value)
		{
			$this->custom[$this->index]['companies'] = array_unique($value);
		}

		function get_comments()
		{
			$comments = new SupportComment();
			$comments->where('idthread = ?', $this->id)
				->order('date ASC')
				->execute();

			return $comments->export();
		}

		function get_ticket()
		{
			$ticket = new SupportTicket( $this->idticket );

			return $ticket;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

		function save($delayed = false)
		{
			parent::save($delayed);

			if ($this->companies != $this->get_companies($refresh = true)) {
				//
				$sql = new \SQL();
				$sql->delete('support_threads_companies')
					->where('idthread = ?', $this->id)->execute();

				foreach ($this->companies as $company) {
					//
					$sql = new \SQL();
					$sql->insert('support_threads_companies')
						->set('idthread = ?', $this->id)
						->set('idcompany = ?', $company)
						->execute();
				}
			}
		}

		function filter()
		{
			parent::filter();

			if (\Session::Exists('SUPPORT.COMPANY.ID')) {
				$this->where('(id IN (SELECT idthread FROM support_threads_companies WHERE idcompany = ?) OR (SELECT COUNT(*) FROM support_threads_companies WHERE idthread = id) = 0)',
					\Session::Get('SUPPORT.COMPANY.ID'));
			}

			return $this;
		}

	}

?>