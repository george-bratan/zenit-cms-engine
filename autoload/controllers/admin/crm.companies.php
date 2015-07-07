<?php

	// CrmCompanies

	class CrmCompanies extends AdminModule
	{
		static
			$TITLE  = 'Companies',
			$IDENT  = 'crm.companies';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.group.png',
				'LARGE' => 'icon.large/user.group.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Companies',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'crm.companies';

		static
			$SIZE = Array(
				0 => 'Individual',
				1 => '1-10 Employees',
				2 => '10-20 Employees',
				3 => '20-50 Employees',
				4 => '50-100 Employees',
				5 => '100-200 Employees',
				6 => '200-500 Employees',
				7 => 'Over 500 Employees',
			);


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function Model($id = NULL)
		{
			return Model::CrmCompany($id);
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}

			UI::set('FORMAT.size', function($record) {
				//
				return CrmCompanies::$SIZE[ $record['size'] ];
			});

			UI::set('FORMAT.labels', function($record) {
				//
				$result = '';
				if ($record['labels']->found()) {
					//
					$record['labels']->reset();
					while ($record['labels']->next()) {
						//
						$result .= '<span style="float:left; margin-right:10px; display:block; width:12px; height:12px; border:1px solid black; background-color:#'.$record['labels']->color.';" title="'.$record['labels']->name.'"></span>';
					}
				}

				return $result;
			});

			parent::GET_List($page);
		}

		static function GET_Details()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('COMPANY', $model->record());

			UI::set('COMPANY.CONTACTS', $model->contacts->export());
			UI::set('COMPANY.LABELS', $model->labels->export());

			UI::set('SIZE', self::$SIZE);

			//UI::set('FOOTER', UI::Render('admin/support.ticket.threads.php'));
			UI::set('CONTENT', UI::Render('admin/crm.company.details.php'));

			UI::set('SECTION', 'Company: '.$model->name);

			self::Wrapper();
		}

		static function GET_Labels()
		{
			$model = self::Model( intval(Request::URL('id')) );

			$labels = Model::CrmLabel();
			$labels->where('type = ?', Models\CrmLabel::F_COLOR)
				->execute();

			UI::Set('TARGET', Request::$URL . '/labels/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $labels->export());
			UI::Set('SELECTED', $model->labels->slice('id'));

			UI::Set('TITLE', 'Labels');
			UI::Set('CONTENT', 'Select labels:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Labels()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				$items = array();
			}

			$model = self::Model( intval(Request::URL('id')) );

			$sql = new SQL();
			$sql->delete('crm_companies_labels')
				->where('idcompany = ?', $model->id)
				->execute();

			foreach ($items as $label) {
				//
				$sql = new SQL();
				$sql->insert('crm_companies_labels')
					->set('idcompany = ?, idlabel = ?', $model->id, $label)
					->execute();
			}
		}

		static function GET_Edit()
		{
			parent::GET_Details();
		}

		static function GET_Address()
		{
			if (Request::URL('id')) {
				//
				$model = static::Model( intval( Request::URL('id') ) );

				UI::set('ITEM', $model->record());

				UI::set('TITLE', 'Edit: '.$model->name);
				UI::set('FIELDS', static::AddressForm( $model ));
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['size']->Type(Input::F_SELECT)->Options(self::$SIZE);

			unset($fields['address']);
			unset($fields['country']);
			unset($fields['city']);
			unset($fields['postcode']);

			return $fields;
		}

		static function AddressForm( $model )
		{
			$fields = parent::EditForm( $model );

			unset($fields['size']);
			unset($fields['email']);
			unset($fields['phone']);
			unset($fields['url']);

			return $fields;
		}

		static function FilterForm()
		{
			$fields = parent::FilterForm();

			$fields['size']->Type(Input::F_CHECKGROUP)->Options(self::$SIZE)->VAlign('vertical');

			return $fields;
		}

	}

?>