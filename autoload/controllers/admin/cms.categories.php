<?php

	// CmsCategories

	class CmsCategories extends AdminModule
	{
		static
			$TITLE  = 'Categories',
			$IDENT  = 'cms.categories';

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
			$AUTH = 'cms.categories';


		static function Model($id = NULL)
		{
			return Model::CmsCategory($id);
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