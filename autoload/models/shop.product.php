<?php

	// Models\ShopProduct

	namespace Models;

	class ShopProduct extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'price' => 'Price',
				'category' => 'Category',
			);

		static
			$default = Array(
				'name', 'price', 'category',
			);

		static
			$filters = Array(
				'name' => 'Product Name',
				'price' => 'Price',
				'idcategory' => 'Category',
				'keywords' => 'Keywords',
				'description' => 'Containing',
			);

		static
			$schema = Array(
				'name' => 'Product Name',
				'keywords' => 'Keywords',
				'idcategory' => 'Category',
				'price' => 'Base Price',
				'sale' => 'On-Sale Price',
				'description' => 'Description',
				'stock' => 'Stock Levels',
			);


		function __construct($id = NULL)
		{
			parent::__construct('shop_products');

			$this->def('images'); // multiple
			$this->def('category');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_images()
		{
			$images = new ShopProductFile();
			$images->where('idproduct = ? AND status > -1', $this->id)
				->order('ord ASC')
				->execute();

			return $images;
		}

		function get_category()
		{
			 $category = new ShopCategory( $this->idcategory );
			 return $category->name;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>