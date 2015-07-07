<?php

	// Models\ShopTax

	namespace Models;

	class ShopTax extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'value' => 'Value',
			);

		static
			$default = Array(
				'name', 'value',
			);

		static
			$filters = Array(
				'name' => 'Tax Name',
				'value' => 'Value',
				'type' => 'Type',
			);

		static
			$schema = Array(
				'name' => 'Tax Name',
				'value' => 'Value',
				'type' => 'Type',
			);


		function __construct($id = NULL)
		{
			parent::__construct('shop_taxes');

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