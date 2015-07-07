<?php

	// Object

	class Object
	{
		protected static
			$VARS = Array();

		public static function &vars()
		{
			return static::$VARS;
		}

		public static function sep()
		{
			return '.';
		}

		public static function get($key, $default = NULL)
		{
			$var = &static::vars();
			$sep = static::sep();

			$parts = explode($sep, $key);

			foreach ($parts as $part) {
				//
				if (isset($var[ $part ]))
					$var = &$var[ $part ];
				else
					return $default;
			}

			return $var;
		}

		static function set($key, $value)
		{
			$var = &static::vars();
			$sep = static::sep();

			$parts = explode($sep, $key);

			foreach ($parts as $part) {
				$var = &$var[ $part ];
			}

			$var = $value;
		}

		public static function clear($key = NULL)
		{
			$var = &static::vars();
			$sep = static::sep();

			$parts = explode($sep, $key);

			foreach ($parts as $index => $part) {
				//
				if ($index == count($parts)-1)
					unset($var[$part]);

				if (isset($var[ $part ]))
					$var = &$var[ $part ];
				else
					return;
			}
		}

		public static function exists($key)
		{
			$var = &static::vars();
			$sep = static::sep();

			$parts = explode($sep, $key);

			foreach ($parts as $part) {
				//
				if (isset($var[ $part ]))
					$var = &$var[ $part ];
				else
					return FALSE;
			}

			return TRUE;
		}

		// set array as string: "a|b|c" => array(a, b, c)
		public static function mset($index, $value)
		{
			if (!is_array($value)) {
				$value = Util::split( $value );
			}

			return static::set($index, $value);
		}

		// set only if no previous value exists
		public static function nset($index, $value)
		{
			if (!static::exists($index)) {
				//
				static::set($index, $value);
			}
		}

		// append to current value as array
		public static function aset($index, $value)
		{
			if (!static::exists($index)) {
				static::set($index, array());
			}

			$current = static::get($index);
			static::set($index, array_merge($current, $value));
		}

		public static function export()
		{
			return static::vars();
		}


	}

?>