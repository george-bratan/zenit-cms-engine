<?php

	// Models\ShopOrderProduct

	namespace Models;

	class ShopOrderProduct extends \Model {

		static
			$schema = Array(
				'idorder' => 'Order',
				'quantity' => 'Quantity',
				'price' => 'Price',
			);


		function __construct($id = NULL)
		{
			parent::__construct('shop_orders_products');

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

			//$this->status = 1;
		}

	}

?>