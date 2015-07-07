<?php

	// Models\SupportInvoice

	namespace Models;

	class SupportInvoice extends \Model {

		static
			$public = Array(
				'name' => 'Identifier',
				'from' => 'From',
				'to' => 'To',
				'start' => 'Start Date',
				'end' => 'End Date',
				'date' => 'Submitted on',
			);

		static
			$default = Array(
				'name', 'from', 'to', 'date',
			);

		static
			$filters = Array(
				'name' => 'Identifier',
				'idfrom' => 'From',
				'idto' => 'To',
				'start' => 'Start Date',
				'end' => 'End Date',
				'date' => 'Submitted on',
			);

		static
			$schema = Array(
				//'name' => 'Identifier',
				//'from' => 'From',
				'series' => 'Invoice Series',
				'index' => 'Invoice Index',
				'idto' => 'Submit To',
				'start' => 'Start Date',
				'end' => 'End Date',
				//'date' => 'Submitted on',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('support_invoices');

			$this->def('from', '(SELECT name FROM support_companies WHERE id = idfrom)');
			$this->def('to', '(SELECT name FROM support_companies WHERE id = idto)');
			$this->def('name', "CONCAT(`series`, '-', `index`)");
			$this->def('quotes');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_quotes()
		{
			$result = new SupportQuote();
			$result->where('id IN (SELECT idquote FROM support_invoices_quotes WHERE idinvoice = ?)', $this->id)
				->execute();

			return $result;
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