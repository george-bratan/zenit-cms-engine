<?php

	// Logout

	class Logout extends AdminPage
	{
		static
			$TITLE = 'Logout';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/offline.png',
				'LARGE' => 'icon.large/delete.png',
			);


		static function Get()
		{
			Auth::Logout();

			Application::Reroute(Admin::$ROOT.'/login');
		}

	}

?>
