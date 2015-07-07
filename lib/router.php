<?php

	// Router

	class Router {

		static
			$Routes = Array();

		static
			$Sitemap = Array();

		/**
			Assign handler to route pattern
				@param $pattern string
				@param $funcs mixed
				@param $ttl integer
				@param $throttle int
				@param $hotlink boolean
				@public
		**/
		static function Route($pattern, $funcs, $ttl = 0, $throttle = 0, $hotlink = TRUE)
		{
			list($methods, $uri) = preg_split('/\s+/', $pattern, 2, PREG_SPLIT_NO_EMPTY);

			foreach (Util::split($methods) as $method) {
				// Use pattern and HTTP methods as route indexes
				self::$Routes[ $uri ][ strtoupper($method) ] =
					// Save handler, cache timeout and hotlink permission
					array($funcs, $ttl, $hotlink, $throttle);
			}
		}

		/**
			Provide REST interface by mapping URL to object/class
				@param $url string
				@param $obj mixed
				@param $ttl integer
				@param $throttle int
				@param $hotlink boolean
				@public
		**/
		static function Map($url, $obj, $ttl = 0, $throttle = 0, $hotlink = TRUE)
		{
			foreach (Conf::get('HTTP:METHODS') as $method)
			{
				// method_exists() tries to load the class into context to see if there is a method
				// and renders the autoload feature useless.
				// best we add all methods by default and wait for an error

				//if (method_exists($obj, $method))
				//{
					self::Route($method.' '.$url, array($obj, $method), $ttl, $throttle, $hotlink);
				//}
			}
		}

		/**
			Provide REST interface by mapping URL to Tree like structures of objects
				@param $url string
				@param $sitemap mixed
				@param $ttl integer
				@param $throttle int
				@param $hotlink boolean
				@public
		**/
		static function Sitemap($url, $sitemap, $ttl = 0, $throttle = 0, $hotlink = TRUE)
		{
			foreach ($sitemap as $_url => $obj) {

				if (is_array($obj)) {
					self::Sitemap($url.$_url, $obj, $ttl, $throttle, $hotlink);
				}
				else {
					self::Map($url.$_url, $obj, $ttl, $throttle, $hotlink);
				}

			}

			self::$Sitemap[ $url ] = $sitemap;
		}

	}

?>