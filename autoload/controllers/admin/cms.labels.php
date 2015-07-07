<?php

	// CmsLabels

	class CmsLabels extends AdminModule
	{
		static
			$TITLE  = 'Labels',
			$IDENT  = 'cms.labels';

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
			$AUTH = 'cms.labels';


		static function Model($id = NULL)
		{
			return Model::CmsLabel($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('FORMAT.color', function($record) {
				//
				return '<span style="float:left; margin-right:10px; display:block; width:16px; height:16px; border:1px solid black; background-color:#'.$record['color'].';"></span> ' . '#'.$record['color'];
			});

			parent::GET_List($page);
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['color']->Type(Input::F_COLOR)->Options( Conf::Get('UI:COLOR:BASIC') )->Title('');

			return $fields;
		}

	}

?>