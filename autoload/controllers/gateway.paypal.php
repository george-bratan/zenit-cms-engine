<?php

	// GatewayPaypal

	class GatewayPaypal
	{
		private
			$env = 'live';

		private
			$endpoints = array(
				'live' => 'https://api-3t.paypal.com/nvp',
				'sandbox' => 'https://api-3t.sandbox.paypal.com/nvp',
			);

		private
			$user, $pass, $auth,
			$version = '51.0';

		static function onLoad()
		{
			//
		}

	    function __construct($user, $pass, $auth)
	    {
	    	$this->user = $user;
	    	$this->pass = $pass;
	    	$this->auth = $auth;

	    	$this->version = '51.0';

	    	return $this;
	    }

	    function sandbox($method, $postfields)
	    {
	    	$this->env = 'sandbox';

	    	return $this->request($method, $postfields);
	    }

	    function live($method, $postfields)
	    {
	    	$this->env = 'live';

	    	return $this->request($method, $postfields);
	    }

		function request($method, $postfields)
		{
			if (!$this->user || !$this->pass || !$this->auth) {
				return 'No Paypal Auth';
			}

			// Set up your API credentials, PayPal end point, and API version.
			$API_UserName = urlencode('my_api_username');
			$API_Password = urlencode('my_api_password');
			$API_Signature = urlencode('my_api_signature');

			$version = urlencode('51.0');

			// Set the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->endpoints[ $this->env ]);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);

			// Turn off the server and peer verification (TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);

			// Set the API operation, version, and API signature in the request.
			$auth = array(
				'METHOD' => $method,
				'USER' => $this->user,
				'PWD' => $this->pass,
				'SIGNATURE' => $this->auth,
				'VERSION' => $this->version,
			);

			$postreq = http_build_query($auth) . '&' . http_build_query($postfields);

			// Set the request as a POST FIELD for curl.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postreq);

			// Get response from the server.
			$response = curl_exec($ch);

			if(!$response) {
				return "{$method} failed: ".curl_error($ch).'('.curl_errno($ch).')';
			}

			parse_str($response, $result);

			if((0 == sizeof($result)) || !array_key_exists('ACK', $result)) {
				return "Invalid HTTP Response for POST request({$postreq}) to ".($this->endpoints[ $this->env ]).".";
			}

			return $result;
		}

	}

?>