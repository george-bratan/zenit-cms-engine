<?php

	// Models\User

	namespace Models;

	class UserGroup extends \Model {

		static
			$public = Array(
				'name' => 'Name',
			);

		static
			$default = Array(
				'name'
			);

		static
			$filters = Array(
				'name' => 'Group Name',
			);

		static
			$schema = Array(
				'name' => 'Group Name',
				'token' => 'Permission Rights',
			);

		function __construct($id = NULL)
		{
			parent::__construct('usergroups');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>