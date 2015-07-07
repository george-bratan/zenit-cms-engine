<?php

	// Models\MailerContact

	namespace Models;

	class MailerContact extends \Model {

		static
			$public = Array(
				'name' => 'Contact Name',
				'email' => 'Email Address',
				'date' => 'Registration',
				///'category' => 'Category',
			);

		static
			$default = Array(
				'name', 'email', 'date', 'category',
			);

		static
			$filters = Array(
				'name' => 'Contact Name',
				'email' => 'Email Address',
				'date' => 'Registration Date',
				'idcategory' => 'Category',
			);

		static
			$schema = Array(
				'name' => 'Name',
				'email' => 'Email Address',
				//'date' => 'Registration Date',
				'idcategory' => 'Category',
			);

		function __construct($id = NULL)
		{
			parent::__construct('mailer_contacts');

			$this->def('category', '(SELECT name FROM mailer_categories WHERE id = idcategory)');

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

			$this->date = '@NOW()';
			$this->status = 1;
		}

	}

?>