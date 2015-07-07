<?php

	// ShopDiscounts

	class ShopDiscounts extends AdminModule
	{
		static
			$TITLE  = 'Discounts',
			$IDENT  = 'shop.discounts';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/currency.dollar.png',
				'LARGE' => 'icon.large/currency.dollar.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Discounts',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'shop.discounts';

		static
			$TYPE = Array(
				0 => 'Flat',
				1 => 'Percent',
			);


		static function onLoad()
		{
			self::$TYPE[0] = 'Flat (' . Model::Settings('shop.currency')->value . ')';
		}

		static function Model($id = NULL)
		{
			return Model::ShopDiscount($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('FORMAT.value', function($record) {
				//
				if ($record['type'] == 0) {
					//
					return $record['value'] . ' ' . Model::Settings('shop.currency')->value;
				}

				if ($record['type'] == 1) {
					//
					return $record['value'] . '%';
				}

				return $record['value'];
			});

			parent::GET_List( $page );
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if ($model->type == 1) {
				//
				if ($model->value < 0)
					$model->value = 0;

				if ($model->value > 100)
					$model->value = 100;

				$model->save();
			}

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['type']->Type(Input::F_SELECT)->Options( self::$TYPE );

			return $fields;
		}

		static function FilterForm()
		{
			$filters = parent::FilterForm();

			$filters['type']->Type(Input::F_SELECT)->Options( self::$TYPE );

			return $filters;
		}

	}

?>