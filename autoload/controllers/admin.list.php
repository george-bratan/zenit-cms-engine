<?php

	// AdminList

	class AdminList extends AdminPage
	{
		static
			$TITLE = 'List',
			$IDENT = 'list';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/settings.png',
				'LARGE' => 'icon.large/settings.png',
			);

		static
			$PERMISSION = Array();


		static function OnLoad()
		{
			if (isset(static::$PERMISSION['list'])) {
				//
				if (Auth::Grant(static::$AUTH . '.list')) {
					//
					$token = Util::Split( Session::Get('ACCOUNT.TOKEN') );
					$token[] = static::$AUTH . '.page';

					Session::Set('ACCOUNT.TOKEN', implode('|', $token));
				}
			}
		}


		// override to return a model used by this list
		static function Model($id = NULL)
		{
			$class = get_called_class();
			trigger_error(sprintf(Lang::ERR_VirtualMethod, "{$class}::Model()"));
		}


		static function Get()
		{
			static::CallHandler($default = 'list');
		}

		static function Post()
		{
			static::CallHandler($default = 'list');
		}

		static function Where($model)
		{
			return $model;
		}

		static function ApplyFilter($model)
		{
			$filters = static::FilterForm();

			$args = func_get_args();
			$SESSION_PATH = isset($args[1]) ? $args[1] : static::$IDENT.".filter";

			if (Session::Exists($SESSION_PATH)){
				foreach (Session::Get($SESSION_PATH) as $field => $value) {
					//
					if (!isset($filters[$field])) {
						//
						continue;
					}

					if (!$model->isdef($field)) {

						if ($filters[$field]->type == Input::F_DATERANGE) {
							//
							if (isset($value[0])) {
								//
								$model->where("DATE(`{$field}`) >= ?")
									->args($value[0]);
							}
							if (isset($value[1])) {
								//
								$model->where("DATE(`{$field}`) <= ?")
									->args($value[1]);
							}
						}
						elseif (is_array($value)) {
							//
							if ($value) {
								//
								$model->where("`{$field}` IN ??")
									->args($value);
							}
						}
						else {
							$value = str_replace(' ', '%', $value);
							$model->where("`{$field}` LIKE CONCAT('%', ?, '%')")
								->args($value);
						}
					}
				}
			}

			if (Session::Exists($SESSION_PATH)){
				foreach (Session::Get($SESSION_PATH) as $field => $value) {
					//
					if (!isset($filters[$field])) {
						//
						continue;
					}

					if ($model->isdef($field)) {
						//
						$value = str_replace(' ', '%', $value);
						$model->having("`{$field}` LIKE CONCAT('%', ?, '%')")
								->args($value);
					}
				}
			}

			return $model;
		}

		static function GET_List($page = 0)
		{
			$args = func_get_args();
			$SESSION_PATH = isset($args[1]) ? $args[1] : static::$IDENT.".filter";

			$model = static::Model();

			$model = static::Where($model);
			$model = static::ApplyFilter($model, $SESSION_PATH);

			if (Session::Exists(static::$IDENT.".order")) {
				//
				foreach (Session::Get(static::$IDENT.".order") as $field => $value) {
					//
					if (!$model->isdef($field)) {
						//
						$model->order("`{$field}` {$value}");
					}
				}
			}

			//$model->page( $page )->where( $cond, $args );
			$model->page( $page )
				->execute();

			UI::nset('LIST', $model->export());
			UI::nset('PAGES', array(
				'total' => $model->count(),
				'count' => $model->limit() ? ceil($model->count() / $model->limit()) : 1,
				'index' => $page,
			));


			// setup fields that are visible in the table (user selected)
			$fields = array();
			foreach ($model::$public as $field => $title) {
				//
				if (Session::Exists(static::$IDENT.".fields.{$field}") ? Session::Get(static::$IDENT.".fields.{$field}") : in_array($field, $model::$default)) {
					//
					$fields[$field] = $model::$public[$field];
				}
			}
			UI::nset('FIELDS', $fields);
			UI::nset('FIXED', array());

			UI::nset('FORMAT.email',
				function($record) {
					if ($record['email'])
						return '<a href="mailto:'.$record['email'].'">'.$record['email'].'</a>';
					return FALSE;
				}
			);
			UI::nset('FORMAT.phone',
				function($record) {
					if ($record['phone'])
						return '<a href="skype:'.$record['phone'].'">'.$record['phone'].'</a>';
					return FALSE;
				}
			);

			UI::nset('ORDER', Session::Get(static::$IDENT.".order"));

			UI::nset('TOOLBAR.reset', array(
					'url' => Request::$URL.'/reset',
					'rel' => 'post',
					'icon' => 'icon.small/refresh.png',
					'title' => 'Reset'
				)
			);

			if (UI::exists('TOOLBAR.advanced'))
			{
				$adv = UI::get('TOOLBAR.advanced');
				UI::clear('TOOLBAR.advanced');
				UI::nset('TOOLBAR.advanced', $adv);
			}

			UI::nset('TOOLBAR.filter', array(
					'url' => Request::$URL.'/filter',
					'rel' => 'modal',
					'icon' => 'icon.small/settings.png',
					'title' => 'Filter'
				)
			);

			if (Session::Exists(static::$IDENT.".filter")) {
				UI::set('SUBSECTION', '(filtered)');
			}


			UI::set('CONTENT', UI::Render('admin/.shared.list.php'));

			parent::Get();
		}

		static function POST_Order()
		{
			if (Request::POST('ORDER')) {
				//
				$model = static::Model();
				Session::Clear(static::$IDENT.".order");

				foreach (Request::POST('ORDER') as $field => $value) {
					//
					if (in_array($field, array_keys($model::$public)) && in_array(strtolower($value), array('asc', 'desc'))) {
						//
						Session::Set(static::$IDENT.".order.{$field}", $value);
					}
				}
			}
		}

		static function GET_Filter()
		{
			UI::nset('TITLE', 'Filter: '.static::$TITLE);

			UI::nset('FILTER', static::FilterForm());
			UI::nset('FIELDS', static::FieldsForm());

			UI::set('CONTENT', UI::Render('admin/.shared.filters.php'));

			parent::Popup();
		}

		// may receive args[0] and args[1], as POST_PATH AND SESSION_PATH
		static function POST_Filter()
		{
			$model = static::Model();

			if (Request::POST('FIELDS')) {
				//
				$params = Request::POST('FIELDS');
				$params = Util::FilterChecked( $params );
				if (count($params)) {
					//
					Session::Clear(static::$IDENT.".fields");

					foreach ($model::$public as $field => $title) {
						//
						Session::Set(static::$IDENT.".fields.{$field}", in_array($field, $params));
					}
				}
			}

			$args = func_get_args();
			$POST_PATH = isset($args[0]) ? $args[0] : 'FILTER';
			$SESSION_PATH = isset($args[1]) ? $args[1] : static::$IDENT.'.filter';

			$params = Request::POST($POST_PATH);
			Session::Clear($SESSION_PATH);

			$filters = static::FilterForm();
			foreach ($model::$filters as $field => $title) {
				//
				if (isset($params[ $field ])) {
					//
					if ($params[ $field ]) {
						//
						if ($filters[$field]->type == Input::F_DATERANGE) {
							//
							if (!$params[ $field ][0]) {
								unset($params[ $field ][0]);
							}
							if (!$params[ $field ][1]) {
								unset($params[ $field ][1]);
							}
						}
						elseif (is_array($params[ $field ])) {
							//
							$params[ $field ] = Util::FilterChecked( $params[ $field ] );
						}

						if (is_array($params[ $field ]) && !count($params[ $field ])) {
							continue;
						}

						Session::Set($SESSION_PATH.".{$field}", $params[ $field ]);
					}
				}
			}
		}

		static function POST_AddFilter()
		{
			$model = static::Model();
			$params = Request::POST('FILTER');

			foreach ($params as $field => $value) {
				//
				if ($params[ $field ] && isset($model::$filters[$field])) {
					//
					Session::Set(static::$IDENT.".filter.{$field}", $params[ $field ]);
				}
			}
		}

		static function POST_Reset()
		{
			Session::Clear(static::$IDENT.".fields");
			Session::Clear(static::$IDENT.".filter");
		}

		static function GET_Page()
		{
			static::GET_List( Request::URL('id') );
		}

		static function FieldsForm()
		{
			$model = static::Model();

			$fields = array();

			foreach ($model::$public as $field => $title) {
				//
				$input = new Input($field);
				$input->Type(Input::F_BOOL)->Title($title)->Context('FIELDS')->Value(
					Session::Exists(static::$IDENT.".fields.{$field}") ? Session::Get(static::$IDENT.".fields.{$field}") : in_array($field, $model::$default)
				);

				$fields[] = $input; //->export();
			}

			return $fields;
		}

		// may receive session path as arg[0] where it gets the stored filter values
		static function FilterForm()
		{
			$model = static::Model();

			$args = func_get_args();
			$SESSION_PATH = isset($args[0]) ? $args[0] : static::$IDENT.".filter";

			$filters = array();

			foreach ($model::$filters as $field => $title) {
				//
				$input = new Input($field);
				$input->Type(Input::F_TEXT)->Title($title)->Context('FILTER')->Value(
					Session::Exists($SESSION_PATH.".{$field}") ? Session::Get($SESSION_PATH.".{$field}") : '' );

				$filters[$field] = $input; //->export();
			}

			return $filters;
		}

	}

?>