<?php

	// AdminModule

	class AdminModule extends AdminList
	{
		static
			$TITLE = 'Module',
			$IDENT = 'module';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static function OnLoad()
		{
			if (isset(static::$PERMISSION['save'])) {
				//
				if (Auth::Grant(static::$AUTH . '.save')) {
					//
					$token = Util::Split( Session::Get('ACCOUNT.TOKEN') );
					$token[] = static::$AUTH . '.new';
					$token[] = static::$AUTH . '.edit';
					$token[] = static::$AUTH . '.bulk';
					$token[] = static::$AUTH . '.status';

					Session::Set('ACCOUNT.TOKEN', implode('|', $token));
				}
			}
		}

		static function GET_List($page = 0)
		{
			// bulk options and individual record options
			if (Auth::Grant(isset(static::$PERMISSION['edit']) ? static::$AUTH .'.edit' : '')) {

				UI::nset('BULK.enable', 'Enable');
				UI::nset('BULK.disable', 'Disable');
			}
			if (Auth::Grant(isset(static::$PERMISSION['details']) ? static::$AUTH .'.details' : '')) {

				UI::nset('OPTIONS.details', Array(
					'handler' => 'details',
					'rel' => 'modal',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}
			if (Auth::Grant(isset(static::$PERMISSION['delete']) ? static::$AUTH .'.delete' : '')) {

				UI::nset('BULK.delete', 'Delete');

				UI::nset('OPTIONS.delete', Array(
					'handler' => 'delete',
					'rel' => 'modal',
					'icon' => 'icon.small/delete.png',
					'title' => 'Delete',
				));
			}

			// fixed columns, will always be shown
			UI::nset('FIXED', array('status' => 'Status'));

			if (Auth::Grant(isset(static::$PERMISSION['status']) ? static::$AUTH .'.status' : '')) {
				//
				UI::nset('FORMAT.status', function($record){
						return '<a rel="post" href="'.Request::$URL.'/status/'.$record['id'].'" style="color:'.($record['status'] ? 'green' : 'red').'">
									<span >'.($record['status'] ? 'Enabled' : 'Disabled').'</span>
								</a>';
					}
				);
			}
			else {
				//
				UI::nset('FORMAT.status', function($record){
						return '<span style="color:'.($record['status'] ? 'green' : 'red').'">'.
							($record['status'] ? 'Enabled' : 'Disabled').'</span>';
					}
				);
			}


			if (Auth::Grant(isset(static::$PERMISSION['new']) ? static::$AUTH .'.new' : '')) {
				//
				UI::nset('TOOLBAR.new', array(
						'url' => Request::$URL.'/new',
						'rel' => 'modal',
						'title' => 'New Record'
					)
				);
			}

			$args = func_get_args();
			parent::GET_List($page, isset($args[1]) ? $args[1] : NULL);
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$model->status = ($model->status == 1) ? 0 : 1;
			$model->save();
		}

		static function GET_Delete($items = NULL)
		{
			if (!is_array($items)) {
				$items = array( Request::URL('id') );
			}

			if (count($items)) {
				//
				$model = static::Model();
				$model->where("id IN ??", $items)->execute();

				$records = array();

				$model->reset();
				while ($model->next()) {
					$records[ $model->id ] = $model->name;
				}

				UI::set('TITLE', 'Confirmation Required');
				UI::set('CONTENT', 'Are you sure you want to delete these records ?');
				UI::set('RECORDS', $records);
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/.shared.delete.php'));

			parent::Popup();
		}

		static function POST_Delete()
		{
			$items = Request::POST('items');

			static::MultiSetStatus($items, -1);
		}

		static function GET_Details()
		{
			if (Request::URL('id')) {
				//
				$model = static::Model( intval( Request::URL('id') ) );

				UI::set('ITEM', $model->record());

				UI::set('TITLE', 'Edit: '.$model->name);
				UI::set('FIELDS', static::EditForm( $model ));
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
			$model = static::Model( intval( Request::URL('id') ) );

			if (!Request::URL('id')) {
				// new record, attach default values first
				$model->defaults();
			}

			foreach (Request::POST('VALUES') as $field => $value) {
				//
				if (in_array($field, array_keys($model::$schema))) {
					//
					if (is_array($value)) {
						//
						$value = Util::FilterChecked($value);
						$value = implode('|', $value);
					}

					$model->$field = $value;
				}
			}

			$model->save();

			return $model;
		}

		static function GET_New()
		{
			UI::set('TITLE', 'New Record');
			UI::set('FIELDS', static::EditForm( static::Model() ));

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Bulk()
		{
			$items = Request::POST('items');
			if (!count($items)) {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'You must select at least one record.');

				UI::set('CONTENT', UI::Render('admin/.shared.message.php'));

				parent::Popup();
				return;
			}

			if (Request::POST('bulk') == 'delete') {
				//
				static::GET_Delete($items);
			}

			if (Request::POST('bulk') == 'enable') {
				//
				static::MultiSetStatus($items, 1);
			}

			if (Request::POST('bulk') == 'disable') {
				//
				static::MultiSetStatus($items, 0);
			}

		}

		static function MultiSetStatus($items, $status)
		{
			$model = static::Model();
			$model->where("id IN ??", $items)
				->execute();

			$model->reset();
			while ($model->next()) {
				//
				$model->status = $status;
				$model->save();
			}
		}

		static function EditForm( $model )
		{
			$fields = array();

			foreach ($model::$schema as $field => $title) {
				//
				$input = new Input($field);
				$input->Type(Input::F_TEXT)->Title($title)->Context('VALUES')->Value( $model->$field );

				$fields[$field] = $input; //->Export();
			}

			return $fields;
		}

	}

?>