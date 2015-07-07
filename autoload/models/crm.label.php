<?php

	// Models\CrmLabel

	namespace Models;

	class CrmLabel extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'type' => 'Type',
				'color' => 'Color',
			);

		static
			$default = Array(
				'name', 'type', 'color',
			);

		static
			$filters = Array(
				'name' => 'Label Name',
				'type' => 'Label Type',
			);

		static
			$schema = Array(
				'name' => 'Label Name',
				'type' => 'Label Type',
				'color' => 'Color',
			);

		const
			F_COLOR = 0,
			F_SUBJECT = 1,
			F_FLAG = 2;

		function __construct($id = NULL)
		{
			parent::__construct('crm_labels');

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