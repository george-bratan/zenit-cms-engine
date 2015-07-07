<?php

	// Network

	class Network {


		/**
			Return TRUE if IP address is local or within a private IPv4 range
				@return boolean
				@param $addr string
				@public
		**/
		static function PrivateIP($addr)
		{
			return preg_match('/^127\.0\.0\.\d{1,3}$/', $addr) ||
				!filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE);
		}

		/**
			Sniff headers for real IP address
				@return string
				@public
		**/
		static function RealIP()
		{
			if (isset($_SERVER['HTTP_CLIENT_IP']))
				// Behind proxy
				return $_SERVER['HTTP_CLIENT_IP'];

			elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				// Use first IP address in list
				list($ip) = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				return $ip;
			}

			return $_SERVER['REMOTE_ADDR'];
		}
	}

?>