<?php

	// Models\ShopTransaction

	namespace Models;

	class ShopTransaction extends \Model {

		static
			$public = Array(
				'date' => 'Date/Time',
				'amount' => 'Amount',
			);

		static
			$default = Array(
				'date', 'amount',
			);

		static
			$filters = Array(
				'date' => 'Date/Time',
				'amount' => 'Amount',
			);

		static
			$schema = Array(
				'idorder' => 'Order',
				'amount' => 'Amount',
				'service' => 'Payment Gateway',
				'trx' => 'Transaction ID',
				'name' => 'Contact Name',
				'cc' => 'Last 4 Digits',
				'date' => 'Date',
				'status' => 'Status',
			);

		private
			$custom	= Array();

		const
			F_NONE = 0,
			F_AUTHORISED = 1,
			F_COMPLETED = 2,
			F_ERRORS = 3;

		function __construct($id = NULL)
		{
			parent::__construct('shop_transactions');

			//$this->def('admin');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->order('date ASC')->execute();
			}

			return $this;
		}

		function defaults()
		{
			parent::defaults();

			$this->date = '@NOW()';
			$this->status = 1;
		}

	}

?>