<?php

	// Models\User

	namespace Models;

	class User extends \Model {

		static
			$public = Array(
				'fullname' => 'Full Name',
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'group' => 'Permission Group',
			);

		static
			$default = Array(
				'fullname', 'email', 'group'
			);

		static
			$filters = Array(
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'group' => 'Group',
			);

		static
			$schema = Array(
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'token' => 'Permission Rights',
				'idgroup' => 'Group',
				'pass' => 'Password',
			);

		function __construct($id = NULL)
		{
			parent::__construct('users');

			$this->def('group', '(SELECT name FROM usergroups WHERE id = idgroup)');
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

			$this->status = 1;
		}

	}

?>