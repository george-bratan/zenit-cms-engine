<?php

	// ShopCategories

	class ShopCategories extends AdminModule
	{
		static
			$TITLE  = 'Categories',
			$IDENT  = 'shop.categories';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/star.png',
				'LARGE' => 'icon.large/star.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Categories',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'shop.categories';


		static function Model($id = NULL)
		{
			return Model::ShopCategory($id);
		}

		static function GET_List($page = 0)
		{
			//

			parent::GET_List($page);
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			$model->stub = Util::URL( $model->name );
			$model->save();

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			//

			return $fields;
		}

	}

?>