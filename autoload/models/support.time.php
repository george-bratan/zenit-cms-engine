<?php

	// Models\SupportTime

	namespace Models;

	class SupportTime extends \Model {

		static
			$public = Array(
				'user' => 'User',
				'company' => 'Company',
				'ticket' => 'Ticket',
				'time' => 'Time',
				'date' => 'Logged on',
			);

		static
			$default = Array(
				'user', 'company', 'ticket', 'time', 'date',
			);

		static
			$filters = Array(
				'user' => 'User',
				'company' => 'Company',
				'ticket' => 'Ticket',
				'time' => 'Time',
				'date' => 'Logged on',
			);

		static
			$schema = Array(
				'user' => 'User',
				'company' => 'Company',
				'ticket' => 'Ticket',
				'date' => 'Logged on',
				'time' => 'Time',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('support_times');

			$this->def('user', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM support_users WHERE id = iduser)");
			$this->def('company', '(SELECT name FROM support_companies WHERE id = idcompany)');
			$this->def('ticket', '(SELECT subject FROM support_tickets WHERE id = idticket)');
			$this->def('time', "CONCAT(hours, ':', minutes)");

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function defaults()
		{
			parent::defaults();

			$this->iduser = \Session::Get('SUPPORT.ID');
			$this->idcompany = \Session::Get('SUPPORT.COMPANY.ID');

			$this->date = '@NOW()';
			$this->status = 0;
		}

		function filter()
		{
			parent::filter();

			if (\Session::Exists('SUPPORT.COMPANY.ID')) {
				$this->where('idcompany = ?',
					\Session::Get('SUPPORT.COMPANY.ID'));
			}

			return $this;
		}

	}

?>