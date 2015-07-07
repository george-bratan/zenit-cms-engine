<?php

	// Models\ContactMessage

	namespace Models;

	class ContactMessage extends \Model {

		static
			$public = Array(
				'name' => 'Contact Name',
				'subject' => 'Subject',
				'email' => 'Email',
				'date' => 'Date/Time',
				'flags' => 'Flags',
			);

		static
			$default = Array(
				'name', 'subject', 'email', 'phone', 'date', 'flags',
			);

		static
			$filters = Array(
				'name' => 'Contact Name',
				'email' => 'Email',
				'subject' => 'Subject',
				'date' => 'Date Between',
			);

		static
			$schema = Array(
				'idsubject' => 'Subject',
				'name' => 'Contact Name',
				'email' => 'Email',
				'phone' => 'Phone',
				'message' => 'Message',
			);


		function __construct($id = NULL)
		{
			parent::__construct('contact_messages');

			$this->def('subject', "(SELECT name FROM contact_subjects WHERE id = idsubject)");
			$this->def('comments');
			$this->def('flags');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_comments()
		{
			$model = new ContactComment();
			$model->where('idmessage = ?', $this->id)
				->order('date ASC')->execute();

			return $model;
		}

		function get_flags()
		{
			$model = new ContactFlag();
			$model->where('id IN ??', \DB::Column('SELECT DISTINCT idflag FROM contact_messages_flags WHERE idmessage = ?', array($this->id)))
				->execute();

			return $model;
		}

		function defaults()
		{
			parent::defaults();

			$this->date = '@NOW()';
			$this->status = 0;
		}

	}

?>