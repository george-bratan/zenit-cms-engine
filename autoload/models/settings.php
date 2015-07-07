<?php

	// Models\Settings

	namespace Models;

	class Settings extends \Model {

		function __construct($id = NULL)
		{
			parent::__construct('settings');

			if ($id)
			{
				$this->where('name = ?', $id);
				$this->execute();
			}

			return $this;
		}

	}

?>