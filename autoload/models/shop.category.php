<?php

	// Models\ShopCategory

	namespace Models;

	class ShopCategory extends \Model {

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
			parent::__construct('shop_categories');

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