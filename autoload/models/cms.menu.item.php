<?php

	// Models\CmsMenuItem

	namespace Models;

	class CmsMenuItem extends \Model {

		static
			$public = Array(
				'caption' => 'Caption',
				'url' => 'URL',
			);

		static
			$default = Array(
				'caption', 'url'
			);

		static
			$filters = Array(
				'name' => 'Menu Name',
			);

		static
			$schema = Array(
				'idmenu' => 'Menu',
				'caption' => 'Caption',
				'url' => 'URL',
				//'idpage' => 'Internal Page',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_menu_items');

			$this->def('parent');
			$this->def('children');
			//$this->def('level');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_parent()
		{
			if (!$this->idparent) {
				//
				return NULL;
			}

			$item = new CmsMenuItem( $this->idparent );
			return $item;
		}

		function get_children()
		{
			$items = new CmsMenuItem();
			$items->where('status > -1 AND idparent = ?', $this->id)
				->order('ord ASC')
				->execute();

			return $items;
		}

		/*
		function get_level()
		{
			$level = 0;
			$item = $this;

			while ($item->parent) {
				//
				$level++;
				$item = $item->parent;
			}

			return $level;
		}
		*/

		function get_name()
		{
			return $this->caption;
		}

		function defaults()
		{
			parent::defaults();

			$this->idmenu = 0;
			$this->idpage = 0;
			$this->idparent = 0;
			$this->level = 0;
			$this->ord = 0;
			$this->status = 1;
		}

	}

?>