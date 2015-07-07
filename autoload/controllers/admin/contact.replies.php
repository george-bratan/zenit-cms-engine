<?php

	// ContactReplies

	class ContactReplies extends AdminModule
	{
		static
			$TITLE  = 'Replies',
			$IDENT  = 'contact.replies';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/lightbulb.png',
				'LARGE' => 'icon.large/lightbulb.png',
			);

		static
			$PERMISSION = Array(
				/*
				'list' 		=> 'List Replies',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
				*/
			);

		static
			$AUTH = 'contact.replies';


		static function Model($id = NULL)
		{
			return Model::ContactReply($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('FORMAT.content', function($record) {
				//
				return substr($record['content'], 0, 100) . ' ...';
			});

			parent::GET_List( $page );
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['content']->Type( Input::F_RICHTEXT );

			return $fields;
		}

	}

?>