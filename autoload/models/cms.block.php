<?php

	// Models\CmsBlock

	namespace Models;

	class CmsBlock extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'feed' => 'Data Feed',
			);

		static
			$default = Array(
				'name', 'feed',
			);

		static
			$filters = Array(
				'name' => 'Block Name',
				'feed' => 'Data Feed',
			);

		static
			$schema = Array(
				'name' => 'Block Name',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_blocks');

			$this->def('filters');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_filters()
		{
			parse_str($this->params, $result);

			return $result;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>