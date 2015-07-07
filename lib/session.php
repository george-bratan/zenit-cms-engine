<?php

	// SESSION

	class Session extends Object
	{
		static function Init()
		{
			session_start();
		}

		public static function &vars()
		{
			return $_SESSION;
		}
	}

?>