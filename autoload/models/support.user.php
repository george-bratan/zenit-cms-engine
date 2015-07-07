<?php

	// Models\SupportUser

	namespace Models;

	class SupportUser extends \Model {

		static
			$public = Array(
				'fullname' => 'Full Name',
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'company' => 'Company',
				'date' => 'Registered',
			);

		static
			$default = Array(
				'fullname', 'email', 'company'
			);

		static
			$filters = Array(
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'company' => 'Company',
			);

		static
			$schema = Array(
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'token' => 'Permission Rights',
				//'idcompany' => 'Company',
				'pass' => 'Password',
			);

		function __construct($id = NULL)
		{
			parent::__construct('support_users');

			$this->def('company', '(SELECT name FROM support_companies WHERE id = idcompany)');
			$this->def('fullname', "IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname))");
			$this->def('name');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_fullname()
		{
			return ($this->firstname && $this->lastname) ?
				$this->lastname.', '.$this->firstname : $this->lastname.$this->firstname;
		}

		function get_name()
		{
			return $this->fullname;
		}

		function defaults()
		{
			parent::defaults();

			$this->token = \Session::Get('SUPPORT.TOKEN');
			$this->idcompany = \Session::Get('SUPPORT.COMPANY.ID');
			$this->date = '@NOW()';
			$this->status = 1;
		}

		function filter()
		{
			parent::filter();

			/*
			if (\Session::Exists('SUPPORT.COMPANY.ID')) {
				$this->where('idcompany = ?', \Session::Get('SUPPORT.COMPANY.ID'));
			}
			*/

			return $this;
		}

	}

?>