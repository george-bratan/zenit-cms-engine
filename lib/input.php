<?php

	// Input

	class Input extends Instance
	{
        const
        	F_NONE = 0,
			F_KEY = 1,
			F_TEXT = 2,
			F_EMAIL = 3,
			F_SELECT = 4,
			F_DATE = 5,
			F_LONGTEXT = 6,
			F_BOOL = 7,
			F_CHECKGROUP = 8,
			F_RADIOGROUP = 9,
			F_PASSWORD = 10,
			F_FILE = 11;

		function __construct($key)
	    {
	    	//
	    	$this->Name($key);
	    	$this->Align('left');

	    	return parent::__construct();
	    }

		function Render()
		{
			$input = $this->Export();

			$default = Array(
				'title' => '',
				'name' => '',
				'value' => '',
				'context' => '',
				'type' => self::F_NONE,
				'details' => '',
				'options' => array(),
				'align' => 'left',
			);

			if (isset($input['context'])) {
				$input['name'] = $input['context'] . '[' . $input['name'] . ']';
			}

			foreach ($default as $key => $value) {
				if (isset($input[ $key ])) {

					$default[ $key ] = $input[ $key ];
				}
			}

			UI::set('field', $default);

			return UI::Render('admin/.shared.input.php');
		}

	}

?>