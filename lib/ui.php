<?php

	// UI

	class UI extends Object
	{
		protected static
			$VARS = Array();

		/**
			Render and serve template
				@return string
				@param $file string
				@param $mime string
				@public
		**/
		static function Serve($file, $mime='text/html')
		{
			$out = self::Render($file);

			if (PHP_SAPI != 'cli') {
				// Send HTTP header with appropriate character set
				header(Request::HTTP_Content.': '.$mime.'; '.'charset='.Conf::get('HTTP:ENCODING'));
			}

			print $out;
		}

		/**
			Render user interface
				@return string
				@param $file string
				@public
		**/
		static function Render($file, $vars = null)
		{
			//$file = self::resolve($file);

			foreach (Util::split(Conf::get('APP:UI')) as $gui) {
				if (is_file($view = Util::fixslashes($gui.$file))) {

					$file = self::Compile($view);

					if (!self::exists('CONF')) {
						self::set('CONF', Conf::Export());
					}

					ob_start();

					// Render
					$sandbox = new Sandbox();
					$sandbox->run($file, $vars ? $vars : self::export());

					return ob_get_clean();
				}
				//else
				//	print $view;

			}

			trigger_error(sprintf(Lang::ERR_Render, $file));
		}

		/**
			Render user provided html
				@return string
				@param $html string
				@param $data array
				@public
		**/
		static function RenderHtml($html, $data)
		{
			//$data = Util::CAPS($data);

			$hash = 'tmp.'.Util::hash($html . print_r($data, true));
			$file = Conf::get('APP:TMP').$_SERVER['SERVER_NAME'].'.tmp.'.$hash;

			file_put_contents($file, $html, LOCK_EX);

			$file = self::Compile($file);

			ob_start();

			// Render
			$sandbox = new Sandbox();
			$sandbox->run($file, $data);

			return ob_get_clean();
		}


		/**
			Compile file contents
				@return mixed
				@param $file string
				@public
		**/
		static function Compile($file)
		{
			//$file = F3::resolve($file);

			$hash = 'tpl.'.Util::hash($file);

			// make sure the cache is fresh
			$cached = Cache::cached($hash);
			if ($cached && filemtime($file) < $cached) {
				// Retrieve PHP-compiled template from cache
				$text = Cache::get($hash);
			}
			else {
				// Parse raw template
				$text = self::Parse(file_get_contents($file));

				// Save PHP-compiled template to cache
				Cache::set($hash, $text);
			}

			// return file to be ran in sandbox
			if (ini_get('allow_url_fopen') && ini_get('allow_url_include'))
			{
				// Stream wrap
				$tmp = 'data:text/plain,'.urlencode($text);
			}
			else
			{
				// make sure the tmp file is updated
				$cached = Cache::cached($hash);

				if (!is_dir(Conf::get('APP:TMP'))) {
					Util::mkdir(Conf::get('APP:TMP'));
				}

				$tmp = Conf::get('APP:TMP').$_SERVER['SERVER_NAME'].'.tpl.'.Util::hash($file);
				if (is_file($tmp)) {
					if ($cached && $cached < filemtime($tmp)) {
						return $tmp;
					}
				}

				// Temporary file is outdated from the cache
				// Create semaphore
				$sem = 'sem.'.Util::hash($file);

				while ($cached = Cache::cached($sem)) {
					// Locked by another process
					usleep(mt_rand(0, 1000));
				}

				Cache::set($sem, TRUE);
				file_put_contents($tmp, $text, LOCK_EX);

				// Remove semaphore
				Cache::clear($sem);
			}

			return $tmp;
		}


		/**
			Compile template chunk
				@return mixed
				@param $file string
				@public
		**/
		static function Parse($chunk)
		{
			// only 1 level of includes is possible
			$chunk = preg_replace_callback('/\{(\$|include )(.*)\}/iU',
				"self::_replace", $chunk);

			$chunk = preg_replace_callback('/\{(\$|print |php |if |for |foreach |elseif |else|\/)(.*)\}/iU',
				"self::_replace", $chunk);

			/*
			// replace &gt with ">"	and &let; with "<"
			$chunk = preg_replace_callback('/\{(.*)(&lt;|&gt;)(.*)\}/iU',
					function($tag) {
						if ($matches[2] == '&lt;')
							return '<';
						if ($matches[2] == '&gt;')
							return '>';
					},
					$chunk);
            */

			// replace short tags
			$chunk = preg_replace_callback(
					'/<\?(?:\s|\s*(=))(.+?)\?>/s',
					function($tag) {
						return '<?php '.($tag[1] ? 'print ' : '').trim($tag[2]).' ?>';
					},
					$chunk);

			return $chunk;
		}


		/**
			Utility preg_replace parse function
				@return string
				@param $matches array
				@public
		**/
		static function _replace($matches)
		{
			//Debug::Log("Render: ".print_r($matches, true));

			switch ($matches[1])
			{
				// {include 'something'}  =>  < ?php include 'something'; ? >
				case 'include ':
					$result = file_get_contents(Conf::get('APP:UI').trim($matches[2], "(')"));

					// {include} directive only needs to return the content, no parsing
					return $result;
				break;

				// {$var}  =>  < ?php if (isset($var)) print $var; ? >
				case '$':
					//$result = '<'.'?php if (isset($'.$matches[2].')) print $'.$matches[2].' ?'.'>';
					$result = '<'.'?php print $'.$matches[2].' ?'.'>';
				break;

				// {print expr}  =>  < ?php print expr; ? >
				case 'print ':
					$result = '<'.'?php print '.$matches[2].' ?'.'>';
				break;

				// {php something()}  =>  < ?php something(); ? >
				case 'php ':
					$result = '<'.'?php '.$matches[2].' ?'.'>';
				break;

				// {if ($something)}  =>  < ?php if ($something) { ? >
				case 'if ':
					$result = '<'.'?php '.$matches[1].'('.$matches[2].') { ?'.'>';
				break;

				// {for ($something)}  =>  < ?php for ($something) { ? >
				case 'for ':
				case 'foreach ':
					$result = '<'.'?php '.$matches[1].'('.trim($matches[2], '()').') { ?'.'>';
				break;

				case 'elseif ':
					$result = '<'.'?php } '.$matches[1].'('.$matches[2].') { ?'.'>';
				break;

				// {else}  =>  < ?php } else {? >
				case 'else':
					$result = '<'.'?php } else { ?'.'>';
				break;

				// {/if}, {/foreach}  =>  < ?php } ? >
				case '/':
					$result = '<'.'?php } ?'.'>';
				break;
			}

			if ($result)
			{
				$i = 0;
				$pieces = explode("'", $result);
				foreach ($pieces as $key => $piece)
				{
					$i++;

					if ($i % 2) {
						// replace $XXX.YYY into $XXX['YYY']
						$pieces[ $key ] = preg_replace('/\.([A-Z0-9\_]+)/i', '[\'${1}\']', $piece);
					}
				}
				$result = implode("'", $pieces);

				return $result;
			}

			return $matches[0];
		}


		// **************************************************
		// OUTDATED
		//

		/**
			Grab file contents
				@return mixed
				@param $file string
				@public
		**/
		static function grab($file)
		{
			//$file = F3::resolve($file);

			ob_start();
			if (!ini_get('short_open_tag'))
			{
				$orig = file_get_contents($file);
				$text = preg_replace_callback(
					'/<\?(?:\s|\s*(=))(.+?)\?>/s',
					function($tag) {
						return '<?php '.($tag[1] ? 'print ' : '').trim($tag[2]).' ?>';
					},
					$orig
				);

				if (ini_get('allow_url_fopen') && ini_get('allow_url_include'))
				{
					// Stream wrap
					$file = 'data:text/plain,'.urlencode($text);
				}
				elseif ($text != $orig)
				{
					// Save re-tagged file in temporary folder
					//if (!is_dir($ref = F3::ref('TEMP')))
					$ref = Conf::get('APP:TMP');
					if (!is_dir($ref)) {
						Util::mkdir($ref);
					}

					$temp = $ref.$_SERVER['SERVER_NAME'].'.tpl.'.Util::hash($file);

					if (!is_file($temp))
					{
						// Create semaphore
						$hash = 'sem.'.Util::hash($file);

						$cached = Cache::cached($hash);
						while ($cached) {
							// Locked by another process
							usleep(mt_rand(0, 1000));
						}

						Cache::set($hash, TRUE);
						file_put_contents($temp, $text, LOCK_EX);

						// Remove semaphore
						Cache::clear($hash);
					}

					$file = $temp;
				}
			}

			// Render
			$sandbox = new Sandbox;
			$sandbox->file($file);

			return ob_get_clean();
		}
	}

?>