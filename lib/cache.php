<?php

	// Cache

	class Cache
	{

		static
			//! Level-1 cached object
			$buffer,
			//! Cache back-end
			$backend;


		/**
			Auto-detect extensions usable as cache back-ends; MemCache must be
			explicitly activated to work properly; Fall back to file system if
			none declared or detected
				@public
		**/
		static function detect()
		{
			$exts = array_intersect(array('apc', 'xcache'), array_map('strtolower', get_loaded_extensions()));

			$ref = array_merge($exts, array());
			$cache = array_shift($ref) ? : ('folder='.Conf::get('APP:CACHE'));

			Conf::set('APP:CACHE', $cache);
		}

		/**
			Initialize cache backend
				@return boolean
				@public
		**/
		static function prep()
		{
			if (!Conf::get('APP:CACHE'))
				return TRUE;

			if (preg_match('/^(apc)|(memcache)=(.+)|(xcache)|(folder)\=(.+\/)/i', Conf::get('APP:CACHE'), $match))
			{
				if (isset($match[5]) && $match[5])
				{
					if (!is_dir($match[6]))
						Util::mkdir($match[6]);

					// File system
					self::$backend = array('type' => 'folder','id' => $match[6]);
				}
				else
				{
					$ext = strtolower($match[1] ?: ($match[2] ?: $match[4]));
					if (!extension_loaded($ext))
					{
						trigger_error(sprintf(Lang::ERR_PHPExt, $ext));
						return FALSE;
					}

					if (isset($match[2]) && $match[2])
					{
						// Open persistent MemCache connection(s)
						// Multiple servers separated by semi-colon
						$pool = explode(';', $match[3]);
						$mcache = NULL;
						foreach ($pool as $server) {
							// Hostname:port
							list($host, $port) = explode(':', $server);
							if (is_null($port))
								// Use default port
								$port=11211;

							// Connect to each server
							if (is_null($mcache))
								$mcache = memcache_pconnect($host, $port);
							else
								memcache_add_server($mcache, $host, $port);
						}

						// MemCache
						self::$backend = array('type' => $ext, 'id' => $mcache);
					}
					else
						// APC and XCache
						self::$backend = array('type' => $ext);
				}

				self::$buffer = NULL;
				return TRUE;
			}

			// Unknown back-end
			trigger_error(Lang::ERR_Backend);
			return FALSE;
		}


		/**
			Store data in framework cache; Return TRUE/FALSE on success/failure
				@return boolean
				@param $name string
				@param $data mixed
				@public
		**/
		static function set($name, $data)
		{
			if (!Conf::get('APP:CACHE'))
				return TRUE;

			if (is_null(self::$backend)) {
				// Auto-detect back-end
				self::detect();
				if (!self::prep())
					return FALSE;
			}

			$key = $_SERVER['SERVER_NAME'].'.'.$name;

			// Serialize data for storage
			$time = time();

			// Add timestamp
			$val = gzdeflate(serialize(array($time, $data)));

			// Instruct back-end to store data
			switch (self::$backend['type'])
			{
				case 'apc':
					$ok = apc_store($key, $val);
					break;
				case 'memcache':
					$ok = memcache_set(self::$backend['id'], $key, $val);
					break;
				case 'xcache':
					$ok = xcache_set($key, $val);
					break;
				case 'folder':
					$ok = file_put_contents(self::$backend['id'].$key, $val, LOCK_EX);
					break;
			}

			if (is_bool($ok) && !$ok) {
				trigger_error(sprintf(Lang::ERR_Store, $name));
				return FALSE;
			}

			// Free up space for level-1 cache
			while (count(self::$buffer) &&
				strlen(serialize($data)) + strlen(serialize(array_slice(self::$buffer, 1))) > ini_get('memory_limit') - memory_get_peak_usage())
					self::$buffer = array_slice(self::$buffer, 1);

			self::$buffer[$name] = array('data' => $data, 'time' => $time);
			return TRUE;
		}

		/**
			Retrieve value from framework cache
				@return mixed
				@param $name string
				@param $quiet boolean
				@public
		**/
		static function get($name, $quiet = FALSE)
		{
			if (!Conf::get('APP:CACHE'))
				return FALSE;

			if (is_null(self::$backend)) {
				// Auto-detect back-end
				self::detect();
				if (!self::prep())
					return FALSE;
			}

			$stats = &Application::$STATS;
			if (!isset($stats['CACHE'])) {
				$stats['CACHE'] = array(
					'level-1' => array('hits' => 0, 'misses' => 0),
					'backend' => array('hits' => 0, 'misses' => 0)
				);
			}

			// Check level-1 cache first
			if (isset(self::$buffer) && isset(self::$buffer[$name])) {
				$stats['CACHE']['level-1']['hits']++;
				return self::$buffer[$name]['data'];
			}
			else
				$stats['CACHE']['level-1']['misses']++;

			$key = $_SERVER['SERVER_NAME'].'.'.$name;

			// Instruct back-end to fetch data
			switch (self::$backend['type'])
			{
				case 'apc':
					$val = apc_fetch($key);
					break;
				case 'memcache':
					$val = memcache_get(self::$backend['id'], $key);
					break;
				case 'xcache':
					$val = xcache_get($key);
					break;
				case 'folder':
					$val = is_file(self::$backend['id'].$key) ?
						file_get_contents(self::$backend['id'].$key) : FALSE;
					break;
			}

			if (is_bool($val)) {
				$stats['CACHE']['backend']['misses']++;

				// No error display if specified
				if (!$quiet)
					trigger_error(sprintf(Lang::ERR_Fetch, $name));

				self::$buffer[$name] = NULL;
				return FALSE;
			}

			// Unserialize timestamp and data
			list($time, $data) = unserialize(gzinflate($val));
			$stats['CACHE']['backend']['hits']++;

			// Free up space for level-1 cache
			while (count(self::$buffer) &&
				strlen(serialize($data)) + strlen(serialize(array_slice(self::$buffer, 1))) > ini_get('memory_limit') - memory_get_peak_usage())
					self::$buffer = array_slice(self::$buffer, 1);

			self::$buffer[$name] = array('data'=>$data, 'time'=>$time);

			return $data;
		}

		/**
			Delete variable from framework cache
				@return boolean
				@param $name string
				@public
		**/
		static function clear($name)
		{
			if (!Conf::get('APP:CACHE'))
				return TRUE;

			if (is_null(self::$backend)) {
				// Auto-detect back-end
				self::detect();
				if (!self::prep())
					return FALSE;
			}

			$key = $_SERVER['SERVER_NAME'].'.'.$name;

			// Instruct back-end to clear data
			switch (self::$backend['type']) {
				case 'apc':
					$ok =! apc_exists($key) || apc_delete($key);
					break;
				case 'memcache':
					$ok = memcache_delete(self::$backend['id'], $key);
					break;
				case 'xcache':
					$ok = !xcache_isset($key) || xcache_unset($key);
					break;
				case 'folder':
					$ok = is_file(self::$backend['id'].$key) &&
						unlink(self::$backend['id'].$key);
					break;
			}

			if (is_bool($ok) && !$ok) {
				trigger_error(sprintf(Lang::ERR_Clear, $name));
				return FALSE;
			}

			// Check level-1 cache first
			if (isset(self::$buffer) && isset(self::$buffer[$name]))
				unset(self::$buffer[$name]);

			return TRUE;
		}

		/**
			Return FALSE if specified variable is not in cache;
			otherwise, return Un*x timestamp
				@return mixed
				@param $name string
				@public
		**/
		static function cached($name)
		{
			return self::get($name, TRUE) ?
				self::$buffer[$name]['time'] : FALSE;
		}


	}

?>