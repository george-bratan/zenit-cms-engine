<?php

	// Models\MailerCategory

	namespace Models;

	class MailerCategory extends \Model {

		static
			$public = Array(
				'name' => 'Name',
			);

		static
			$default = Array(
				'name',
			);

		static
			$filters = Array(
				'name' => 'Category Name',
			);

		static
			$schema = Array(
				'name' => 'Category Name',
			);


		function __construct($id = NULL)
		{
			parent::__construct('mailer_categories');

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