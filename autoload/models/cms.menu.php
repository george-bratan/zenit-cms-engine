<?php

	// Models\CmsMenu

	namespace Models;

	class CmsMenu extends \Model {

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
				'name' => 'Menu Name',
			);

		static
			$schema = Array(
				'name' => 'Menu Name',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_menus');

			$this->def('items');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_items( $level = null )
		{
			$items = new CmsMenuItem();

			if ($level !== null) {
				//
				$items->where('level = ?', $level);
			}

			$items->where('status > -1 AND idmenu = ?', $this->id)
				->order('ord ASC')
				->execute();

			return $items;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>