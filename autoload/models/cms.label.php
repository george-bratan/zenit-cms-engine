<?php

	// Models\CmsLabel

	namespace Models;

	class CmsLabel extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'color' => 'Color',
			);

		static
			$default = Array(
				'name', 'color',
			);

		static
			$filters = Array(
				'name' => 'Label Name',
			);

		static
			$schema = Array(
				'name' => 'Label Name',
				'color' => 'Color',
			);


		function __construct($id = NULL)
		{
			parent::__construct('cms_labels');

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