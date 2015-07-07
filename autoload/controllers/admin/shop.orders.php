<?php

	// ShopOrders

	class ShopOrders extends AdminModule
	{
		static
			$TITLE  = 'Orders',
			$IDENT  = 'shop.orders';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/cart.png',
				'LARGE' => 'icon.large/cart.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Orders',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'shop.orders';

		static
			$STATUS = Array(
				3 => 'Cancelled',  // red
				2 => 'Closed',     // black
				1 => 'Paid',       // green
				0 => 'Pending',    // blue
			);

		static
			$TRXSTATUS = Array(
				0 => 'None',
				1 => 'Authorised',
				2 => 'Completed',
				3 => 'Errors',
			);


		static function Model($id = NULL)
		{
			return Model::ShopOrder($id);
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}

			UI::set('FORMAT.client', function($record) {
				//
				return $record['client'] . (date('Y-m-d') == date('Y-m-d', strtotime($record['date'])) ? ' <span style="color:red">(New)</span>' : '');
			});

			UI::set('FORMAT.total', function($record) {
				//
				return number_format($record['total'], 2) . ' ' . Model::Settings('shop.currency')->value;
			});

			UI::set('FORMAT.status', function($record) {
				//
				return '<span style="color:'.($record['status'] == 0 ? 'blue' : ($record['status'] == 1 ? 'green' : ($record['status'] == 2 ? 'black' : 'red'))).'">'.
							ShopOrders::$STATUS[ $record['status'] ].'</span>';
			});

			parent::GET_List( $page );
		}

		static function GET_Details()
		{
			UI::set('STATUS', self::$STATUS);
			UI::set('TRXSTATUS', self::$TRXSTATUS);
			UI::set('DSTATUS', ShopDeliveries::$STATUS);
			UI::set('CURRENCY', Model::Settings('shop.currency')->value);

			$model = static::Model( intval( Request::URL('id') ) );

			$order = $model->record();
			$order['CONTACT'] = $model->contact->record();
			$order['CONTACT']['postal'] = $model->contact->postal->record();
			$order['PRODUCTS'] = $model->products->export();
			$order['DISCOUNTS'] = $model->discounts->export();
			$order['TAXES'] = $model->taxes->export();
			$order['NOTES'] = $model->notes->export();
			$order['DELIVERIES'] = $model->deliveries->export();
			$order['TRANSACTIONS'] = $model->transactions->export();

			UI::set('ORDER', $order);

			UI::set('FOOTER', UI::Render('admin/shop.order.notes.php'));
			UI::set('CONTENT', UI::Render('admin/shop.order.details.php'));

			UI::set('SECTION', 'Order #'.$model->id);

			self::Wrapper();
		}

		static function GET_Recurrence()
		{
			$model = static::Model( intval( Request::URL('id') ) );
			UI::Set('ORDER', $model->record());

			UI::Set('TITLE', 'Setup Subscription');
			UI::Set('TARGET', Request::$URL . '/recurrence/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/shop.order.recurrence.php'));

			self::Popup();
		}

		static function POST_Recurrence()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$model->subtype = Request::POST('subscription.type');
			$model->subday = 0;
			$model->subinterval = 0;

			if (Request::POST('subscription.type') == 1) {
				//
				$model->subday = Request::POST('subscription.wday');
				$model->subinterval = Request::POST('subscription.week');
			}
			if (Request::POST('subscription.type') == 2) {
				//
				$model->subday = Request::POST('subscription.mday');
				$model->subinterval = Request::POST('subscription.month');
			}

			$model->save();
		}

		static function POST_Note()
		{
			$note = Model::ShopOrderNote();
			$note->defaults();

			$note->idorder = intval( Request::URL('id') );
			$note->content = Request::POST('VALUES.content');

			$file = self::Upload('file');

			if ($file) {
				//
				$note->idfile = $file->id;
			}

			$note->save();
		}

		static function GET_Download()
		{
			$file = Model::ShopFile( intval(Request::URL('id')) );

			File::Send( $file->path, $file->original );
		}

		static function Upload($file)
		{
			if (!is_dir(Conf::get('APP:UPLOAD') . 'shop/')) {
				Util::mkdir(Conf::get('APP:UPLOAD') . 'shop/');
			}

			$temp = File::Temporary($file, 'shop/tmp');

			if ($temp) {
				//
				$file = Model::ShopFile();
				$file->defaults();

				$file->original = $temp['name'];
				$file->file = $temp['server_name'];
				$file->save();

				$perm = File::Permanent($temp, 'shop/file', $file->id);
				$file->original = $perm['name'];
				$file->file = $perm['server_name'];
				$file->save();

				return $file;
			}

			return NULL;
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Request::POST('status') !== NULL) {
				//
				$model->status = Request::POST('status');
			}
			else {
				//
				$model->status = ($model->status == 1) ? 0 : 1;
			}

			$model->save();
		}

		static function GET_Product()
		{
			$fields = Array();

			$products = DB::AssociativeColumn('SELECT id, name FROM shop_products WHERE status > -1 ORDER BY name ASC');

			// PRODUCT
			$input = new Input('product');
			$input->Type(Input::F_SELECT)->Title( 'Product' )->Context('VALUES')->Options( $products );

			$fields['product'] = $input; //->Export();

			// QUANTITY
			$input = new Input('quantity');
			$input->Type(Input::F_TEXT)->Title( 'Quantity' )->Context('VALUES');

			$fields['quantity'] = $input; //->Export();


			UI::set('FIELDS', $fields);

			UI::Set('TITLE', 'Add Product');
			UI::Set('TARGET', Request::$URL . '/product/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/.shared.edit.php'));

			self::Popup();
		}

		static function POST_Product()
		{
			$model = Model::ShopOrderProduct();
			$model->defaults();

			$product = Model::ShopProduct( Request::POST('VALUES.product') );

			$model->idorder = intval( Request::URL('id') );
			$model->idproduct = $product->id;
			$model->name = $product->name;
			$model->price = $product->price;
			$model->quantity = Request::POST('VALUES.quantity');

			$model->save();
		}

		static function POST_Quantity()
		{
			$model = Model::ShopOrderProduct( intval( Request::URL('id') ) );
			$model->quantity += intval( Request::POST('add') );
			$model->quantity = $model->quantity < 0 ? 0 : $model->quantity;
			$model->save();
		}

		static function GET_Discount()
		{
			$model = self::Model( intval(Request::URL('id')) );

			$discounts = Model::ShopDiscount();
			$discounts->where('status > -1')
				->execute();

			UI::Set('TARGET', Request::$URL . '/discount/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $discounts->export());
			UI::Set('SELECTED', $model->discounts->slice('id'));

			UI::Set('TITLE', 'Discounts');
			UI::Set('CONTENT', 'Select Discounts:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Discount()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				$items = array();
			}

			$model = self::Model( intval(Request::URL('id')) );

			$sql = new SQL();
			$sql->delete('shop_orders_discounts')
				->where('idorder = ?', $model->id)
				->execute();

			foreach ($items as $discount) {
				//
				$sql = new SQL();
				$sql->insert('shop_orders_discounts')
					->set('idorder = ?, iddiscount = ?', $model->id, $discount)
					->execute();
			}
		}

		static function GET_Tax()
		{
			$model = self::Model( intval(Request::URL('id')) );

			$taxes = Model::ShopTax();
			$taxes->where('status > -1')
				->execute();

			UI::Set('TARGET', Request::$URL . '/tax/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $taxes->export());
			UI::Set('SELECTED', $model->taxes->slice('id'));

			UI::Set('TITLE', 'Taxes');
			UI::Set('CONTENT', 'Select Taxes:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Tax()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				$items = array();
			}

			$model = self::Model( intval(Request::URL('id')) );

			$sql = new SQL();
			$sql->delete('shop_orders_taxes')
				->where('idorder = ?', $model->id)
				->execute();

			foreach ($items as $tax) {
				//
				$sql = new SQL();
				$sql->insert('shop_orders_taxes')
					->set('idorder = ?, idtax = ?', $model->id, $tax)
					->execute();
			}
		}

		static function GET_Transaction()
		{
			$model = Model::ShopOrder( intval(Request::URL('id')) );

			$fields = Array();

			// NAME
			$input = new Input('fname');
			$input->Type(Input::F_TEXT)->Title( 'First Name' )->Context('VALUES')->Value( $model->contact->firstname );
			$fields['fname'] = $input;

			// NAME
			$input = new Input('lname');
			$input->Type(Input::F_TEXT)->Title( 'Last Name' )->Context('VALUES')->Value( $model->contact->lastname );
			$fields['lname'] = $input;

			// CC NUMBER
			$input = new Input('ccnumber');
			$input->Type(Input::F_TEXT)->Title( 'Credit Card Number' )->Context('VALUES');
			$fields['ccnumber'] = $input;

			/*
			// CC TYPE
			$types = Util::split('Visa|MasterCard|Discover|Amex');
			$types = array_combine($types, $types);

			$input = new Input('cctype');
			$input->Type(Input::F_SELECT)->Title( 'Credit Card Type' )->Context('VALUES')->Options( $types );
			$fields['cctype'] = $input;
			*/

			// CC EXP MONTH
			$months = array();
			for ($m = 1; $m < 13; $m++) {
				//
				$months[ str_pad($m, 2, '0', STR_PAD_LEFT) ] = date('F', mktime(0, 0, 0, $m, 1, 2005));
			}

			$input = new Input('ccexpm');
			$input->Type(Input::F_SELECT)->Title( 'Expiration Date, Month' )->Context('VALUES')->Options( $months );
			$fields['ccexpm'] = $input;

			// CC EXP YEAR
			$years = array();
			for ($y = 0; $y < 10; $y++) {
				//
				$years[ date('Y') + $y ] = date('Y') + $y;
			}

			$input = new Input('ccexpy');
			$input->Type(Input::F_SELECT)->Title( 'Expiration Date, Year' )->Context('VALUES')->Options( $years );
			$fields['ccexpy'] = $input;

			// CC CVV
			$input = new Input('cccvv');
			$input->Type(Input::F_TEXT)->Title( 'CVV2 Verification Number' )->Context('VALUES');
			$fields['cccvv'] = $input;

			// AMOUNT
			$input = new Input('amount');
			$input->Type(Input::F_TEXT)->Title( 'Amount ('. Model::Settings('shop.currency')->value .')' )->Context('VALUES')->Align('right')
				->Value( number_format($model->total - $model->paid, 2) );
			$fields['amount'] = $input;

			// ACTION
			$input = new Input('auth');
			$input->Type(Input::F_RADIOGROUP)->Title( 'Action' )->Context('VALUES')->VAlign('vertical')->Value( 'Authorization' )->Align('right')
				->Options( array('Authorization' => 'Authorize Only', 'Sale' => 'Authorize and Capture Amounts') );
			$fields['auth'] = $input;


			UI::set('FIELDS', $fields);

			UI::Set('TITLE', 'New Transaction');
			UI::Set('TARGET', Request::$URL . '/transaction/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/.shared.edit.php'));

			self::Popup();
		}

		static function POST_Transaction()
		{
			$model = Model::ShopOrder( intval(Request::URL('id')) );

			$trx = Model::ShopTransaction();
			$trx->defaults();

			$trx->service = 'paypal';
			$trx->idorder = $model->id;
			$trx->amount = Request::POST('VALUES.amount');
			$trx->cc = 'XXXX XXXX XXXX ' . substr(Request::POST('VALUES.ccnumber'), -4);

			$paypal = new GatewayPaypal( Model::Settings('shop.paypal.user')->value, Model::Settings('shop.paypal.pass')->value,
				Model::Settings('shop.paypal.auth')->value );

			$result = $paypal->sandbox('DoDirectPayment', array(
				'PAYMENTACTION' => Request::POST('VALUES.auth'),
				'ACCT' => trim(Request::POST('VALUES.ccnumber')),
				'EXPDATE' => Request::POST('VALUES.ccexpm') . Request::POST('VALUES.ccexpy'),
				'CVV2' => Request::POST('VALUES.cccvv'),
				'PAYMENTACTION' => Request::POST('VALUES.auth'),

				'FIRSTNAME' => Request::POST('VALUES.fname'),
				'LASTNAME' => Request::POST('VALUES.lname'),

				'STREET' => $model->contact->billing->street,
				'CITY' => $model->contact->billing->city,
				'STATE' => $model->contact->billing->state,
				'COUNTRYCODE' => $model->contact->billing->country,
				'ZIP' => $model->contact->billing->postcode,

				'AMT' => Request::POST('VALUES.amount'),
				'CURRENCYCODE' => Model::Settings('shop.currency')->value,
				'INVNUM' => $model->id,
				//'NOTIFYURL' => '',
			));

			$trx->response = print_r( $result, $return = true );

			$trx->status = $trx::F_ERRORS;
			if ( strtoupper($result['ACK']) == 'SUCCESS' || strtoupper($result['ACK']) == 'SUCCESSWITHWARNING' ) {
				//
				$trx->status = strtoupper(Request::POST('VALUES.auth')) == 'SALE' ? $trx::F_COMPLETED : $trx::F_AUTHORISED;
				$trx->trx = $result['TRANSACTIONID'];

				$trx->authorized = '@NOW()';
				$trx->captured = strtoupper(Request::POST('VALUES.auth')) == 'SALE' ? '@NOW()' : '';
			}

			$trx->save();
		}

		static function GET_Trx()
		{
			$trx = Model::ShopTransaction( intval(Request::URL('id')) );

			UI::Set('TITLE', 'Transaction Details');
			UI::Set('CONTENT', '<pre>'. $trx->response .'</pre>');

			self::Popup();
		}

		static function POST_Capture()
		{
			$trx = Model::ShopTransaction( intval(Request::URL('id')) );

			$paypal = new GatewayPaypal( Model::Settings('shop.paypal.user')->value, Model::Settings('shop.paypal.pass')->value,
				Model::Settings('shop.paypal.auth')->value );

			$result = $paypal->sandbox('DoCapture', array(
				'AUTHORIZATIONID' => $trx->trx,
				'AMT' => number_format($trx->amount, 2),
				'CURRENCYCODE' => Model::Settings('shop.currency')->value,
				'COMPLETETYPE' => 'Complete',
			));

			$trx->response = print_r( $result, $return = true );

			$trx->status = $trx::F_ERRORS;
			if ( strtoupper($result['ACK']) == 'SUCCESS' || strtoupper($result['ACK']) == 'SUCCESSWITHWARNING' ) {
				//
				$trx->status = $trx::F_COMPLETED;
				$trx->trx = $result['TRANSACTIONID'];

				$trx->captured = '@NOW()';
			}

			$trx->save();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			//

			return $model;
		}

		static function FilterForm()
		{
			$filters = parent::FilterForm();

			$filters['date']->Type(Input::F_DATERANGE)->Width('142px');

			return $filters;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$contacts = DB::AssociativeColumn("SELECT id, IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM crm_contacts WHERE status > -1");

			$fields['idcontact']->Type(Input::F_SELECT)
				->Options(array_merge(array('-'), $contacts));

			return $fields;
		}

		static function Notification()
		{
			$num = DB::Fetch('SELECT COUNT(*) FROM shop_orders WHERE DATE(date) = CURDATE() AND status > -1');

			return $num;
		}

		static function Timeline( $feed = NULL )
		{
			if (!$feed) {
				return array(
					'orders' => 'Orders',
				);
			}

			if ($feed == 'orders') {
				return DB::AssociativeColumn("SELECT DATE(T.date), COUNT(*) FROM shop_orders AS T WHERE TRUE GROUP BY DATE(T.date) ORDER BY T.date DESC LIMIT 10");
			}

			return parent::Timeline();
		}

		static function RecipientFeed( $feed = NULL, $filters = NULL )
		{
			if (!$feed) {
				//
				return array(
					'SHOP.CONTACTS' => 'Customers',
				);
			}

			if ($feed == 'SHOP.CONTACTS') {
				//
				$model = Model::ShopOrder();

				if (isset($filters['orders'])) {
					if (isset($filters['orders'][0])) {
						if ($filters['orders'][0])
							$model->having("COUNT(*) >= ?", $filters['orders'][0]);
					}
					if (isset($filters['orders'][1])) {
						if ($filters['orders'][1])
							$model->having("COUNT(*) <= ?", $filters['orders'][1]);
					}
				}
				if (isset($filters['amount'])) {
					if (isset($filters['amount'][0])) {
						if ($filters['amount'][0])
							$model->where("(SELECT SUM(price * quantity) FROM shop_orders_products WHERE idorder = M.id AND status > -1) >= ?", $filters['amount'][0]);
					}
					if (isset($filters['amount'][1])) {
						if ($filters['amount'][1])
							$model->where("(SELECT SUM(price * quantity) FROM shop_orders_products WHERE idorder = M.id AND status > -1) <= ?", $filters['amount'][1]);
					}
				}
				if (isset($filters['deliveries'])) {
					if (isset($filters['deliveries'][0])) {
						if ($filters['deliveries'][0])
							$model->where("(SELECT COUNT(*) FROM shop_deliveries WHERE idorder = M.id AND status > -1) >= ?", $filters['deliveries'][0]);
					}
					if (isset($filters['deliveries'][1])) {
						if ($filters['deliveries'][1])
							$model->where("(SELECT COUNT(*) FROM shop_deliveries WHERE idorder = M.id AND status > -1) <= ?", $filters['deliveries'][1]);
					}
				}
				if (isset($filters['date'])) {
					if (isset($filters['date'][0])) {
						if ($filters['date'][0])
							$model->where("DATE(`date`) >= ?", $filters['date'][0]);
					}
					if (isset($filters['date'][1])) {
						if ($filters['date'][1])
							$model->where("DATE(`date`) <= ?", $filters['date'][1]);
					}
				}

				$model->select('idcontact')->group('idcontact');
				$customers = str_replace(array(' AS M ', ' M.'), array(' AS N ', ' N.'), $model->sql());
				$args = $model->args();

				$model = Model::CrmContact();
				$model->where('id IN (' . $customers . ')');
				foreach ($args as $arg) {
					$model->args($arg);
				}

				if (isset($filters['name'])) {
					if ($filters['name'])
						$model->where("CONCAT(firstname, ' ', lastname) LIKE '%" . $filters['name'] . "%'");
				}
				if (isset($filters['email'])) {
					if ($filters['email'])
						$model->where("email LIKE '%" . $filters['email'] . "%'");
				}
				if (isset($filters['company'])) {
					if ($filters['company'])
						$model->where("idcompany = ?", $filters['company']);
				}
				if (isset($filters['city'])) {
					if ($filters['city'])
						//$model->where("city LIKE '%" . $filters['city'] . "%'");
						$model->where("(SELECT COUNT(*) FROM crm_addresses WHERE id = idpostal AND city LIKE '%" . $filters['city'] . "%') > 0");
				}
				if (isset($filters['state'])) {
					if ($filters['state'])
						//$model->where("state LIKE '%" . $filters['state'] . "%'");
						$model->where("(SELECT COUNT(*) FROM crm_addresses WHERE id = idpostal AND state LIKE '%" . $filters['state'] . "%') > 0");
				}
				if (isset($filters['country'])) {
					if ($filters['country'])
						//$model->where("country = ?", $filters['country']);
						$model->where("(SELECT COUNT(*) FROM crm_addresses WHERE id = idpostal AND country = ?) > 0", $filters['country']);
				}

				$model->execute();

				// BUILD RESPONSE

				$result = array();

				if ($model->found()) {
					//
					$model->reset();
					while ($model->next()) {
						//
						$other = array();
						if ($model->postal->street)
							$other[] = $model->postal->street;
						if ($model->postal->city)
							$other[] = $model->postal->city;
						if ($model->postal->state)
							$other[] = $model->postal->state;

						if (!count($other))
							$other[] = $model->company->name;

						$result[] = array(
							'name' => $model->name,
							'email' => $model->email,
							'other' => implode(', ', $other),
						);
					}
				}

				$RESULT = Array();
				$RESULT['FEED'] = $feed;

				$RESULT['RESULT'] = $result;

				$RESULT['FILTERS'] = Array();

				$input = new Input('name');
				$input->Type(Input::F_TEXT)->Title('Filter by Name');
				if (isset($filters['name'])) {
					$input->Value($filters['name']);
				}
				$RESULT['FILTERS']['name'] = $input;

				$companies = DB::AssociativeColumn("SELECT '0', '-' UNION (SELECT id, name FROM crm_companies WHERE status = 1 ORDER BY name ASC)");
				$input = new Input('company');
				$input->Type(Input::F_SELECT)->Options($companies)->Title('Company');
				if (isset($filters['company'])) {
					$input->Value($filters['company']);
				}
				$RESULT['FILTERS']['company'] = $input;

				$input = new Input('city');
				$input->Type(Input::F_TEXT)->Title('Filter by City');
				if (isset($filters['city'])) {
					$input->Value($filters['city']);
				}
				$RESULT['FILTERS']['city'] = $input;

				$input = new Input('state');
				$input->Type(Input::F_TEXT)->Title('Filter by State');
				if (isset($filters['state'])) {
					$input->Value($filters['state']);
				}
				$RESULT['FILTERS']['state'] = $input;

				$countries = array_merge(array('' => '-'), Conf::Get('COUNTRIES'));
				$input = new Input('country');
				$input->Type(Input::F_SELECT)->Title('Country')->Options($countries);
				if (isset($filters['country'])) {
					$input->Value($filters['country']);
				}
				$RESULT['FILTERS']['country'] = $input;

				$input = new Input('orders');
				$input->Type(Input::F_RANGE)->Title('Number of Orders Between')->Width('143px');
				if (isset($filters['orders'])) {
					$input->Value($filters['orders']);
				}
				$RESULT['FILTERS']['orders'] = $input;

				$input = new Input('amount');
				$input->Type(Input::F_RANGE)->Title('With Order Values Between')->Width('143px')->Details( Model::Settings('shop.currency')->value );
				if (isset($filters['amount'])) {
					$input->Value($filters['amount']);
				}
				$RESULT['FILTERS']['amount'] = $input;

				$input = new Input('deliveries');
				$input->Type(Input::F_RANGE)->Title('Number of Deliveries Between')->Width('143px');
				if (isset($filters['deliveries'])) {
					$input->Value($filters['deliveries']);
				}
				$RESULT['FILTERS']['deliveries'] = $input;

				$input = new Input('date');
				$input->Type(Input::F_DATERANGE)->Title('Orders Submitted between')->Width('143px');
				if (isset($filters['date'])) {
					$input->Value($filters['date']);
				}
				$RESULT['FILTERS']['date'] = $input;


				return $RESULT;
			}
		}

	}

?>