<?php

	// Page, to be extended

	class Page
	{
        static function onBeforeRoute()
        {
        	return self::onLoad();
        }

        static function onAfterRoute()
        {
        }

		static function onLoad()
		{
		}

		static function Get()
		{
		}

		static function Post()
		{
		}
	}

?>