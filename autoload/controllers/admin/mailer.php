<?php

	// Mailer

	class Mailer extends AdminPage
	{
		static
			$TITLE = 'Mass Mailing';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/email.edit.png',
				'LARGE' => 'icon.large/email.png',
			);

		static
			$HELP = 'mailer';

		static
			$FORWARD = TRUE;

	}

?>