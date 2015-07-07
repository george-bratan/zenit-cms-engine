<?php

	// Models\ContactFlag

	namespace Models;

	class ContactFlag extends \Model {

		static
			$public = Array(
				'name' => 'Flag',
				'color' => 'Color',
			);

		static
			$default = Array(
				'name', 'color',
			);

		static
			$filters = Array(
				'name' => 'Flag Name',
				'color' => 'Color',
			);

		static
			$schema = Array(
				'name' => 'Flag Name',
				'color' => 'Color',
			);


		function __construct($id = NULL)
		{
			parent::__construct('contact_flags');

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