<?php

	// Models\ShopOrder

	namespace Models;

	class ShopOrder extends \Model {

		static
			$public = Array(
				'client' => 'Client',
				'total' => 'Amount',
				'date' => 'Date/Time',
				'numdeliveries' => 'Deliveries',
			);

		static
			$default = Array(
				'client', 'total', 'date',
			);

		static
			$filters = Array(
				'client' => 'Client Name',
				'total' => 'Amount',
				'date' => 'Date Between',
			);

		static
			$schema = Array(
				'idcontact' => 'Client',
			);


		function __construct($id = NULL)
		{
			parent::__construct('shop_orders');

			$this->def('client', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM crm_contacts WHERE id = idcontact)");

			$this->def('amount');
			$this->def('tax');
			$this->def('discount');
			$this->def('total');
			$this->def('recurrence');

			$this->def('contact');
			$this->def('products');
			$this->def('discounts');
			$this->def('deliveries');
			$this->def('transactions');
			$this->def('taxes');
			$this->def('notes');
			$this->def('paid', "(SELECT SUM(T.amount) FROM shop_transactions AS T WHERE T.idorder = M.id AND T.status = 2)");

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_recurrence()
		{
			if ($this->subtype == 1) {
				//
				$WDAYS = \Conf::Get('WDAYS');
				return 'Weekly: <strong>'. $WDAYS[$this->subday] . '</strong>, every <strong>' . ($this->subinterval == 1 ? 'week' : $this->subinterval . ' weeks') . '</strong>';
			}

			if ($this->subtype == 2) {
				//
				$MDAYS = \Conf::Get('MDAYS');
				return 'Monthly: <strong>'. $MDAYS[$this->subday] . '</strong>, every <strong>' . ($this->subinterval == 1 ? 'month' : $this->subinterval . ' months') . '</strong>';
			}

			return 'No Recurrence';
		}

		function get_amount()
		{
			return \DB::Fetch("SELECT SUM(price * quantity) FROM shop_orders_products WHERE idorder = ?", array($this->id));
		}

		function get_discount()
		{
			$original = $this->amount;
			$result = 0;

			$discounts = $this->discounts;
			if ($discounts->found()) {
				//
				$discounts->reset();
				while ($discounts->next()) {
					//
					if ($discounts->type == 0) {
						// FLAT
						$result += $discounts->value;
					}

					if ($discounts->type == 1) {
						// PERCENT
						$result += $original * $discounts->value / 100;
					}
				}
			}

			return $result;
		}

		function get_tax()
		{
			$original = $this->amount;
			$result = 0;

			$taxes = $this->taxes;
			if ($taxes->found()) {
				//
				$taxes->reset();
				while ($taxes->next()) {
					//
					if ($taxes->type == 0) {
						// FLAT
						$result += $taxes->value;
					}

					if ($taxes->type == 1) {
						// PERCENT
						$result += $original * $taxes->value / 100;
					}
				}
			}

			return $result;
		}

		function get_total()
		{
			$total = $this->amount - $this->discount + $this->tax;

			if ($total < 0) {
				//
				$total = 0;
			}

			return $total;
		}

		function get_contact()
		{
			$model = new CrmContact( $this->idcontact );

			return $model;
		}

		function get_products()
		{
			$model = new ShopOrderProduct();
			$model->where('idorder = ?', $this->id)
				->execute();

			return $model;
		}

		function get_discounts()
		{
			$model = new ShopDiscount();
			$model->where('id IN ??', \DB::Column('SELECT DISTINCT iddiscount FROM shop_orders_discounts WHERE idorder = ?', array($this->id)))
				->execute();

			return $model;
		}

		function get_taxes()
		{
			$model = new ShopTax();
			$model->where('id IN ??', \DB::Column('SELECT DISTINCT idtax FROM shop_orders_taxes WHERE idorder = ?', array($this->id)))
				->execute();

			return $model;
		}

		function get_transactions()
		{
			$model = new ShopTransaction();
			$model->where('idorder = ?', $this->id)
				->execute();

			return $model;
		}

		function get_deliveries()
		{
			$model = new ShopDelivery();
			$model->where('idorder = ? AND status > -1', $this->id)
				->execute();

			return $model;
		}

		function get_notes()
		{
			$model = new ShopOrderNote();
			$model->where('idorder = ?', $this->id)
				->order('date ASC')->execute();

			return $model;
		}

		function defaults()
		{
			parent::defaults();

			$this->date = '@NOW()';
			$this->status = 0;
		}

	}

?>