<?php

	// Models\SupportCompany

	namespace Models;

	class SupportCompany extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'email' => 'Email',
			);

		static
			$default = Array(
				'name', 'email',
			);

		static
			$filters = Array(
				'name' => 'Company Name',
				'email' => 'Email',
			);

		static
			$schema = Array(
				'name' => 'Company Name',
				'email' => 'Email',
			);

		function __construct($id = NULL)
		{
			parent::__construct('support_companies');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function defaults()
		{
			parent::defaults();

			$this->date = '@NOW()';
			$this->status = 1;
		}

		function filter()
		{
			parent::filter();

			if (\Session::Exists('SUPPORT.COMPANY.ID')) {
				$this->where('id IN (SELECT ? UNION SELECT idcompany1 FROM support_companies_companies WHERE idcompany2 = ? UNION SELECT idcompany2 FROM support_companies_companies WHERE idcompany1 = ?)',
					\Session::Get('SUPPORT.COMPANY.ID'), \Session::Get('SUPPORT.COMPANY.ID'), \Session::Get('SUPPORT.COMPANY.ID'));
			}

			return $this;
		}

	}

?>