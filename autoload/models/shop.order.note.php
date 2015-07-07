<?php

	// Models\ShopOrderNote

	namespace Models;

	class ShopOrderNote extends \Model {

		static
			$public = Array(
				'admin' => 'Administrator',
				'contact' => 'Contact Name',
				'order' => 'Order',
				'content' => 'Notes',
				'date' => 'Date/Time',
			);

		static
			$default = Array(
				'admin', 'contact', 'order', 'date',
			);

		static
			$filters = Array(
				'admin' => 'Administrator',
				'contact' => 'Contact Name',
				'order' => 'Order #',
				'date' => 'Date Between',
				'content' => 'Containing',
			);

		static
			$schema = Array(
				'idorder' => 'Order',
				'content' => 'Notes',
				'date' => 'Date',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('shop_orders_notes');

			$this->def('admin', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM users WHERE id = idadmin)");
			$this->def('contact', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM crm_contacts WHERE id = ".
				"(SELECT idcontact FROM shop_orders WHERE id = idorder))");

			$this->def('file');

			if ($id)
			{
				$this->where('idorder = ?', $id);
				$this->order('date ASC')->execute();
			}

			return $this;
		}

		function get_file()
		{
			if ($this->idfile) {
				//
				$file = new ShopFile( $this->idfile );
				return $file->record();
			}

			return NULL;
		}

		function defaults()
		{
			parent::defaults();

			$this->idadmin = \Session::Get('ACCOUNT.ID');
			$this->date = '@NOW()';
			$this->status = 1;
		}

	}

?>