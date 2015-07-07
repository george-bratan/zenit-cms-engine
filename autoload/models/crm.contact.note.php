<?php

	// Models\CrmContactNote

	namespace Models;

	class CrmContactNote extends \Model {

		static
			$public = Array(
				'admin' => 'Administrator',
				'contact' => 'Contact Name',
				'method' => 'Contact Method',
				'subject' => 'Subject',
				'content' => 'Notes',
				'flagnames' => 'Resolution',
				'date' => 'Date/Time',
			);

		static
			$default = Array(
				'admin', 'contact', 'subject', 'flagnames', 'date',
			);

		static
			$filters = Array(
				'admin' => 'Administrator',
				'contact' => 'Contact Name',
				'date' => 'Date Between',
				'method' => 'Contact Method',
				'subject' => 'Subject',
				'flagnames' => 'Resolution',
				'content' => 'Containing',
			);

		static
			$schema = Array(
				'idcontact' => 'Contact',
				'method' => 'Contact Method',
				'idsubject' => 'Contact Reason',
				'flags' => 'Resolution',
				'content' => 'Notes',
				'date' => 'Date',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('crm_contacts_notes');

			$this->def('admin', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM users WHERE id = idadmin)");
			$this->def('contact', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM crm_contacts WHERE id = idcontact)");
			$this->def('subject', "(SELECT name FROM crm_labels WHERE id = idsubject)");

			$this->def('flags');
			$this->def('flagnames', "(SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM crm_labels WHERE id IN (SELECT idflag FROM crm_contacts_notes_flags WHERE idnote = M.id))");

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

		function get_flags($refresh = false)
		{
			if (!isset($this->custom[$this->index]['flags'])) {
				$this->custom[$this->index]['flags'] =
					\DB::Column('SELECT idflag FROM crm_contacts_notes_flags WHERE idnote = ?', array($this->id));
			}

			if ($refresh) {
				return \DB::Column('SELECT idflag FROM crm_contacts_notes_flags WHERE idnote = ?', array($this->id));
			}

			return $this->custom[$this->index]['flags'];
		}

		function set_flags($value)
		{
			$this->custom[$this->index]['flags'] = array_unique($value);
		}

		/*
		function get_flagnames()
		{
			return \DB::Fetch("SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM crm_labels WHERE id IN ??", array($this->flags));
		}
		*/

		function save($delayed = false)
		{
			parent::save($delayed);

			if ($this->flags != $this->get_flags($refresh = true)) {
				//
				$sql = new \SQL();
				$sql->delete('crm_contacts_notes_flags')
					->where('idnote = ?', $this->id)->execute();

				foreach ($this->flags as $flag) {
					//
					$sql = new \SQL();
					$sql->insert('crm_contacts_notes_flags')
						->set('idnote = ?', $this->id)
						->set('idflag = ?', $flag)
						->execute();
				}
			}
		}

		function filter()
		{
			parent::filter();

			//

			return $this;
		}

	}

?>