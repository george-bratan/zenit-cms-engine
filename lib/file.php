<?php

	// File

	class File {

		//

		/**
			Transmit a file for downloading by HTTP client; If kilobytes per
			second is specified, output is throttled (bandwidth will not be
			controlled by default); Return TRUE if successful, FALSE otherwise;
			Support for partial downloads is indicated by third argument
				@param $file string
				@param $kbps integer
				@param $partial
				@public
		**/
		static function Send($file, $name = '', $kbps=0, $partial=TRUE)
		{
			//$file = self::resolve($file);

			if (!is_file($file))
			{
				Application::Error(404);
				return FALSE;
			}

			if (!$name) {
				$name = basename($file);
			}

			if (PHP_SAPI != 'cli')
			{
				header(Request::HTTP_Content.': application/octet-stream');
				header(Request::HTTP_Disposition.': filename="'.$name.'"');
				header(Request::HTTP_Partial.': '.($partial ? 'bytes' : 'none'));
				header(Request::HTTP_Length.': '.filesize($file));
				ob_end_flush();
			}

			$max = ini_get('max_execution_time');
			$ctr = 1;
			$handle = fopen($file, 'r');
			$time = microtime(TRUE);

			while (!feof($handle) && !connection_aborted())
			{
				if ($kbps > 0) {
					// Throttle bandwidth
					$ctr++;
					$elapsed = microtime(TRUE) - $time;
					if (($ctr/$kbps) > $elapsed)
						usleep(1e6*($ctr / $kbps - $elapsed));
				}

				// Send 1KiB and reset timer
				echo fread($handle, 1024);
				set_time_limit($max);
			}

			fclose($handle);
			return TRUE;
		}

		/**
	     * Upload a file in a temporary location
	     *
	     * @param array $files
	     * @return string - temporary file object, serialized
	     */
	    static function Temporary($file, $context = 'tmp') {

	        if (Request::FILES("{$file}.tmp_name") == NULL)
	        	return;

	        if (Request::FILES("{$file}.error") != 0) {
	        	throw Exception('File Upload Error: Code '.Request::FILES("{$file}.error"));
	        }

	        $uid = 0;
	        $timestamp = time();

	        $file_name = basename(Request::FILES("{$file}.name"));
	        $file_ext = substr($file_name, strrpos($file_name, '.') + 1);

	        while (file_exists(Conf::get('APP:UPLOAD') . "{$context}.{$uid}.{$timestamp}.{$file_ext}")) {
	        	$uid++;
	        }

	        $path = Conf::get('APP:UPLOAD');
	    	$parts = explode('/', $context);
	    	array_pop($parts);
	        foreach ($parts as $dir) {
	        	//
	        	$path .= $dir . '/';
	        	if (!is_dir($path)) {
	        		Util::mkdir($path);
	        	}
	        }

	        $temporary = array(
	        	'name' => $file_name,
	        	'ext'  => $file_ext,
	        	'type' => Request::FILES("{$file}.type"),
	        	'size' => Request::FILES("{$file}.size"),
	        	'server_path' => Conf::get('APP:UPLOAD'),
	        	'server_name' => "{$context}.{$uid}.{$timestamp}.{$file_ext}",
	        	'timestamp'   => $timestamp,
	        );

	        if (file_exists(Request::FILES("{$file}.tmp_name"))) {
		        if (move_uploaded_file(Request::FILES("{$file}.tmp_name"), $temporary['server_path'].$temporary['server_name']))
		        	return $temporary;
	        }

	        throw Exception('File Upload Error: Cannot Move Uploaded File');
	    }

	    /**
	     * Move a file that was uploaded in a temporary location
	     *
	     * @param mixed $temporary - a previously uploaded file
	     * @param string $server_path - destination folder
	     * @return string - permanent file object
	     */
	    static function Permanent($temporary, $context = 'file', $uid = 0) {

	    	if (empty($temporary))
	    		return;

	    	$path = Conf::get('APP:UPLOAD');
	    	$parts = explode('/', $context);
	    	array_pop($parts);
	        foreach ($parts as $dir) {
	        	//
	        	$path .= $dir . '/';
	        	if (!is_dir($path)) {
	        		Util::mkdir($path);
	        	}
	        }

	    	$permanent = array(
	    		'name' => $temporary['name'],
	        	'ext'  => $temporary['ext'],
	        	'type' => $temporary['type'],
	        	'size' => $temporary['size'],
	        	'server_path' => Conf::get('APP:UPLOAD'),
	    		'server_name' => ($context ? $context.'.' : '') . ($uid ? $uid.'.' : '') . $temporary['timestamp'].'.'.$temporary['ext'],
	    		'timestamp'   => $temporary['timestamp'],
	    	);

	    	if (file_exists($temporary['server_path'].$temporary['server_name'])) {
	    		//
		    	if (copy($temporary['server_path'].$temporary['server_name'], $permanent['server_path'].$permanent['server_name'])) {
		    		//
		    		unlink($temporary['server_path'].$temporary['server_name']);
		    		return $permanent;
		    	}
		    }

	    	throw Exception('File Upload Error: Cannot Move Temporary File');
	    }

	    /**
	     * Upload a file to a server destination
	     *
	     * @return string - permanent file object
	     */
	    static function Upload($file, $context = 'file', $uid = 0)
	    {
	    	$temporary = self::Temporary($file);

	    	$permanent = NULL;
	    	if ($temporary) {
	    		//
	    		$permanent = self::Permanent($temporary, $context, $uid);
	    	}

	    	return $permanent;
	    }

	}

?>