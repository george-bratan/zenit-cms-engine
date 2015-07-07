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
			F_FILE = 11,
			F_RICHTEXT = 12,
			F_COLOR = 13,
			F_HIDDEN = 14,
			F_DATERANGE = 15,
			F_MAP = 16,
			F_RANGE = 17;

		function __construct($key)
	    {
	    	//
	    	$this->Name($key);
	    	$this->Align('left');
	    	$this->VAlign('horizontal');
	    	$this->Width('300px');

	    	return parent::__construct();
	    }

	    function Align($value = NULL)
	    {
	    	parent::Align($value);

	    	if ($value == 'right' && $this->Width() == '300px') {
	    		//
	    		$this->Width('230px');
	    	}

	    	return $this;
	    }

	    function Type($value = NULL)
	    {
	    	parent::Type($value);

	    	if ($value == self::F_RICHTEXT && $this->Width() == '300px') {
	    		//
	    		$this->Width('98%');
	    	}

	    	if ($value == self::F_SELECT && $this->Width() == '300px') {
	    		//
	    		$this->Width('312px');
	    	}

	    	return $this;
	    }

		function Render()
		{
			$input = $this->export();

			$default = Array(
				'id' => '',
				'title' => '',
				'name' => '',
				'value' => '',
				'context' => '',
				'type' => self::F_NONE,
				'details' => '',
				'options' => array(),
				'align' => 'left',
				'valign' => 'horizontal',
				'width' => '300px',
				'height' => '150px',
				'multiple' => FALSE,
				'onchange' => '',
				'alt' => '',
			);

			$input['id'] = $input['name'];

			if (isset($input['context'])) {
				if ($input['context']) {
					$input['name'] = $input['context'] . '[' . $input['name'] . ']';
				}
			}

			foreach ($default as $key => $value) {
				if (isset($input[ $key ])) {

					$default[ $key ] = $input[ $key ];
				}
			}

			UI::set('INPUT', $default);

			return UI::Render('admin/.shared.input.php');
		}

	}

?>