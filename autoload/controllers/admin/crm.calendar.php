<?php

	// CrmCalendar

	class CrmCalendar extends AdminPage
	{
		static
			$TITLE  = 'Calendar',
			$IDENT  = 'crm.calendar';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/calendar.png',
				'LARGE' => 'icon.large/calendar.red.png',
			);

		static
			$AUTH = 'crm.calendar';


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function GET()
		{
			$notes = Model::CrmContactNote();

			if (Request::URL('date')) {
				$notes->where('DATE(date) = ?', Request::URL('date'));

				UI::set('DATE', Request::URL('date'));
			}
			else {
				$notes->where('DATE(date) = CURDATE()');
			}

			$notes->where('status > -1')
				->order('date ASC')
				->execute();

			UI::set('NOTES', $notes->export());

			UI::set('CONTENT', UI::Render('admin/crm.calendar.php'));

			parent::Get();
		}

	}

?>