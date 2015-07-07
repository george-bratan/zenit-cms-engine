<?php

	// Model

	class Model extends SQL
	{
		//@{
		//! Model properties
		static
			$public, $default, $filters, $schema;

		protected
			$index, $empty, $modified;

		protected
			$fields, $adhoc, $pkeys, $types;


		/**
			Intercept calls to undefined static methods and try to initialyze s model
				@param $func string
				@param $args array
				@public
		**/
		static function __callStatic($func, array $args)
		{
		    //Application::Autoload($func, $silent = true);
		    if (class_exists('Models\\'.$func, $autoload = TRUE))
		    {
		    	//$vars = func_get_args() ; // get arguments

		    	$rc = new ReflectionClass('Models\\'.$func);
        		$model = $rc->newInstanceArgs($args);

        		$model->filter();

        		return $model;
		    }

			trigger_error(sprintf(Lang::ERR_Method, get_called_class().'::'.
				$func.'('.Util::csv($args).')'));
		}

		/**
			Return custom model
				@param $func string
				@param $args array
				@param $ttl int
				@public

		static function Fetch($sql, array $args = NULL, $ttl = 60)
		{
			//$self = __CLASS__;
			$class = get_called_class();
			$model = new $class;

			$model->sql($sql, $args, $ttl);

			return $model;
		}
		**/


		/**
			Class constructor
				@public
		**/
		function __construct($table = NULL, $ttl = 60)
		{
			parent::__construct($ttl);

			$this->fields = array();
			$this->adhoc = array();
			$this->pkeys = array();

			if ($table) {
				//
				$this->sync($table, $ttl);
			}

		}

		// return unique id
		function get_id()
		{
			$class = get_called_class();
			trigger_error(sprintf(Lang::ERR_VirtualMethod, "{$class}->get_id()"));
		}

		// return identifiable human name
		function get_name()
		{
			$class = get_called_class();
			trigger_error(sprintf(Lang::ERR_VirtualMethod, "{$class}->get_name()"));
		}

		/**
			Synchronize Model and SQL table structure
				@param $table string
				@param $ttl int
				@public
		**/
		function sync($table, $ttl=60)
		{
			if (!DB::$pdo)
				DB::Connect();

			if (method_exists($this, 'onBeforeSync') && $this->beforeSync() === FALSE)
				return;

			// Initialize Model
			///list($this->db, $this->table()) = array($db, $table);

			$this->table( $table );

			if ($schema = DB::Schema($table, $ttl))
			{
				// Populate properties
				foreach ($schema['result'] as $row)
				{
					$this->fields[ $row[$schema['field']] ] = NULL;

					if ($row[ $schema['pkname'] ] == $schema['pkval']) {
						// Save primary key
						$this->pkeys[ $row[$schema['field']] ] = NULL;
					}

					$this->types[ $row[$schema['field']] ] =
							preg_match('/int|bool/i', $row[$schema['type']], $match) ?
								constant('PDO::PARAM_'.strtoupper($match[0])) :
									PDO::PARAM_STR;
				}

				$this->empty = TRUE;
			}

			if (method_exists($this, 'onAfterSync'))
				$this->afterSync();
		}

		// Export all records
		// 	$key -> return the list with keys on the specified $key field
		function export($key = '', $depth = 0)
		{
			// make sure there are computed fields before trying to compute them
			$adhoc = FALSE;

			foreach ($this->adhoc as $field => $expr) {
				//
				if (is_null($expr) && method_exists($this, 'get_'.strtolower($field))) {
					//
					$adhoc = TRUE;
				}
			}

			// if there are no adhoc computed values AND there is no mandatory key for the resultset
			if ($adhoc == FALSE && !$key) {
				return $this->result;
			}

			// Oh dear, $adhoc == TRUE means we have to compute all adhoc fields
			// load each record and export with computed adhoc fields

			$result = array();
			foreach ($this->result as $index => $values) {
				$this->load($index);

				if ($key) {
					$result[ $this->$key ] = $this->record( $depth );
				}
				else
					$result[] = $this->record( $depth );
			}

			return $result;
		}

		// Export all current records
		// 	$key -> return the list with keys on the specified $key field
		function record($depth = 0)
		{
			$result = $this->result[ $this->index ];

			foreach ($this->adhoc as $field => $expr) {
				//
				if (is_null($expr) && method_exists($this, 'get_'.strtolower($field))) {
					//
					$result[ $field ] = call_user_func(array($this, 'get_'.strtolower($field)));

					// CONTINUE EXPORTING IF RESULT IS ITSELF A MODEL
					if ($depth) {
						//
						if (is_a($result[ $field ], 'Model')) {
							//
							$result[ $field ] = $result[ $field ]->export( $key = '', $depth - 1 ); //record( $depth - 1 );
						}
					}
				}
			}

			return $result;
		}

		/**
			SQL select statement wrapper
				@return model
				@param $sql string
				@param $args array
				@param $ttl int
				@public
		**/
		function execute($args = array())
		{
			$adhoc = '';
			if ($this->adhoc) {
				foreach ($this->adhoc as $field => $expr) {
					if ($expr) {
						$adhoc .= ', '.$expr.' AS `'.$field.'`';
					}
				}
			}

			$this->select('*'.$adhoc);

			parent::execute($args);

			if ($this->found() > 0) {
				//
				$this->load(0);
			}
		}

		/**
			Create an adhoc field
				@param $field string
				@param $expr string
				@public
		**/
		function def($field, $expr = NULL)
		{
			if (isset($this->fields[$field]))
			{
				trigger_error(sprintf(Lang::ERR_ModelConflict, $field));
				return;
			}

			if ($expr == NULL && !method_exists($this, 'get_'.strtolower($field)))
			{
				trigger_error(sprintf(Lang::ERR_ModelInvalid, $field));
				return;
			}
            if ($expr) {
				$this->fields[$field] = NULL;
			}
			$this->adhoc[$field] = $expr;
		}

		/**
			Destroy an adhoc field
				@param $field string
				@public
		**/
		function undef($field)
		{
			if (!self::isdef($field))
			{
				trigger_error(sprintf(Lang::ERR_ModelCantUndef, $field));
				return;
			}

			unset($this->fields[$field]);
			unset($this->adhoc[$field]);
		}

		/**
			Return TRUE if adhoc field exists
				@param $field string
				@public
		**/
		function isdef($field)
		{
			return array_key_exists($field, $this->adhoc);
		}

		/**
			Return value of mapped field
				@return mixed
				@param $field string
				@public
		**/
		function __get($field)
		{
			//if (isset($this->fields[$field])) {
			if (array_key_exists($field, $this->fields)) {
				return $this->fields[$field];
			}

			if (method_exists($this, 'get_'.strtolower($field))) {
				return call_user_func(array($this, 'get_'.strtolower($field)));
			}

			return NULL;
		}

		/**
			Assign value to mapped field
				@return bool
				@param $field string
				@param $val mixed
				@public
		**/
		function __set($field, $val)
		{
			if (array_key_exists($field, $this->fields) && !array_key_exists($field, $this->adhoc))
			{
				// modified
				if ($this->fields[$field] != $val && !$this->modified)
					$this->modified = TRUE;

				$this->fields[$field] = $val;

				if (!is_null($val))
					$this->empty = FALSE;

				$this->result[ $this->index ][ $field ] = $val;

				return TRUE;
			}

			if (method_exists($this, 'set_'.strtolower($field))) {
				return call_user_func(array($this, 'set_'.strtolower($field)), $val);
			}

			if (self::isdef($field)) {
				trigger_error(sprintf(Lang::ERR_ModelReadOnly, $field));
			}

			return FALSE;
		}

		/**
			Trigger error in case a field is unset
				@param $field string
				@public
		**/
		function __unset($field)
		{
			trigger_error(sprintf(Lang::ERR_ModelCantUnset, $field));
		}

		/**
			Return TRUE if mapped field is set
				@return bool
				@param $field string
				@public
		**/
		function __isset($field)
		{
			return isset($this->fields[$field]);
		}


		/**
			Insert record/update database
				@public
		**/
		function save($delayed = false)
		{
			if ($this->empty || method_exists($this, 'onBeforeSave') && $this->onBeforeSave() === FALSE)
				return;

			$new = TRUE;

			if ($this->pkeys) {
				// If all primary keys are NULL, this is a new record
				foreach ($this->pkeys as $pkey) {
					if (!is_null($pkey)) {
						$new = FALSE;
						break;
					}
				}
			}

			$list_adhoc = array_keys($this->adhoc);

			if ($new)
			{
				// Insert record
				$fields = $values = '';
				foreach ($this->fields as $field => $val)
				{
					if (!in_array($field, $list_adhoc))
					{
						$fields .= ($fields ? ',' : '').'`'.$field.'`';

						if (substr($val, 0, 1) == '@') {
							//
							$values .= ($values ? ',' : '').substr($val, 1);
						}
						else {
							//
							$values .= ($values ? ',' : '').':'.$field;
							$bind[':'.$field] = array($val, $this->types[$field]);
						}
					}
				}



                if ($delayed)
					DB::Execute('INSERT DELAYED INTO '.$this->table().' ('.$fields.') '.'VALUES ('.$values.');', $bind);
				else
					DB::Execute('INSERT INTO '.$this->table().' ('.$fields.') '.'VALUES ('.$values.');', $bind);

				if (array_key_exists('id', $this->fields) && !array_key_exists('id', $this->adhoc)) {
					$this->fields['id'] = DB::$pdo->lastInsertId();
				}
			}
			elseif ($this->modified)
			{
				// Update record
				$set = $cond = '';
				foreach ($this->fields as $field => $val)
				{
					if (!in_array($field, $list_adhoc))
					{
						if (substr($val, 0, 1) == '@') {
							//
							$set .= ($set ? ',' : '').'`'.$field.'`'.'='.substr($val, 1);
						}
						else {
							//
							$set .= ($set ? ',' : '').'`'.$field.'`'.'=:'.$field;
							$bind[':'.$field] = array($val, $this->types[$field]);
						}
					}
				}

				// Use primary keys to find record
				foreach ($this->pkeys as $pkey => $val)
				{
					$cond .= ($cond ? ' AND ' : '').$pkey.'=:c_'.$pkey;
					$bind[':c_'.$pkey] = array($val, $this->types[$pkey]);
				}

				if ($set) {
					DB::Execute('UPDATE '.$this->table().' SET '.$set.($cond ? (' WHERE '.$cond) : '').';', $bind);

					//print 'UPDATE '.$this->table().' SET '.$set.($cond ? (' WHERE '.$cond) : '').'; <br>'.print_r($bind, true).'<hr>';
				}
			}

			if ($this->pkeys) {
				// Update primary keys with new values
				foreach (array_keys($this->pkeys) as $pkey)
					$this->pkeys[$pkey] = $this->fields[$pkey];
			}

			if (method_exists($this, 'onAfterSave')) {
				$this->onAfterSave();
			}
		}

		/**
			Delete record/s
				@param $cond mixed
				@param $force boolean
				@public
		**/
		function delete()
		{
			if ($this->empty) {
				trigger_error(Lang::ERR_ModelEmpty);
				return FALSE;
			}

			if (method_exists($this, 'onBeforeDelete') && $this->onBeforeDelete() === FALSE)
				return;

			$cond = '';
			// Use primary keys to identify record
			foreach ($this->pkeys as $pkey => $val)
			{
				$cond .= ($cond ? ' AND ' : '').$pkey.'=:c_'.$pkey;
				$bind[':c_'.$pkey] = array($val, $this->types[$pkey]);
			}

			if ($cond) {
				//
				DB::Execute('DELETE FROM '.$this->table().' WHERE '.$cond, $bind);
			}

			if (method_exists($this, 'onAfterDelete')) {
				$this->onAfterDelete();
			}
		}

		/**
			Return TRUE if Model is empty
				@return bool
				@public
		**/
		function dry()
		{
			return $this->empty;
		}

		/**
			Load Model with elements from array variable;
			Adhoc fields are not modified
				@param $name string
				@param $keys string
				@public
		**/
		function copy($var, $keys=NULL)
		{
			$keys = is_null($keys) ? array_keys($var) : Util::split($keys);

			$list_var = array_keys($var);
			$list_fields = array_keys($this->fields);
			$list_adhoc = array_keys($this->adhoc);

			foreach ($keys as $key) {

				if (in_array($key, $list_var)) {

					if (in_array($key, $list_fields) && !in_array($key, $list_adhoc))
						$this->fields[$key] = $var[$key];
				}
			}

			$this->empty = FALSE;
		}

		/**
			Populate array variable with Model properties
				@param $name string
				@param $keys string
				@public
		**/
		function copyTo(&$var, $keys=NULL) {
			if ($this->empty) {
				trigger_error(Lang::ERR_ModelEmpty);
				return FALSE;
			}

			$list = array_diff(preg_split('/[\|;,]/', $keys, 0, PREG_SPLIT_NO_EMPTY), array(''));
			$keys = array_keys($this->fields);

			foreach ($keys as $key)
			{
				if (empty($list) || in_array($key, $list))
				{
					if (in_array($key, array_keys($this->fields)))
						$var[$key] = $this->fields[$key];
				}
			}
		}

		/**
			Load Model with nth record relative to current position
				@return mixed
				@param $ofs int
				@public
		**/
		function next($offset = 1) {
			if ($this->empty) {
				trigger_error(Lang::ERR_ModelEmpty);
				return FALSE;
			}

			return $this->load($this->index + $offset);
		}

		/**
			Return previous record
				@return array
				@public
		**/
		function prev() {
			return $this->next(-1);
		}

		/**
			load Model with first record that matches criteria
				@return mixed
				@param $cond mixed
				@param $seq string
				@param $ofs int
				@public
		**/
		function load($offset = 0)
		{
			$this->reset();

			if ($offset > -1 && $offset < count($this->result))
			{
				if (method_exists($this, 'onBeforeLoad') && $this->onBeforeLoad() === FALSE)
					return;

				// Load Model
				foreach ($this->fields as $field => $val)
				{
					if (!isset($this->result[ $offset ][ $field ]))
						continue;

					$this->fields[$field] = $this->result[ $offset ][ $field ];

					if (array_key_exists($field, $this->pkeys))
						$this->pkeys[$field] = $this->result[ $offset ][ $field ];
				}

				$this->index = $offset;
				$this->empty = FALSE;

				if (method_exists($this, 'onAfterLoad'))
					$this->onAfterLoad();

				return $this;
			}

			return FALSE;
		}

		/**
			Reset Model position
				@public
		**/
		function reset()
		{
			if ($this->fields) {
				foreach (array_keys($this->fields) as $field)
					$this->fields[$field] = NULL;
			}

			if ($this->pkeys) {
				foreach (array_keys($this->pkeys) as $pkey)
					$this->pkeys[$pkey] = NULL;
			}

			//$this->empty = TRUE;
			$this->modified = NULL;
			$this->index = -1;
		}

		// set default values before saving a new record
		function defaults()
		{
			foreach ($this->fields as $field => $value) {
				// if not primary key and not computed, assign an empty value
				if (!in_array($field, array_keys($this->pkeys)) && !in_array($field, array_keys($this->adhoc))) {
					//
					$this->$field = '';
				}
			}
		}

		function filter()
		{
			$this->where('`status` > -1');

			return $this;
		}

		function slice($key)
		{
			$export = $this->export();

			$result = array();
			foreach ($export as $record) {
				$result[] = $record[ $key ];
			}

			return $result;
		}

	}

?>