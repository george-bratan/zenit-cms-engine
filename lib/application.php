<?php

	// Application

	class Application
	{
		static $ERROR = FALSE;
		static $LOADED = Array();
		static $STATS = Array();

		static function Start()
		{
			// Handle all exceptions/non-fatal errors
			error_reporting(E_ALL|E_STRICT);
			error_reporting(E_ERROR);
			ini_set('display_errors', 0);

			// Get PHP settings
			$ini = ini_get_all(NULL, FALSE);

			// Intercept errors and send output to browser
			set_error_handler(
				function($errno, $errstr) {
					if (error_reporting()) {
						// Error suppression (@) is not enabled
						$self = __CLASS__;
						$self::Error(500, $errstr);
					}
				}
			);

			// Do the same for PHP exceptions
			set_exception_handler(
				function($ex) {
					if (!count($trace = $ex->getTrace()))
					{
						// Translate exception trace
						list($trace) = debug_backtrace();
						$arg = $trace['args'][0];
						$trace = array(
							array(
								'file' => $arg->getFile(),
								'line' => $arg->getLine(),
								'function' => '{main}',
								'args' => array()
							)
						);
					}
					$self = __CLASS__;
					$self::Error($ex->getCode(), $ex->getMessage(), $trace);
					// PHP aborts at this point
				}
			);

			// Apache mod_rewrite enabled?
			if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) {
				trigger_error(Lang::ERR_Apache);
				return;
			}

			// Fix Apache's VirtualDocumentRoot limitation
			$_SERVER['DOCUMENT_ROOT'] = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);

			// Adjust HTTP request time precision
			$_SERVER['REQUEST_TIME'] = microtime(TRUE);

			// Hydrate framework variables
			//$root = Util::fixslashes(realpath('.')).'/';
			Conf::Defaults();

			// Create convenience containers for PHP globals
			foreach (Conf::get('PHP:GLOBALS') as $var) {
				// Sync framework and PHP globals
				Request::$Params[$var] = &$GLOBALS['_'.$var];

				if (!empty($ini['magic_quotes_gpc']) && preg_match('/^[GPCR]/', $var)) {

					// Corrective action on PHP magic quotes
					array_walk_recursive(Request::$Params[$var],
						function(&$val) {
							$val = stripslashes($val);
						}
					);
				}
			}

			if (PHP_SAPI == 'cli')
			{
				// Command line: Parse GET variables in URL, if any
				if (isset($_SERVER['argc']) && $_SERVER['argc'] < 2)
					array_push($_SERVER['argv'], '/');

				preg_match_all('/[\?&]([^=]+)=([^&$]*)/', $_SERVER['argv'][1], $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$_REQUEST[ $match[1] ] = $match[2];
					$_GET[ $match[1] ] = $match[2];
				}

				// Detect host name from environment
				$_SERVER['SERVER_NAME'] = gethostname();

				// Convert URI to human-readable string
				self::EmulateRequest('GET '.$_SERVER['argv'][1]);
			}

			// Initialize autoload stack and shutdown sequence
			spl_autoload_register(__CLASS__.'::Autoload');
			register_shutdown_function(__CLASS__.'::Stop');

			if (Conf::get('HANDLER:ONLOAD'))
			{
				self::call(Conf::get('HANDLER:ONLOAD'));
			}
		}


		/**
			Process routes based on incoming URI
				@public
		**/
		static function Run() {

			self::$STATS['TIME']['start'] = microtime(TRUE);

			if ( Security::CheckSpam() )
			{
				if ( Conf::get('SECURITY:SPAM:ROUTE') )
					// Spammer detected; Send to blackhole
					Application::Reroute( Conf::get('SECURITY:SPAM:ROUTE') );
				else
					// HTTP 404 message
					Application::Error(404);
			}


			// Process routes
			if (!count(Router::$Routes)) {
				trigger_error(Lang::ERR_NoRoutes);
				return;
			}

			// URI is relative from WWW:ROOT, complete with query-string
			Request::$URI = preg_replace('/^'.preg_quote(Conf::get('WWW:ROOT'),'/').'\b(.+)/', '\1',
								rawurldecode($_SERVER['REQUEST_URI']));
			Request::$METHOD = $_SERVER['REQUEST_METHOD'];
			Request::$HOST = $_SERVER['SERVER_NAME'];

			// X - Detailed routes get matched first
			// issue on urls with @params, if sorted these get pushed up even though they're actually smaller
			// we'll leave the order given by the user routes
			//krsort(Router::$Routes);

			// eventually, we'll have to create a routine for route identification first, with priority on length of route.

			$found = FALSE;

			// Save the current time
			$time = time();
			foreach (Router::$Routes as $uri => $methods)
			{
				// WHAT THE F IS THIS ?
				$pattern = preg_replace(
							'/(?:{{)?@(\w+\b)(?:}})?/i',
							// Valid URL characters (RFC 1738)
							'(?P<\1>[\w\-\.!~\*\'"(),\s]+)',
							// Replace Wildcard character in URI
							//str_replace('\*', '(.*)', preg_quote($uri,'/'))
							str_replace(array('\*', '\(', '\)', '\|'), array('(.*)', '(', ')', '|'), preg_quote($uri, '/'))
				);

				// check if current uri matches our request, and get params
				if (!preg_match('/^'.$pattern.'\/?(?:\?.*)?$/i', Request::$URI, $params))
					continue;

				// URL is full path from host, no query-string, no url @param
				Request::$URL = Conf::get('WWW:ROOT').'/'.trim(preg_replace('/(?:{{)?@(\w+\b)(?:}})?/i', '', $uri), '/');

				$found = TRUE;

				// Inspect each defined method for this route
				foreach ($methods as $method => $proc) {

					// check if current method matches
					if (!preg_match('/'.$method.'/', $_SERVER['REQUEST_METHOD']))
						continue;

					// explode handler
					list($funcs, $ttl, $throttle, $hotlink) = $proc;

					if (!$hotlink && Security::CheckHotlink() ) {
						// Hot link detected; Redirect page
						if ( Conf::get('SECURITY:HOTLINK:ROUTE') )
							// Spammer detected; Send to blackhole
							Application::Reroute( Conf::get('SECURITY:HOTLINK:ROUTE') );
						else
							// HTTP 404 message
							Application::Error(404);
					}

					// Save named uri captures
					foreach ($params as $key => $param) {
						// Remove non-zero indexed elements
						if (is_numeric($key) && $key)
							unset($params[$key]);
					}
					Request::$Params['URL'] = $params;

					// Default: Do not cache
					Request::Expire(0);

					if ($_SERVER['REQUEST_METHOD'] == 'GET' && $ttl)
					{
						$_SERVER['REQUEST_TTL'] = $ttl;

						// Get HTTP request headers
						$headers = Request::Headers();

						// Content divider
						$div = chr(0);

						// Get hash code for this Web page
						$hash = 'url.'.Util::hash(
							$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI']
						);

						$cached = Cache::cached($hash);
						$uri = '/^'.Request::HTTP_Content.':.+/';
						$time = time();

						if ($cached && $time - $cached < $ttl)
						{
							if (!isset($headers[Request::HTTP_IfMod]) || $cached > strtotime($headers[self::HTTP_IfMod]))
							{
								// Activate cache timer
								Request::Expire($cached + $ttl - $time);

								// Retrieve from cache
								$buffer = Cache::get($hash);
								$type = strstr($buffer, $div, TRUE);
								if (PHP_SAPI != 'cli' && !headers_sent() && preg_match($uri, $type, $match))
									// Cached MIME type
									header($match[0]);

								// Save response
								Request::$Response = substr(strstr($buffer, $div), 1);
							}
							else {
								// Client-side cache is still fresh
								Request::Status(304);
								die();
							}
						}
						else {
							// Activate cache timer
							Request::Expire($ttl);

							$type = '';
							foreach (headers_list() as $header) {
								if (preg_match($uri, $header)) {
									// Add Content-Type header to buffer
									$type = $header;
									break;
								}
							}

							// Cache this page
							ob_start();
							self::call($funcs, TRUE);
							Request::$Response = ob_get_clean();

							if (!self::$ERROR && Request::$Response) {
								// Compress and save to cache
								Cache::set($hash, $type . $div . Request::$Response);
							}
						}
					}
					else {
						// Capture output
						ob_start();
						if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
							// Associate PUT with file handle of stdin stream
							//self::$vars['PUT'] = fopen('php://input', 'rb');
							self::call($funcs, TRUE);
							//fclose(self::$vars['PUT']);
						}
						else {
							self::call($funcs,TRUE);
						}

						Request::$Response = ob_get_clean();
					}

					$elapsed = time() - $time;
					$throttle = $throttle ? : Conf::get('APP:THROTTLE');
					if ($throttle/1e3 > $elapsed) {
						// Delay output
						usleep(1e6 * ($throttle / 1e3-$elapsed));
					}

					if (Request::$Response && !Conf::get('APP:QUIET')) {
						// Display response
						echo Request::$Response;
					}

					// Hail the conquering hero
					return;
				}
			}

			// No such Web page
			Application::Error(404);
		}


		/**
			Display default error page; Use custom page if found
				@param $code integer
				@param $str string
				@param $trace array
				@public
		**/
		static function Error($code, $str = '', array $trace = NULL, $quiet = FALSE)
		{
			$prior = self::$ERROR;
			$out = '';

			if ($code == 404)
			{
				// No stack trace needed
				$str = sprintf(Lang::ERR_NotFound, $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
			}
			elseif ($code == 403)
			{
				// No stack trace needed
				$str = sprintf(Lang::ERR_AccDenied, $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
			}
			else
			{
				// Generate internal server error if code is zero
				if (!$code)
					$code = 500;

				if (is_null($trace))
					$trace = debug_backtrace();

				$class = NULL;
				$line = 0;
				if (is_array($trace))
				{
					$plugins = is_array($plugins = glob(Conf::get('APP:LIBRARY').'*')) ?
						array_map('Util::fixslashes', $plugins) : array();

					// Stringify the stack trace
					ob_start();
					foreach ($trace as $nexus)
					{
						// Remove stack trace noise
						if (Conf::get('DEBUG:LEVEL') < 3 && (
									!isset($nexus['file']) ||
									Conf::get('DEBUG:LEVEL') < 2 &&
										(strrchr(basename($nexus['file']), '.') == '.tmp' || in_array(Util::fixslashes($nexus['file']), $plugins)) ||
									isset($nexus['function']) &&
										preg_match('/^(call_user_func(?:_array)?|trigger_error|{.+}|'.__FUNCTION__.'|__)/', $nexus['function'])
								)
							)
							continue;

						print '#'.$line.' ';

						if (isset($nexus['line'])) {
							print urldecode(Util::fixslashes($nexus['file'])).':'.$nexus['line'].' ';
						}

						if (isset($nexus['function'])) {

							if (isset($nexus['class'])) {
								print $nexus['class'].$nexus['type'];
							}

							print $nexus['function'];

							if (!preg_match('/{{.+}}/', $nexus['function']) && isset($nexus['args'])) {
								print '('.Util::csv($nexus['args']).')';
							}

						}

						print "\n";

						$line++;
					}

					$out = ob_get_clean();
				}
			}

			if (PHP_SAPI != 'cli' && !headers_sent())
				// Remove all pending headers
				header_remove();

			// Save error details
			self::$ERROR = array(
				'code' => $code,
				'title' => Request::Status($code),
				'text' => preg_replace('/\v/', '', $str),
				'trace' => Conf::get('DEBUG:LEVEL') ? $out : ''
			);

			$error = &self::$ERROR;
			if (Conf::get('DEBUG:LEVEL') < 2 && Conf::get('APP:QUIET'))
				return;

			// Write to server's error log (with complete stack trace)
			error_log($error['text']);
			foreach (explode("\n", $out) as $str) {
				if ($str)
					error_log($str);
			}

			if ($prior || Conf::get('APP:QUIET') || $quiet)
				return;

			foreach (array('title','text','trace') as $sub) {
				// Convert to HTML entities for safety
				$error[$sub] = Util::htmlencode(rawurldecode($error[$sub]));
			}

			$error['trace'] = nl2br($error['trace']);

			$func = Conf::get('HANDLER:ERROR');


			if ($func) {
				self::call($func);
			}
			else {
				print
					'<html>'.
						'<head>'.
							'<title>'.$error['code'].' '.$error['title'].'</title>'.
						'</head>'.
						'<body>'.
							'<h1>'.$error['title'].'</h1>'.
							'<p><i>'.$error['text'].'</i></p>'.
							'<p>'.$error['trace'].'</p>'.
						'</body>'.
					'</html>';
			}
		}


		/**
			Execute shutdown function
				@public
		**/
		static function Stop() {
			$error = error_get_last();
			if ($error && !Conf::get('APP:QUIET') && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR)))
				// Intercept fatal error
				self::Error(500, $error['message'], array($error));

			if (Conf::get('HANDLER:UNLOAD'))
			{
				ob_end_flush();

				if (PHP_SAPI != 'cli')
					header(self::HTTP_Connect.': close');

				self::call(Conf::get('HANDLER:UNLOAD'));
			}
		}


		/**
			Reroute to specified URI
				@param $uri string
				@public
		**/
		static function Reroute($uri)
		{
			//$uri = self::resolve($uri);

			if (PHP_SAPI != 'cli' && !headers_sent())
			{
				if (session_id())
					session_commit();

				// HTTP redirect
				Request::status($_SERVER['REQUEST_METHOD'] == 'GET' ? 301 : 303);

				header(Request::HTTP_Location.': '.(preg_match('/^https?:\/\//',$uri) ? $uri : (Conf::get('WWW:ROOT').$uri)));
				die();
			}

			self::EmulateRequest(Request::$METHOD . ' '.$uri);
			self::Run();
		}


		/**
			Call route handler
				@return mixed
				@param $funcs string
				@param $listen boolean
				@public
		**/
		static function Call($funcs, $listen = FALSE)
		{
			$classes = array();
			$funcs = is_string($funcs) ? Util::split($funcs) : array($funcs);

			foreach ($funcs as $func)
			{
				if (is_string($func))
				{
					//$func = self::resolve($func);

					if (preg_match('/(.+)\s*(->|::)\s*(.+)/s', $func, $match))
					{
						if (!class_exists($match[1]) || !method_exists($match[1], $match[3])) {
							trigger_error(sprintf(Lang::ERR_Callback,$func));
							return FALSE;
						}
						$func = array($match[2] == '->' ? new $match[1] : $match[1], $match[3]);
					}
					elseif (!function_exists($func) && !($func instanceof Closure))
					{
						if (preg_match('/\.php$/i', $func)) {
							foreach (Util::split(Conf::get('APP:IMPORTS')) as $path) {
								if (is_file($file = $path.$func)) {
									$sandbox = new Sandbox;
									return $sandbox->run($file);
								}
							}

							trigger_error(sprintf(Lang::ERR_Import, $func));
						}
						else
							trigger_error(sprintf(Lang::ERR_Callback, $func));

						return FALSE;
					}
				}

				if (is_array($func) && count($func) > 2) {
					$params = array_slice($func, 2);
					$func = array_slice($func, 0, 2);
				}

				if (!is_callable($func)) {
					trigger_error(sprintf(Lang::ERR_Callback,
						is_array($func) && count($func) > 1 ?
							(get_class($func[0]).(is_object($func[0]) ? '->' : '::').$func[1]) : $func));

					return FALSE;
				}

				$oop = is_array($func) && (is_object($func[0]) || is_string($func[0]));

				if ($listen && $oop && method_exists($func[0], $before='onBeforeRoute') && !in_array($func[0], $classes))
				{
					// Execute beforeRoute() once per class
					if (call_user_func(array($func[0] , $before)) === FALSE)
						return FALSE;

					$classes[] = is_object($func[0]) ? get_class($func[0]) : $func[0];
				}

				$out = isset($params) ? call_user_func_array($func, $params) : call_user_func($func);

				if ($listen && $oop && method_exists($func[0], $after='onAfterRoute') &&  !in_array($func[0], $classes))
				{
					// Execute afterRoute() once per class
					call_user_func(array($func[0], $after));

					$classes[] = is_object($func[0]) ? get_class($func[0]) : $func[0];
				}
			}

			return $out;
		}


		/**
			Mock environment for command-line use and/or unit testing
				@param $pattern string
				@param $params array
				@public
		**/
		static function EmulateRequest($pattern, array $params=NULL) {
			// Override PHP globals
			list($method, $uri) = preg_split('/\s+/', $pattern, 2, PREG_SPLIT_NO_EMPTY);

			$query = explode('&', parse_url($uri, PHP_URL_QUERY));

			foreach ($query as $pair) {
				if (strpos($pair, '=')) {
					list($var, $val) = explode('=', $pair);
					Request::$Params[$method][$var] = $val;
					Request::$Params['REQUEST'][$var] = $val;
				}
			}

			if (is_array($params)) {
				foreach ($params as $var=>$val) {
					Request::$Params[$method][$var] = $val;
					Request::$Params['REQUEST'][$var] = $val;
				}
			}

			$_SERVER['REQUEST_METHOD'] = $method;
			$_SERVER['REQUEST_URI'] = Conf::get('WWW:ROOT').$uri;
		}


		/**
			Intercept instantiation of objects in undefined classes
				@param $class string
				@public
		**/
		static function Autoload($class)
		{
			$list = array_map('Util::fixslashes', get_included_files());

			// Support both namespace mapping styles: NS_class and NS/class
			// Also transform camelCase to camel.case
			foreach (array(str_replace('\\', '_', $class), $class, Util::cameltofile($class)) as $style)
			{
				// Prioritize plugins
				foreach (Util::split(Conf::get('APP:AUTOLOAD')) as $auto)
				{
					$path = Util::fixslashes(realpath($auto));

					if (!$path)
						continue;

					$file = Util::fixslashes($style).'.php';

					if (is_int(strpos($file, '/')))
					{
						$ok = FALSE;

						// Case-insensitive check for folders
						foreach (explode('/', Util::fixslashes(dirname($file))) as $dir)
						{
							foreach (glob($path.'/*') as $found)
							{
								$found = Util::fixslashes($found);

								if (strtolower($path.'/'.$dir) == strtolower($found))
								{
									$path = $found;
									$ok = TRUE;
								}
							}
						}

						if (!$ok)
							continue;

						$file = basename($file);
					}

					$glob = glob($path.'/*.php', GLOB_NOSORT);

					if ($glob)
					{
						$glob = array_map('Util::fixslashes', $glob);

						// Case-insensitive check for file presence
						$fkey = array_search(strtolower($path.'/'.$file), array_map('strtolower', $glob));

						if (is_int($fkey) && !in_array($glob[$fkey], $list))
						{
							$sandbox = new Sandbox;
							$sandbox->run($glob[$fkey]);

							// Verify that the class was loaded
							if (class_exists($class, FALSE)) {
								// Run onLoad event handler if defined
								self::onLoadClass($class);
								return;
							}
						}
					}
				}
			}

			if (count(spl_autoload_functions()) == 1) {
				// No other registered autoload functions exist
				trigger_error(sprintf(Lang::ERR_Class, $class));
			}
		}


		/**
			onLoad event handler (static class initializer)
				@public
		**/
		static function onLoadClass($class)
		{
			$loaded = &self::$LOADED;
			$lower = strtolower($class);

			if (!isset($loaded[$lower]))
			{
				$loaded[$lower] = array_map('strtolower', get_class_methods($class));

				if (in_array('onload', $loaded[$lower]))
				{
					// Execute onload method
					$method = new ReflectionMethod($class, 'onLoad');

					if ($method->isStatic())
						call_user_func(array($class,'onLoad'));
					else
						trigger_error(sprintf(self::ERR_Static, $class.'::onload'));
				}
			}
		}

		/**
			Load all available autoload classes
				@param $callback func
				@public
		**/
		static function LoadAll($callback, $dir = NULL)
		{
			if (!$dir) {
				$dir = Conf::get('APP:AUTOLOAD');
			}

			foreach (Util::split($dir) as $auto)
			{
				$path = Util::fixslashes(realpath($auto));

				if (!$path)
					continue;

				foreach (glob($path.'/*') as $found)
				{
					$found = Util::fixslashes($found);

					if (is_dir($found)) {
						//
						//self::LoadAll($callback, $found);
						continue;
					}

					$included = array_map('Util::fixslashes', get_included_files());

					if (!in_array($found, $included)) {
						//
						$sandbox = new Sandbox;
						$sandbox->run($found);
					}

					$class = basename($found, '.php');
					$class = Util::filetocamel($class, TRUE);

					// Verify that the class was loaded
					if (class_exists($class, FALSE)) {

						// call $callback with new class loaded
						call_user_func($callback, $class);
					}
				}
			}
		}

		/**
			Return runtime performance analytics
				@return array
				@public
		**/
		static function Profile()
		{
			$stats = &self::$STATS;

			// Compute elapsed time
			$stats['TIME']['elapsed'] = microtime(TRUE) - $stats['TIME']['start'];

			// Compute memory consumption
			$stats['MEMORY']['current'] = memory_get_usage();
			$stats['MEMORY']['peak'] = memory_get_peak_usage();

			return $stats;
		}

	}


	// Sandbox

	class Sandbox
	{

		/*
			Run PHP code in sandbox
				@param $file string
				@public
		*/
		function run($file, $vars = array())
		{
			// bring $vars keys into local context
			foreach ($vars as $key => $value)
				$$key = $value;

			return require $file;
		}

	}

?>