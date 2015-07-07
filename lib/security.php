<?php

	//

	class Security
	{
		//

		static function CheckSpam()
		{
			if (!Conf::get('SECURITY:SPAM:CHECK')) {
				return FALSE;
			}

			$addr = Network::RealIP();

			// direct blacklisted IP
			if (in_array($addr, Util::split(Conf::get('SECURITY:SPAM:BLACKLIST:IP')))) {

				return TRUE;
			}

			// Validate user against spam blacklists, if ($blacklist and !private and !exempt)
			if (Conf::get('SECURITY:SPAM:BLACKLIST:DNS') && !Network::PrivateIP($addr) && !in_array($addr, Util::split(Conf::get('SECURITY:SPAM:ALLOW:IP'))))
			{
				// Convert to reverse IP dotted quad
				$quad = implode('.', array_reverse(explode('.', $addr)));

				foreach (Util::split(Conf::get('SECURITY:SPAM:BLACKLIST:DNS')) as $dns) {

					// Check against DNS blacklist
					if (gethostbyname($quad.'.'.$dns) != $quad.'.'.$dns) {

						return TRUE;
					}
				}
			}

			return FALSE;
		}

		static function CheckHotlink()
		{
			if (!Conf::get('SECURITY:HOTLINK:CHECK')) {
				return FALSE;
			}

			return (
				Conf::get('SECURITY:HOTLINK:ROUTE') &&
				isset($_SERVER['HTTP_REFERER']) &&
				parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['SERVER_NAME']
			);
		}

	}

?>