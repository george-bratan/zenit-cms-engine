<?php

	// Models\SupportComment

	namespace Models;

	class SupportComment extends \Model {

		function __construct($id = NULL)
		{
			parent::__construct('support_comments');

			$this->def('user', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM support_users WHERE id = iduser)");
			$this->def('company', '(SELECT name FROM support_companies WHERE id = idcompany)');

			$this->def('file');
			$this->def('new');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_new()
		{
			$ticket = \DB::Fetch('SELECT idticket FROM support_threads WHERE id = ?', array($this->idthread));
			$viewed = \DB::Fetch('SELECT date FROM support_tickets_viewed WHERE idticket = ? AND iduser = ?', array($ticket, \Session::Get('SUPPORT.ID')));

			return $viewed ?
				\DB::Fetch('SELECT COUNT(*) FROM support_comments WHERE id = ? AND date > ?', array($this->id, $viewed)) :
				TRUE;
		}

		function get_file()
		{
			if ($this->idfile) {
				//
				$file = new SupportFile( $this->idfile );
				return $file->record();
			}

			return NULL;
		}

		function defaults()
		{
			parent::defaults();

			$this->iduser = \Session::Get('SUPPORT.ID');
			$this->idcompany = \Session::Get('SUPPORT.COMPANY.ID');

			$this->date = '@NOW()';
			$this->idfile = 0;
			$this->status = 1;
		}

	}

?>