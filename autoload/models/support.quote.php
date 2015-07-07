<?php

	// Models\SupportQuote

	namespace Models;

	class SupportQuote extends \Model {

		static
			$public = Array(
				'from' => 'From',
				'to' => 'To',
				'ticket' => 'Ticket',
				'time' => 'Time',
				'date' => 'Logged on',
			);

		static
			$default = Array(
				'from', 'to', 'ticket', 'time', 'date',
			);

		static
			$filters = Array(
				'from' => 'From',
				'to' => 'To',
				'ticket' => 'Ticket',
				'date' => 'Logged on',
				'time' => 'Time',
			);

		static
			$schema = Array(
				'from' => 'From',
				'to' => 'To',
				'ticket' => 'Ticket',
				'date' => 'Logged on',
				'time' => 'Time',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('support_quotes');

			$this->def('from', '(SELECT name FROM support_companies WHERE id = idfrom)');
			$this->def('to', '(SELECT name FROM support_companies WHERE id = idto)');
			$this->def('ticket', '(SELECT subject FROM support_tickets WHERE id = idticket)');
			$this->def('time', "CONCAT(hours, ':', minutes)");
			$this->def('paid', '(SELECT status FROM support_invoices WHERE id = (SELECT idinvoice FROM support_invoices_quotes WHERE idquote = M.id))');

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

			$this->idfrom = \Session::Get('SUPPORT.COMPANY.ID');

			$this->date = '@NOW()';
			$this->status = 0;
		}

		function filter()
		{
			parent::filter();

			if (\Session::Exists('SUPPORT.COMPANY.ID')) {
				$this->where('(idfrom = ? OR idto = ?)',
					\Session::Get('SUPPORT.COMPANY.ID'), \Session::Get('SUPPORT.COMPANY.ID'));
			}

			return $this;
		}

	}

?>