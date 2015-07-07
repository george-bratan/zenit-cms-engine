<?php

	// Models\ShopDelivery

	namespace Models;

	class ShopDelivery extends \Model {

		static
			$public = Array(
				'order' => 'Order',
				'scheduled' => 'Date Scheduled',
				'date' => 'Sent on',
				'number' => 'Number',
			);

		static
			$default = Array(
				'order', 'scheduled', 'date',
			);

		static
			$filters = Array(
				'scheduled' => 'Scheduled date',
				'date' => 'Send Date',
				'order' => 'Order Number',
			);

		static
			$schema = Array(
				'scheduled' => 'Date Scheduled',
				'idorder' => 'Order',
				'date' => 'Sent on',
				'number' => 'Number',
				'status' => 'Status',
			);

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('shop_deliveries');

			$this->def('client');
			$this->def('order');
			$this->def('name');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->order('number ASC')->execute();
			}

			return $this;
		}

		function get_order()
		{
			return new ShopOrder( $this->idorder );
		}

		function get_client()
		{
			return $this->order->client;
		}

		function get_name()
		{
			return 'Delivery #'. $this->number . ' (Order #'.$this->idorder.')';
		}

		function defaults()
		{
			parent::defaults();

			//$this->date = '@NOW()';
			$this->status = 1;
		}

	}

?>