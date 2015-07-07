<?php

	// Models\CrmContact

	namespace Models;

	class CrmContact extends \Model {

		static
			$public = Array(
				'fullname' => 'Full Name',
				'id' => 'VCard',
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'companyname' => 'Company',
				'position' => 'Position',
				'email' => 'Email Address',
				'phone' => 'Phone Number',
				'labels' => 'Labels',
				//'notes' => 'Notes',
				'city' => 'City',
				'country' => 'Country',
				'postcode' => 'Postcode',
			);

		static
			$default = Array(
				'fullname', 'id', 'email', 'phone', 'labels', 'companyname',
			);

		static
			$filters = Array(
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'companyname' => 'Company',
				'email' => 'Email Address',
				'city' => 'City',
				'country' => 'Country',
				//'notes' => 'Number of Notes',
			);

		static
			$schema = Array(
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'idcompany' => 'Company',
				'position' => 'Position',
				'email' => 'Email Address',
				'phone' => 'Phone Number',
				//
				'idpostal' => 'Postal Address',
				'idbilling' => 'Billing Address',
				'idshipping' => 'Shipping Address',
				//
				'address' => 'Address',
				'city' => 'City',
				'country' => 'Country',
				'postcode' => 'Postcode',
			);

		function __construct($id = NULL)
		{
			parent::__construct('crm_contacts');

			$this->def('name');
			$this->def('companyname', '(SELECT name FROM crm_companies WHERE id = idcompany)');
			$this->def('fullname', "IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname))");

			$this->def('company');
			$this->def('labels');
			$this->def('notes');

			$this->def('billing');
			$this->def('shipping');
			$this->def('postal');
			$this->def('addresses');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_company()
		{
			$company = new CrmCompany( $this->idcompany );

			return $company;
		}

		function get_labels()
		{
			$labels = new CrmLabel();
			$labels->where('status > 0 AND id IN (SELECT idlabel FROM crm_contacts_labels WHERE idcontact = ?)', $this->id)
				->execute();

			return $labels;
		}

		function get_notes()
		{
			$notes = new CrmContactNote();
			$notes->where('idcontact = ?', $this->id)
				->order('date ASC')
				->execute();

			return $notes;
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

		function get_billing()
		{
			return new CrmAddress( $this->idbilling );
		}

		function get_shipping()
		{
			return new CrmAddress( $this->idshipping );
		}

		function get_postal()
		{
			return new CrmAddress( $this->idpostal );
		}

		function get_addresses()
		{
			$result = new CrmAddress();
			$result->where('idcontact = ? AND status > -1', $this->id)
				->execute();

			return $result;
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