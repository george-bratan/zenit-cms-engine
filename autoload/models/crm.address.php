<?php

	// Models\CrmAddress

	namespace Models;

	class CrmAddress extends \Model {

		static
			$public = Array(
				'contact' => 'Contact Name',
				'street' => 'Street',
				'city' => 'City',
				'state' => 'State',
				'country' => 'Country',
				'postcode' => 'Post Code',
			);

		static
			$default = Array(
				'contact', 'street', 'city', 'state',
			);

		static
			$filters = Array(
				'contact' => 'Contact Name',
				'street' => 'Street',
				'city' => 'City',
				'state' => 'State',
				'country' => 'Country',
				'postcode' => 'Post Code',
			);

		static
			$schema = Array(
				'idcontact' => 'Contact',
				'street' => 'Street',
				'city' => 'City',
				'state' => 'State',
				'country' => 'Country',
				'postcode' => 'Post Code',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('crm_addresses');

			$this->def('contact', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM crm_contacts WHERE id = idcontact)");
			$this->def('name', "CONCAT(street, ', ', city, ', ', state)");

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

			$this->status = 1;
		}

		function filter()
		{
			parent::filter();

			//

			return $this;
		}

	}

?>