<?php

	// SQL

	class SQL extends Instance
	{
		const
			SQL_SELECT = 1,
			SQL_UPDATE = 2,
			SQL_INSERT = 3,
			SQL_DELETE = 4;


		//@{
		//! SQL properties
		protected
			$ttl, $result;

		protected
			$vars = Array(),
			$args = Array();


		/**
			Class constructor
				@public
		**/
		function __construct($ttl = 60)
		{
			$this->ttl = $ttl;
			$this->select( '*' )->from( 'undefined' )->where( 'TRUE' );
			$this->offset( 0 );
		}

		public function __call($name, $args)
		{
			// request to set value
			if (isset($args[0])) {
				//
				if (in_array($name, Util::split('where|having|set'))) {
					//
					if (!isset( $this->vars[ strtolower($name) ] )) {
						$this->vars[ strtolower($name) ] = array();
					}

					$this->vars[ strtolower($name) ] = array_merge($this->vars[ strtolower($name) ], array($args[0]));
				}
				else {
					$this->vars[ strtolower($name) ] = $args[0];
				}
				unset($args[0]);

				// if any additional arguments are found, consider them sql arguments
				if (count($args) > 0) {
					foreach ($args as $arg) {
						$this->args( $arg );
					}
				}

				return $this;
			}

			// request to get value
			if (isset($this->vars[ strtolower($name) ])) {
				//
				return $this->vars[ strtolower($name) ];
			}

			return NULL;
	    }


        // $sql->select('bla, bla')->from('table')->where('blabla', $x, $y)->execute();
        // $sql->update('table')->set('blabla', $x, $y)->where('blabla', $x, $y)->execute();
        // $sql->insert('table')->set('blabla', $x, $y)->execute();
        // $sql->delete('table')->where('blabla', $x, $y)->execute();
		function sql( $count = FALSE )
		{
			switch ($this->cmd())
			{
				case SQL::SQL_SELECT:
					$sql = "SELECT ".$this->fields()." FROM ".$this->table()." AS M WHERE ".implode(' AND ', $this->where())." ";
				break;

				case SQL::SQL_UPDATE:
					$sql = "UPDATE ".$this->table()." SET ".implode(', ', $this->set())." WHERE ".implode(' AND ', $this->where())." ";
				break;

				case SQL::SQL_INSERT:
					$sql = "INSERT INTO ".$this->table()." SET ".implode(', ', $this->set())." ";
				break;

				case SQL::SQL_DELETE:
					$sql = "DELETE FROM ".$this->table()." WHERE ".implode(' AND ', $this->where())." ";
				break;
			}

			if ($this->group())
				$sql .= "GROUP BY ".$this->group()." ";

			if ($this->having())
				$sql .= "HAVING ".implode(' AND ', $this->having())." ";

			if ($this->order())
				$sql .= "ORDER BY ".$this->order()." ";

			if ($this->limit() && !$count)
				$sql .= "LIMIT ".$this->offset().", ".$this->limit()." ";

			return $sql;
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
			if (!count($args)) {
				$args = $this->args();
			}
			$sql = $this->sql();

			$this->result = DB::Execute($sql, $args, $this->ttl);

			//print_r( $this->result );
			//print '<hr>';

			return $this->result;
		}

		function count()
		{
			$args = $this->args();
			$sql = $this->sql( $count = TRUE );

			$result = DB::Fetch('SELECT COUNT(*) AS `count` FROM ('.$sql.') AS `T`', $args, $this->ttl);
			return $result;
		}

		function found()
		{
			return count($this->result);
		}

		function page($index, $numrows = null)
		{
			$this->limit( $numrows ? $numrows : Conf::get('UI:ITEMSPERPAGE') );
			$this->offset( $index * $this->limit() );

			return $this;
		}

		function args()
		{
			$args = func_get_args();

			if ($args) {
				//
				$this->args = array_merge($this->args, func_get_args());
				return $this;
			}

			return $this->args;
		}

		function from($table)
		{
			return $this->table( $table );
		}

		function select($fields)
		{
			$this->fields( $fields );

			return $this->cmd( SQL::SQL_SELECT );
		}

		function insert($table)
		{
			$this->table( $table );

			return $this->cmd( SQL::SQL_INSERT );
		}

		function update($table)
		{
			$this->table( $table );

			return $this->cmd( SQL::SQL_UPDATE );
		}

		function delete($table)
		{
			$this->table( $table );

			return $this->cmd( SQL::SQL_DELETE );
		}

	}

?>