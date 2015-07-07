<?php

	// Dashboard

	class Dashboard extends AdminPage
	{
		static
			$TITLE = 'Dashboard',
			$IDENT  = 'dashboard';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/home.png',
				'LARGE' => 'icon.large/home.png',
			);

		static
			$HELP = 'dashboard';

		static
			$AUTH = 'admin.dashboard';

		static function combine($source, $values, $ident)
		{
			foreach ($values as $key => $value) {
				//
				$source[$key][$ident] = $value;
			}

			return $source;
		}

		static function Get()
		{
			//
			$feeds = Admin::Timeline();

			$report = $series = array();
			foreach ($feeds as $feed => $title) {
				//
				$data = Admin::Timeline($feed);
				if ($data) {
					//
					$report = self::combine($report, $data, $feed);
					$series[ $feed ] = $title;
				}
			}

			ksort($report);

			if (count($report) > 10) {
				//
				$report = array_slice($report, count($report) - 10);
			}

			UI::set('SERIES', $series);
			UI::set('REPORT', $report);

			UI::set('CONTENT', UI::Render('admin/dashboard.php'));

			UI::set('SECTIONS', Admin::ListSections( 'ShopOrders', 'CrmContacts', 'SupportTickets', 'ContactMessages', 'MailerContacts', 'CmsPages', 'CmsArticles', 'CmsDocuments', 'Social', 'Settings', 'Logout' ));

			parent::Get();
			//UI::Serve('admin/wrapper.outer.php');
		}

		static function Timeline( $feed = NULL )
		{
			if (!$feed) {
				return array(
					'views' => 'Page Views',
					'visitors' => 'Unique Visitors',
				);
			}

			if ($feed == 'views') {
				return DB::AssociativeColumn("SELECT DATE(T.date), COUNT(*) FROM cms_views AS T WHERE TRUE GROUP BY DATE(T.date) ORDER BY T.date DESC LIMIT 10");
			}

			if ($feed == 'visitors') {
				return DB::AssociativeColumn("SELECT DATE(T.date), COUNT(DISTINCT ip) FROM cms_views AS T WHERE TRUE GROUP BY DATE(T.date) ORDER BY T.date DESC LIMIT 10");
			}

			return parent::Timeline();
		}

	}

?>