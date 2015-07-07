<?php

	//

	class Request
	{
		//@{ HTTP status codes (RFC 2616)
		const
			HTTP_100 = 'Continue',
			HTTP_101 = 'Switching Protocols',
			HTTP_200 = 'OK',
			HTTP_201 = 'Created',
			HTTP_202 = 'Accepted',
			HTTP_203 = 'Non-Authorative Information',
			HTTP_204 = 'No Content',
			HTTP_205 = 'Reset Content',
			HTTP_206 = 'Partial Content',
			HTTP_300 = 'Multiple Choices',
			HTTP_301 = 'Moved Permanently',
			HTTP_302 = 'Found',
			HTTP_303 = 'See Other',
			HTTP_304 = 'Not Modified',
			HTTP_305 = 'Use Proxy',
			HTTP_306 = 'Temporary Redirect',
			HTTP_400 = 'Bad Request',
			HTTP_401 = 'Unauthorized',
			HTTP_402 = 'Payment Required',
			HTTP_403 = 'Forbidden',
			HTTP_404 = 'Not Found',
			HTTP_405 = 'Method Not Allowed',
			HTTP_406 = 'Not Acceptable',
			HTTP_407 = 'Proxy Authentication Required',
			HTTP_408 = 'Request Timeout',
			HTTP_409 = 'Conflict',
			HTTP_410 = 'Gone',
			HTTP_411 = 'Length Required',
			HTTP_412 = 'Precondition Failed',
			HTTP_413 = 'Request Entity Too Large',
			HTTP_414 = 'Request-URI Too Long',
			HTTP_415 = 'Unsupported Media Type',
			HTTP_416 = 'Requested Range Not Satisfiable',
			HTTP_417 = 'Expectation Failed',
			HTTP_500 = 'Internal Server Error',
			HTTP_501 = 'Not Implemented',
			HTTP_502 = 'Bad Gateway',
			HTTP_503 = 'Service Unavailable',
			HTTP_504 = 'Gateway Timeout',
			HTTP_505 = 'HTTP Version Not Supported';
		//@}

		//@{ HTTP headers (RFC 2616)
		const
			HTTP_AcceptEnc 		= 'Accept-Encoding',
			HTTP_Agent 			= 'User-Agent',
			HTTP_Cache 			= 'Cache-Control',
			HTTP_Connect 		= 'Connection',
			HTTP_Content 		= 'Content-Type',
			HTTP_Disposition 	= 'Content-Disposition',
			HTTP_Encoding 		= 'Content-Encoding',
			HTTP_Expires 		= 'Expires',
			HTTP_Host 			= 'Host',
			HTTP_IfMod 			= 'If-Modified-Since',
			HTTP_Keep 			= 'Keep-Alive',
			HTTP_LastMod 		= 'Last-Modified',
			HTTP_Length 		= 'Content-Length',
			HTTP_Location 		= 'Location',
			HTTP_Partial 		= 'Accept-Ranges',
			HTTP_Powered 		= 'X-Powered-By',
			HTTP_Pragma 		= 'Pragma',
			HTTP_Referer 		= 'Referer',
			HTTP_Transfer 		= 'Content-Transfer-Encoding',
			HTTP_WebAuth 		= 'WWW-Authenticate';
		//@}

		// URI is relative from WWW:ROOT, complete with query-string
		// URL is full path from host, no query-string
		static
			$URL = '', $URI = '', $METHOD = '', $HOST = '';

		static
			$Response = '';

		static
			$Params = Array();

		/**
			Retrieve HTTP headers
				@return array
				@public
		**/
		static function Headers()
		{
			if (PHP_SAPI != 'cli') {
				// Apache server
				if (function_exists('getallheaders'))
					return getallheaders();

				// Workaround
				$headers = array();
				foreach ($_SERVER as $key => $val)
				{
					if (substr($key, 0, 5) == 'HTTP_') {

						// Translate:  HTTP_CONTENT_TYPE  =>  Content-Type
						$header = preg_replace_callback('/\w+\b/',
							function($word) {
								return ucfirst(strtolower($word[0]));
							},
							strtr(substr($key,5),'_','-')
						);
						$headers[ $header ] = $val;
					}
				}

				return $headers;
			}

			return array();
		}

		/**
			Send HTTP header with expiration date (seconds from current time)
				@param $secs integer
				@public
		**/
		static function Expire($secs = 0)
		{
			if (PHP_SAPI != 'cli' && !headers_sent())
			{
				$time = time();
				$headers = self::Headers();

				if (isset($headers[self::HTTP_IfMod]) && strtotime($headers[self::HTTP_IfMod])+$secs > $time)
				{
					self::Status(304);
					die();
				}

				header(self::HTTP_Powered.': '.Lang::TEXT_AppName);

				if ($secs) {
					header_remove(self::HTTP_Pragma);
					header(self::HTTP_Expires.': '.gmdate('r', $time+$secs));
					header(self::HTTP_Cache.': max-age='.$secs);
					header(self::HTTP_LastMod.': '.gmdate('r'));
				}
				else {
					header(self::HTTP_Pragma.': no-cache');
					header(self::HTTP_Cache.': no-cache, must-revalidate');
				}
			}
		}

		/**
			Send HTTP status header; Return text equivalent of status code
				@return mixed
				@param $code int
				@public
		**/
		static function Status($code)
		{
			if (!defined('self::HTTP_'.$code)) {
				// Invalid status code
				trigger_error(sprintf(Lang::ERR_HTTPCode, $code));
				return FALSE;
			}

			// Get description
			$response = constant('self::HTTP_'.$code);

			// Send raw HTTP header
			if (PHP_SAPI != 'cli' && !headers_sent()) {
				header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$response);
			}

			return $response;
		}

		/**
			Shortcut methods
		**/
		/*
		static function __callStatic($func, array $args)
		{
		    if (isset(self::$Params[$func]))
		    {
		    	$param = $args[0];

		    	if (isset(self::$Params[$func][$param]))
		    		return self::$Params[$func][$param];

		    	return NULL;
		    }

			trigger_error(sprintf(Lang::ERR_Method, get_called_class().'::'.
				$func.'('.Util::csv($args).')'));
		}
		*/

		/**
			Shortcut methods
		**/
		static function __callStatic($func, array $args)
		{
		    if (isset(self::$Params[$func]))
		    {
		    	if (!isset($args[0])) {
		    		return self::$Params[$func];
		    	}

		    	$key = $args[0];

		    	$var = &self::$Params[$func];

				$parts = explode('.', $key);

				foreach ($parts as $part) {
					//
					if (isset($var[ $part ]))
						$var = &$var[ $part ];
					else
						return NULL;
				}

				return $var;
		    }

			trigger_error(sprintf(Lang::ERR_Method, get_called_class().'::'.
				$func.'('.Util::csv($args).')'));
		}

		static function Redirect( $url )
		{
			header('Location: '.$url);
			die();
		}

	}

?>