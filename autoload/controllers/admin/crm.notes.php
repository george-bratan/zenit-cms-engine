<?php

	// CrmNotes

	class CrmNotes extends AdminList
	{
		static
			$TITLE  = 'Notes',
			$IDENT  = 'crm.notes';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/comments.png',
				'LARGE' => 'icon.large/comments.png',
			);

		static
			$AUTH = 'crm.notes';

		static
			$METHOD = Array(
				0 => '-',
				1 => 'Phone Call',
				2 => 'SMS Message',
				3 => 'Email',
			);


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function Model($id = NULL)
		{
			return Model::CrmContactNote($id);
		}

		static function GET_List($page = 0)
		{
			UI::nset('FORMAT.contact',
				function($record) {
					return '<a rel="modal" id="note_'.$record['idcontact'].'" href="'.Request::$URL.'/../contacts/note/'.$record['idcontact'].'" style="display:none">note</a>'.
						'<a style="float:left; margin-right:10px" href="'.Request::$URL.'/../contacts/card/'.$record['idcontact'].'" rel="modal"><img src="'.Conf::Get('WWW:ROOT').'/admin/images/icon.small/vcard.png"></a> '.
						$record['contact'];
				}
			);

			UI::set('FORMAT.method', function($record) {
				//
				return CrmNotes::$METHOD[ $record['method'] ];
			});

			parent::GET_List($page);
		}

		static function FilterForm()
		{
			$args = func_get_args();
			$filters = parent::FilterForm(isset($args[0]) ? $args[0] : NULL);

			unset(self::$METHOD[0]);
			$filters['method']->Type(Input::F_CHECKGROUP)->Options(self::$METHOD);
			$filters['date']->Type(Input::F_DATERANGE);

			return $filters;
		}

	}

?>