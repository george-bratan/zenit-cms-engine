<?php

	// DB

	class DB
	{
		public static
			//! Exposed data object properties
			$pdo;

		private static
			//! schema cache
			$schema = Array(),
			//! Connection parameters
			$dsn,
			//! List of available connections
			$conn = Array();

		private static
			$inTransaction = NULL;


		/**
			Connect PDO
				@param $dsn string
				@param $user string
				@param $pw string
				@param $opt array
				@param $force boolean
				@public
		**/
		static function Connect($dsn=NULL, $user=NULL, $pass=NULL, $opt=NULL)
		{
			if (!$dsn) {
				// Using default values
				$dsn = Conf::get('DB:DSN') ? Conf::get('DB:DSN') :
					'mysql:'.
					'host='.Conf::get('DB:HOST').';'.
					//'port=3306;'.
					'dbname='.Conf::get('DB:NAME');

				$user = Conf::get('DB:USER');
				$pass = Conf::get('DB:PASS');
			}

			// Check if we already have this connection
			foreach (self::$conn as $_dsn => $_pdo) {
				if ($dsn == $_dsn) {
					self::$dsn = $_dsn;
					self::$pdo = $_pdo;
				}
			}

			if (!$opt) {
				// Append other default options
				if (extension_loaded('pdo_mysql') && preg_match('/^mysql:/', $dsn))
				{
					if (!Conf::exists('DB:ENCODING')) {
						// Default MySQL character set
						Conf::set('DB:ENCODING', 'utf8');
					}

					// PHP 5.3 bug, PDO::MYSQL_ATTR_INIT_COMMAND is no longer available, but is required, last known value 1002
					$PDO_MYSQL_ATTR_INIT_COMMAND = defined('PDO::MYSQL_ATTR_INIT_COMMAND') ? PDO::MYSQL_ATTR_INIT_COMMAND : 1002;

					$opt = array(PDO::ATTR_EMULATE_PREPARES => FALSE, $PDO_MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.Conf::get('DB:ENCODING'));
				}
				else {
					$opt = array(PDO::ATTR_EMULATE_PREPARES => FALSE);
				}
			}

			// Connect
			self::$dsn = $dsn;
			self::$pdo = new PDO(self::$dsn, $user, $pass, $opt);

			self::$conn[ self::$dsn ] = self::$pdo;
		}


		/**
			End current connection
				@public
		**/
		static function Stop()
		{
			unset(self::$conn[ self::$dsn ]);

			self::$dsn = NULL;
			self::$pdo = NULL;

			if (count(self::$conn)) {
				self::$dsn = ${array_keys(self::$conn)}[0];
				self::$pdo = ${array_values(self::$conn)}[0];
			}
		}


		/**
			Begin SQL transaction
				@public
		**/
		function Begin()
		{
			if (!self::$pdo)
				self::Connect();

			self::$inTransaction = self::$pdo->beginTransaction();
		}


		/**
			Rollback SQL transaction
				@public
		**/
		function Rollback()
		{
			if (!self::$pdo)
				self::Connect();

			self::$pdo->Rollback();
			self::$inTransaction = NULL;
		}


		/**
			Commit SQL transaction
				@public
		**/
		function Commit()
		{
			if (!self::$pdo)
				self::Connect();

			self::$pdo->Commit();
			self::$inTransaction = NULL;
		}


		/**
			Convenience method for direct SQL queries (static call)
				@return array
				@param $cmds mixed
				@param $args mixed
				@param $ttl int
				@param $db string
				@public
		**/
		static function SQL($cmds, array $args=NULL, $ttl=0)
		{
			return self::Execute($cmds, $args, $ttl);
		}


		/**
			Process SQL statement(s)
				@return array
				@param $cmds mixed
				@param $args array
				@param $ttl int
				@public
		**/
		static function Execute($cmds, array $args=NULL, $ttl=0)
		{
			if (!self::$pdo)
				self::Connect();

			$stats = &Application::$STATS;
			if (!isset($stats[ self::$dsn ]))
			{
				$stats[ self::$dsn ]=array(
					'cache' => array(),
					'queries' => array()
				);
			}

			$batch = is_array($cmds);
			if (!$batch) {
				$cmds = array($cmds);
				$args = array($args);
			}
			elseif (!self::$inTransaction) {
				self::Begin();
			}

			foreach (array_combine($cmds, $args) as $cmd => $arg)
			{
				$hash = 'sql.'.Util::hash($cmd.print_r($args, true));
				$cached = Cache::cached($hash);

				if ($ttl && $cached && $_SERVER['REQUEST_TIME'] - $cached < $ttl)
				{
					// Gather cached queries for profiler
					if (!isset($stats[self::$dsn]['cache'][$cmd]))
						$stats[self::$dsn]['cache'][$cmd] = 0;

					$stats[self::$dsn]['cache'][$cmd]++;
					$result = Cache::get($hash);
				}
				else {
					if (is_null($arg))
					{
						$query = self::$pdo->query($cmd);
					}
					else
					{
						// check for double question symbol => array
						$offset = 0;
						while ($offset = strpos($cmd, '??', $offset)) {
							$index = substr_count(substr($cmd, 0, $offset), '?');

							$items = $arg[$index];

							if (is_array($items)) {
								if (count($items)) {
									$placeholder = '('.implode(',', array_fill(0, count($items), '?')).')';

									$cmd = substr_replace($cmd, $placeholder, $offset, 2);

									// collapse array argument inside the argument list
									array_splice($arg, $index, 1, $arg[$index]);
								}
								else {
									$cmd = substr_replace($cmd, '(?)', $offset, 2);
									array_splice($arg, $index, 1, array(0));
								}
							}

							$offset++;
						}

						if ($ttl == 11) {
							print $cmd.'<hr>';
							print_r($arg); die();
						}

						$query = self::$pdo->prepare($cmd);
						$ok = TRUE;

						if (!is_object($query))
						{
							$ok = FALSE;
						}
						else
						{
							foreach ($arg as $key => $value)
							{
								// if question-mark notation is used, PDO expects 1-based keys
								if (is_int($key)) {
									$key++;
								}

								if (is_array($value)) {
									$type = $value[1];
									$value = $value[0];
								}
								else {
									$type = self::type($value);
								}

								if (!$query->bindvalue($key, $value, $type)) {
									$ok = FALSE;
									break;
								}
							}

							if ($ok) {
								$ok = $query->execute();
							}
						}

						if (!$ok)
						{
							if (self::$inTransaction)
								self::Rollback();

							trigger_error(sprintf(Lang::ERR_ExecFail, $cmd));
							return FALSE;
						}
					}

					// Check SQLSTATE
					foreach (array(self::$pdo, $query) as $obj)
					{
						if ($obj->errorCode() != PDO::ERR_NONE)
						{
							if (self::$inTransaction)
								self::Rollback();

							$error = $obj->errorinfo();
							trigger_error($error[2]);
							return FALSE;
						}
					}

					if (preg_match('/^\s*(?:INSERT)\s/i', $cmd)) {

						$result = self::$pdo->lastInsertId();
					}
					elseif (preg_match('/^\s*(?:UPDATE|DELETE)\s/i', $cmd)) {

						$result = $query->rowCount();
					}
					else {

						$result = $query->fetchAll(PDO::FETCH_ASSOC);
					}


					if ($ttl)
						Cache::set($hash, $result, $ttl);

					// Gather real queries for profiler
					if (!isset($stats[self::$dsn]['queries'][$cmd]))
						$stats[self::$dsn]['queries'][$cmd] = 0;

					$stats[self::$dsn]['queries'][$cmd]++;
				}
			}

			if ($batch && !self::$inTransaction)
				self::Commit();

			return $result;
		}


		/**
			Return auto-detected PDO data type of specified value
				@return int
				@param $val mixed
				@public
		**/
		static function type($val)
		{
			if (is_null($val)) {
				return PDO::PARAM_NULL;
			}

			if (is_bool($val)) {
				return PDO::PARAM_BOOL;
			}

			if (is_string($val)) {
				return PDO::PARAM_STR;
			}

			if (is_int($val)) {
				return PDO::PARAM_INT;
			}

			if (is_float($val)) {
				return PDO::PARAM_STR;
			}

			return PDO::PARAM_LOB;
		}

		/**
			Return schema of specified table
				@return array
				@param $table string
				@param $ttl int
				@public
		**/
		static function Schema($table, $ttl)
		{
			if (isset(self::$schema[$table])) {
				return self::$schema[$table];
			}

			$cmd = array(
				'sqlite2?' => array(
					'PRAGMA table_info('.$table.');',
					'name','pk',1,'type'),

				'mysql' => array(
					'SHOW columns FROM '.Conf::get('DB:NAME').'.'.$table.';',
					'Field', 'Key', 'PRI', 'Type'),

				'(mysql|mssql|sybase|dblib|pgsql)' => array(
					'SELECT c.column_name AS field, t.constraint_type AS pkey '.
					'FROM information_schema.columns AS c '.
					'LEFT OUTER JOIN '.
						'information_schema.key_column_usage AS k ON '.
							'c.table_name  = k.table_name AND '.
							'c.column_name = k.column_name '.
							(Conf::get('DB:NAME') ?
								'AND c.table_schema=k.table_schema ' : '').

					'LEFT OUTER JOIN '.
						'information_schema.table_constraints AS t ON '.
							'k.table_name = t.table_name AND '.
							'k.constraint_name = t.constraint_name '.
							(Conf::get('DB:NAME') ?
								'AND k.table_schema = t.table_schema ':'').

					'WHERE '.
						'c.table_name = "'.$table.'"'.
						(Conf::get('DB:NAME') ?
							('AND c.table_schema = "'.Conf::get('DB:NAME').'"'):'').
					';',
					'field', 'pkey', 'PRIMARY KEY', 'data_type')
			);

			$match = FALSE;
			foreach ($cmd as $backend => $val)
			{
				if (preg_match('/^'.$backend.'$/', strtolower(Conf::get('DB:TYPE')))) {
					$match = TRUE;
					break;
				}
			}

			if (!$match) {
				trigger_error(Lang::ERR_DBEngine);
				return FALSE;
			}

			$result = self::Execute($val[0], NULL, $ttl);

			if (!$result) {
				trigger_error(sprintf(Lang::ERR_Schema, $table));
				return FALSE;
			}

			self::$schema[$table] = array(
				'result' 	=> $result,
				'field' 	=> $val[1],
				'pkname' 	=> $val[2],
				'pkval' 	=> $val[3],
				'type' 		=> $val[4]
			);

			return self::$schema[$table];
		}

		/**
			Fetch one row or value if row contains one column
				@return array
				@param $cmds mixed
				@param $args array
				@param $ttl int
				@public
		**/
		static function Fetch($cmds, array $args=NULL, $ttl=0)
		{
			$record = self::Row($cmds, $args, $ttl);

			if (!$record) {
				return NULL;
			}

			if (count($record) == 1) {
				return array_pop($record);
			}

			return $record;
		}

		// alias for DB::Fetch
		static function Value($cmds, array $args=NULL, $ttl=0)
		{
			$record = self::Row($cmds, $args, $ttl);

			if (!$record) {
				return NULL;
			}

			if (count($record) > 0) {
				return array_pop($record);
			}

			return NULL;
		}

		/**
			Fetch one row as an array
				@return array
				@param $cmds mixed
				@param $args array
				@param $ttl int
				@public
		**/
		static function Row($cmds, array $args=NULL, $ttl=0)
		{
			$result = self::Execute($cmds, $args, $ttl);

			if (!count($result)) {
				return NULL;
			}

			return $result[0];
		}

		/**
			Fetch one column as an array
				@return array
				@param $cmds mixed
				@param $args array
				@param $ttl int
				@public
		**/
		static function Column($cmds, array $args=NULL, $ttl=0)
		{
			$result = self::Execute($cmds, $args, $ttl);

			if (!count($result)) {
				return array();
			}

			$column = array();

			foreach ($result as $row) {
				$column[] = array_shift($row);
			}

			return $column;
		}

		/**
			Fetch one column as an array
				@return array
				@param $cmds mixed
				@param $args array
				@param $ttl int
				@public
		**/
		static function AssociativeColumn($cmds, array $args=NULL, $ttl=0)
		{
			$result = self::Execute($cmds, $args, $ttl);

			if (!count($result)) {
				return array();
			}

			$column = array();

			foreach ($result as $row) {
				$column[ array_shift($row) ] = array_shift($row);
			}

			return $column;
		}

	}

?>