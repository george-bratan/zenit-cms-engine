<?php

	// ContactSubjects

	class ContactSubjects extends AdminModule
	{
		static
			$TITLE  = 'Subjects',
			$IDENT  = 'contact.subjects';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/info.png',
				'LARGE' => 'icon.large/info.png',
			);

		static
			$PERMISSION = Array(
				/*
				'list' 		=> 'List Subjects',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
				*/
			);

		static
			$AUTH = 'contact.subjects';


		static function Model($id = NULL)
		{
			return Model::ContactSubject($id);
		}

	}

?>