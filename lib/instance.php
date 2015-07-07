<?php

	// Instance

	class Instance
	{
		private
			$vars = Array();

		static function onLoad()
		{
			//
		}

	    function __construct()
	    {
	    	return $this;
	    }

		public function __call($name, $arg)
		{
			// set value
			if (isset($arg[0])) {
				//
				$this->vars[ strtolower($name) ] = $arg[0];
				return $this;
			}

			// get value
			if (isset($this->vars[ strtolower($name) ])) {
				//
				return $this->vars[ strtolower($name) ];
			}

			return NULL;
	    }

	    public function __get($name)
	    {
	    	if (isset($this->vars[ strtolower($name) ])) {
				//
				return $this->vars[ strtolower($name) ];
			}

			return NULL;
	    }

	    public function export()
	    {
	    	return $this->vars;
	    }

	}

?>