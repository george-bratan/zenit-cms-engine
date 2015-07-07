<?php

	// CrmLabels

	class CrmLabels extends AdminModule
	{
		static
			$TITLE  = 'Labels',
			$IDENT  = 'crm.labels';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/tag.png',
				'LARGE' => 'icon.large/tag.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Labels',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'crm.labels';

		static
			$TYPE = Array(
				0 => 'Color Label',
				1 => 'Contact Subject',
				2 => 'Note Resolution Flag',
			);


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function Model($id = NULL)
		{
			return Model::CrmLabel($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('FORMAT.color', function($record) {
				//
				if ($record['type'] > 0) {
					return '-';
				}

				return '<span style="float:left; margin-right:10px; display:block; width:16px; height:16px; border:1px solid black; background-color:#'.$record['color'].';"></span> ' . '#'.$record['color'];
			});

			UI::set('FORMAT.type', function($record) {
				//
				return CrmLabels::$TYPE[ $record['type'] ];
			});

			parent::GET_List($page);
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['type']->Type(Input::F_SELECT)->Options( self::$TYPE );

			$fields['color']->Type(Input::F_COLOR)->Options( Conf::Get('UI:COLOR:BASIC') )->Title('');

			return $fields;
		}

		static function FilterForm()
		{
			$fields = parent::FilterForm();

			$fields['type']->Type(Input::F_CHECKGROUP)->Options(self::$TYPE)->VAlign('vertical');

			return $fields;
		}

	}

?>