<?php

	// Permission

	class PermissionGroups extends AdminModule
	{
		static
			$TITLE  = 'Groups',
			$IDENT  = 'permission.group';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.group.png',
				'LARGE' => 'icon.large/user.group.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Groups',
				'details' 	=> 'View Details',
				'edit' 		=> 'Add/Edit',
				'delete' 	=> 'Delete',
				'access'	=> 'View Access Rights',
			);

		static
			$AUTH = 'admin.permission.group';



		static function Model($id = NULL)
		{
			return Model::UserGroup($id);
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.access', Array(
					'rel' => 'modal',
					'handler' => 'access',
					'icon' => 'icon.small/key.png',
					'title' => 'Access Rights',
				));
			}

			parent::GET_List($page);
		}

		static function GET_Access()
		{
			if (!Request::URL('id'))
			{
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');

				UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

				parent::Popup();
				return;
			}


			$used = array();
			$permission = array();

			foreach (Admin::$SITEMAP as $key => $value)
			{
				//class may be key or value, depending on whether it has children or not
				$class = (is_array($value)) ? $key : $value;

				if ($class::$AUTH) {
					if (!in_array($class::$AUTH, $used)) {
						$permission[ $class::$TITLE ][ $class::$AUTH ] = $class::$TITLE;
						$used[] = $class::$AUTH;

						if (isset($class::$PERMISSION)) {
							//
							foreach ($class::$PERMISSION as $key => $title) {
								//
								if ($key)
									$permission[ $class::$TITLE ][ $class::$AUTH .'.'. $key ] = $title;
							}
						}
					}
				}

				// test if children are also a set of controllers

				if (is_array($value)) {
					//
					foreach ($value as $subclass) {
						//
						if ($subclass::$AUTH) {
							if (!in_array($subclass::$AUTH, $used)) {
								$permission[ $class::$TITLE ][ $subclass::$AUTH ] = $subclass::$TITLE;
								$used[] = $subclass::$AUTH;

								if (isset($subclass::$PERMISSION)) {
									//
									foreach ($subclass::$PERMISSION as $key => $title) {
										//
										if ($key)
											$permission[ $class::$TITLE.': '.$subclass::$TITLE ][ $subclass::$AUTH .'.'. $key ] = $title;
									}
								}
							}
						}
					}
				}
			}

			$model = static::Model( intval( Request::URL('id') ) );

			// TOKEN
            $token = Util::split($model->token);

			// UI
			UI::set('USER', $model->record());

			UI::set('TITLE', 'Manage Permission Rights: '.$model->name);
			UI::set('ACCESS', $permission);
			UI::set('TOKEN', $token);


			UI::set('CONTENT', UI::Render('admin/zenit.permission.access.php'));

			parent::Popup();
		}

		static function POST_Save()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (!Request::URL('id')) {
				$model->Defaults();
			}

			foreach (Request::POST('VALUES') as $field => $value) {
				//
				if ($field == 'token') {
					//
					$permission = Util::FilterChecked($value);
					$value = implode('|', $permission);
				}

				if (in_array($field, array_keys($model::$schema))) {
					//
					$model->$field = $value;
				}
			}

			$model->save();
		}

		static function GET_Save()
		{
			//
		}

		static function EditForm( $model )
		{
			// SETUP EDIT FORM
			$fields = parent::EditForm( $model );

			// we don't need no token around here, boy
			unset($fields['token']);

			return $fields;
		}
	}

?>