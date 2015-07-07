<?php

	// Models\CrmCompany

	namespace Models;

	class CrmCompany extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'phone' => 'Phone',
				'email' => 'Email',
				'size' => 'Size',
				'labels' => 'Labels',
				'city' => 'City',
				'country' => 'Country',
				'postcode' => 'Postcode',
			);

		static
			$default = Array(
				'name', 'phone', 'size', 'labels',
			);

		static
			$filters = Array(
				'name' => 'Company Name',
				'city' => 'City',
				'country' => 'Country',
				'size' => 'Size',
			);

		static
			$schema = Array(
				'name' => 'Company Name',
				'size' => 'Size',
				'phone' => 'Phone',
				'email' => 'Email',
				'url' => 'Website',
				//
				'address' => 'Address',
				'city' => 'City',
				'country' => 'Country',
				'postcode' => 'Postcode',
			);

		function __construct($id = NULL)
		{
			parent::__construct('crm_companies');

			$this->def('contacts');
			$this->def('labels');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_contacts()
		{
			$contacts = new CrmContact();
			$contacts->where('status > 0 AND idcompany = ?', $this->id)
				->execute();

			return $contacts;
		}

		function get_labels()
		{
			$labels = new CrmLabel();
			$labels->where('status > 0 AND id IN (SELECT idlabel FROM crm_companies_labels WHERE idcompany = ?)', $this->id)
				->execute();

			return $labels;
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