<?php

	// Util

	class Util
	{
		//

		/**
			Split pipe-, semi-colon, comma-separated string
				@return array
				@param $str string
				@public
		**/
		static function split($str)
		{
			//if (is_array($str))
			//	return $str;

			return array_map('trim',
				preg_split('/[|;,]/', $str, 0, PREG_SPLIT_NO_EMPTY));
		}

		/**
			Convert Windows double-backslashes to slashes; Also for
			referencing namespaced classes in subdirectories
				@return string
				@param $str string
				@public
		**/
		static function fixslashes($str)
		{
			return $str ?
				strtr($str, '\\', '/') : $str;
		}

		/**
			Generate Base36/CRC32 hash code
				@return string
				@param $str string
				@public
		**/
		static function hash($str)
		{
			return str_pad(base_convert(sprintf('%u', crc32($str)), 10, 36), 7, '0', STR_PAD_LEFT);
		}

		/**
			Convert special characters to HTML entities using globally-
			defined character set
				@return string
				@param $str string
				@param $all boolean
				@public
		**/
		static function htmlencode($str, $all = FALSE) {

			return $all ?
				htmlentities($str, ENT_COMPAT, Conf::get('ENCODING'), TRUE) :
				htmlspecialchars($str, ENT_COMPAT, Conf::get('ENCODING'), TRUE);
		}

		/**
			Convert HTML entities back to their equivalent characters
				@return string
				@param $str string
				@param $all boolean
				@public
		**/
		static function htmldecode($str, $all = FALSE) {

			return $all ?
				html_entity_decode($str, ENT_COMPAT, Conf::get('ENCODING')) :
				htmlspecialchars_decode($str, ENT_COMPAT);
		}

		/**
			Flatten array values and return as CSV string
				@return string
				@param $args mixed
				@public
		**/
		static function csv($args)
		{
			if (!is_array($args))
				$args = array($args);

			$str = '';
			foreach ($args as $key => $val)
			{
				$str .= $str ? ',' : '';

				if (is_string($key))
					$str .= var_export($key, TRUE).'=>';

				$str .= is_array($val) ?
					'array('.self::csv($val).')' : self::stringify($val);
			}

			return $str;
		}

		/**
			Convert PHP expression/value to string
				@return string
				@param $val mixed
				@public
		**/
		static function stringify($val)
		{
			$pattern = '/\s+=>\s+/';
			$replace = '=>';

			if (is_object($val) && !method_exists($val,'__set_state')) {
				if (method_exists($val,'__toString')) {
					preg_replace($pattern, $replace, var_export((string)stripslashes($val),TRUE));
				}
				else {
					preg_replace($pattern, $replace, 'object:'.get_class($val));
				}
			}
			else {
				preg_replace($pattern, $replace, var_export($val, TRUE));
			}

			/*
			return preg_replace('/\s+=>\s+/', '=>',
				is_object($val) && !method_exists($val,'__set_state') ?
					(method_exists($val,'__toString') ?
						var_export((string)stripslashes($val),TRUE):
						('object:'.get_class($val))):
					var_export($val, TRUE));
			*/
		}


		/**
			Create folder; Trigger error and return FALSE if script has no
			permission to create folder in the specified path
				@param $name string
				@param $perm int
				@public
		**/
		static function mkdir($name, $perm = 0755)
		{
			if (!is_writable(dirname($name)) && function_exists('posix_getpwuid'))
			{
				$uid = posix_getpwuid( posix_geteuid() );

				trigger_error(sprintf(Lang::ERR_Write, $uid['name'], realpath(dirname($name))));

				return FALSE;
			}

			// Create the folder
			umask(0);
			mkdir($name, $perm);
		}


		/**
			Remove HTML tags (except those enumerated) to protect against
			XSS/code injection attacks
				@return mixed
				@param $input string
				@param $tags string
				@public
		**/
		static function scrub($input, $tags=NULL)
		{
			if (is_array($input)) {
				foreach ($input as &$val)
					$val = self::scrub($val, $tags);
			}

			if (is_string($input)) {
				if ($tags != '*') {

					if (is_string($tags))
						$tags = '<'.implode('><', Util::split($tags)).'>';

					$input = strip_tags($input, $tags);
				}
			}

			return $input;
		}

		/**
			Convert camelCase class names to camel.case file name
				@return string
				@param $str string
				@public
		**/
		static function cameltofile($str)
		{
			$str[0] = strtolower($str[0]);

			$pos = 0;
			while ($pos = strpos($str, '\\', $pos+1)) {
				//
				$str[$pos+1] = strtolower($str[$pos+1]);
			}

			$func = create_function('$c', 'return "." . strtolower($c[1]);');
			return preg_replace_callback('/([A-Z])/', $func, $str);
		}

		/**
			Convert camel.case file names to camelCase class names
				@param    string   $str                     String in underscore format
				@param    bool     $capitalise_first_char   If true, capitalise the first char in $str
				@return   string                            $str translated into camel caps
		*/
		static function filetocamel($str, $capitalise_first_char = false)
		{
			if($capitalise_first_char) {
				$str[0] = strtoupper($str[0]);
			}

			$func = create_function('$c', 'return strtoupper($c[1]);');
			return preg_replace_callback('/\.([a-z])/', $func, $str);
		}



		// Detect encoding
		static function Encoding($buffer)
		{
			// Unicode BOM is U+FEFF, but after encoded, it will look like this.
			$UTF32_BIG_ENDIAN_BOM = chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF);
			$UTF32_LITTLE_ENDIAN_BOM = chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00);
			$UTF16_BIG_ENDIAN_BOM = chr(0xFE) . chr(0xFF);
			$UTF16_LITTLE_ENDIAN_BOM = chr(0xFF) . chr(0xFE);
			$UTF8_BOM = chr(0xEF) . chr(0xBB) . chr(0xBF);

			///$contents = file_get_contents($file);
		    $first2 = substr($buffer, 0, 2);
		    $first3 = substr($buffer, 0, 3);
		    $first4 = substr($buffer, 0, 3);

		    if ($first3 == $UTF8_BOM)
		    	return 'UTF-8';
		    elseif ($first4 == $UTF32_BIG_ENDIAN_BOM)
		    	return 'UTF-32BE';
		    elseif ($first4 == $UTF32_LITTLE_ENDIAN_BOM)
		    	return 'UTF-32LE';
		    elseif ($first2 == $UTF16_BIG_ENDIAN_BOM)
		    	return 'UTF-16BE';
		    elseif ($first2 == $UTF16_LITTLE_ENDIAN_BOM)
		    	return 'UTF-16LE';

		    return 'ASCII';
		}

		//
		static function FilterChecked($values)
		{
			return array_keys(array_filter($values, function($var){ return $var == 'true'; }));
		}

		static function Links($string)
		{
			return preg_replace('/http:\/\/([a-zA-Z0-9\.\/\_\-\~\?\=\&\#]+)/', '<a href="$0" target="_blank">$0</a>', str_replace('&amp;', '&', $string));
		}

		static function URL($string)
		{
			return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($string)), '-');
		}

		static function CAPS($data)
		{
			if (!is_array($data)) {
				//
				return $data;
			}

			$result = array();
			foreach ($data as $key => $value) {
				$result[ strtoupper($key) ] = self::CAPS( $value );
			}

			return $result;
		}

	}

?>