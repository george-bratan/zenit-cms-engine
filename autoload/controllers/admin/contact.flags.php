<?php

	// ContactFlags

	class ContactFlags extends AdminModule
	{
		static
			$TITLE  = 'Flags',
			$IDENT  = 'contact.flags';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/flag.png',
				'LARGE' => 'icon.large/flag.png',
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
			$AUTH = 'contact.flags';

		static
			$COLOR = Array(
				'gray' => 'Gray',
				'blue' => 'Blue',
				'green' => 'Green',
				'red' => 'Red',
				'yellow' => 'Yellow',
				'pink' => 'Pink',
				'purple' => 'Purple',
				'orange' => 'Orange',
			);


		static function Model($id = NULL)
		{
			return Model::ContactFlag($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('FORMAT.color', function($record) {
				//
				return '<img src="'.Conf::Get('WWW:ROOT').'/admin/images/icon.small/flag.'.$record['color'].'.png" /> ' . ContactFlags::$COLOR[ $record['color'] ];
			});

			parent::GET_List( $page );
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['color']->Type( Input::F_SELECT )->Options( self::$COLOR );

			return $fields;
		}

	}

?>