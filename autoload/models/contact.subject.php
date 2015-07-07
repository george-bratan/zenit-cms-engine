<?php

	// Models\ContactSubject

	namespace Models;

	class ContactSubject extends \Model {

		static
			$public = Array(
				'name' => 'Subject',
			);

		static
			$default = Array(
				'name',
			);

		static
			$filters = Array(
				'name' => 'Subject',
			);

		static
			$schema = Array(
				'name' => 'Subject',
			);


		function __construct($id = NULL)
		{
			parent::__construct('contact_subjects');

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

	}

?>