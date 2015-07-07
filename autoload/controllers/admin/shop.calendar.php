<?php

	// ShopCalendar

	class ShopCalendar extends AdminPage
	{
		static
			$TITLE  = 'Deliveries', // 'Calendar'
			$IDENT  = 'shop.calendar';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/calendar.png',
				'LARGE' => 'icon.large/calendar.red.png',
			);

		static
			$AUTH = 'shop.calendar';


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function GET()
		{
			$deliveries = Model::ShopDelivery();

			if (Request::URL('date')) {
				$deliveries->where('DATE(scheduled) = ?', Request::URL('date'));

				UI::set('DATE', Request::URL('date'));
			}
			else {
				$deliveries->where('DATE(scheduled) = CURDATE()');
			}

			$deliveries->where('status > -1')
				->order('date ASC')
				->execute();

			UI::set('STATUS', ShopDeliveries::$STATUS);
			UI::set('DELIVERIES', $deliveries->export());

			UI::set('CONTENT', UI::Render('admin/shop.calendar.php'));

			parent::Get();
		}

	}

?>