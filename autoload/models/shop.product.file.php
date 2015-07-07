<?php

	// Models\ShopProductFile

	namespace Models;

	class ShopProductFile extends \Model {

		static
			$public = Array(
				'title' => 'Title',
				'name' => 'File Name',
			);

		static
			$default = Array(
				'title', 'name',
			);

		static
			$filters = Array(
				'title' => 'Title',
				'name' => 'File Name',
			);

		static
			$schema = Array(
				'title' => 'Title',
				'name' => 'File Name',
				//'disk' => 'Disk Name',
				'description' => 'Description',
			);

		function __construct($id = NULL)
		{
			parent::__construct('shop_products_files');

			$this->def('gallery');
			$this->def('product');
			$this->def('url');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_gallery()
		{
			$gallery = new ShopProductFile();
			$gallery->where('status > -1 AND idproduct = ?', $this->idproduct)
				->order('ord ASC')
				->execute();

			return $gallery;
		}

		function get_product()
		{
			$product = new ShopProduct( $this->idproduct );

			return $product;
		}

		function get_url()
		{
			return \Conf::Get('WWW:UPLOAD') . $this->disk;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
			$this->ord = time();
			$this->title = '';
			$this->description = '';
		}

	}

?>