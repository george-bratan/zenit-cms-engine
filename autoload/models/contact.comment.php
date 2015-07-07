<?php

	// Models\ContactComment

	namespace Models;

	class ContactComment extends \Model {

		static
			$public = Array(
				'admin' => 'Administrator',
				'contact' => 'Contact Name',
				'content' => 'Comment',
				'date' => 'Date/Time',
			);

		static
			$default = Array(
				'admin', 'contact', 'content', 'date',
			);

		static
			$filters = Array(
				'admin' => 'Administrator',
				'contact' => 'Contact Name',
				'date' => 'Date Between',
				'content' => 'Containing',
			);

		static
			$schema = Array(
				'idmessage' => 'Message',
				'content' => 'Content',
				'date' => 'Date',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('contact_comments');

			$this->def('admin', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM users WHERE id = idadmin)");
			$this->def('contact', "(SELECT name FROM contact_messages WHERE id = idmessage)");

			if ($id)
			{
				$this->where('idmessage = ?', $id);
				$this->order('date ASC')->execute();
			}

			return $this;
		}

		function defaults()
		{
			parent::defaults();

			$this->idadmin = 0;
			$this->date = '@NOW()';
			$this->status = 1;
		}

	}

?>