<?php

	// SupportTimes

	class SupportTimes extends AdminList
	{
		static
			$TITLE  = 'Time Entries',
			$IDENT  = 'support.times';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/clock.png',
				'LARGE' => 'icon.large/clock.png',
			);

		/*
		static
			$PERMISSION = Array(
				'list' 		=> 'List Time Entries',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);
		*/

		static
			$AUTH = 'support.times';


		static function Model($id = NULL)
		{
			return Model::SupportTime($id);
		}
	}

?>