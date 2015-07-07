<?php

	// ShopDeliveries

	class ShopDeliveries extends AdminModule
	{
		static
			$TITLE  = 'Deliveries',
			$IDENT  = 'shop.deliveries';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/calendar.png',
				'LARGE' => 'icon.large/calendar.red.png',
			);

		/*
		static
			$PERMISSION = Array(
				'list' 		=> 'List Deliveries',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'shop.deliveries';
		*/

		static
			$STATUS = Array(
				0 => 'Scheduled',
				1 => 'Sent',
				2 => 'Pending',
				3 => 'Cancelled',
			);


		static function Model($id = NULL)
		{
			return Model::ShopDelivery($id);
		}

		static function GET_List($page = 0)
		{
			/*
			UI::set('OPTIONS.details', Array(
				'handler' => 'details',
				'icon' => 'icon.small/edit.png',
				'title' => 'Details',
			));
			*/

			/*
			UI::set('FORMAT.client', function($record) {
				//
				return $record['client'] . (date('Y-m-d') == date('Y-m-d', strtotime($record['date'])) ? ' <span style="color:red">(New)</span>' : '');
			});
			*/

			UI::set('FORMAT.date', function($record) {
				//
				return intval($record['date']) ? $record['date'] : '';
			});

			UI::set('FORMAT.order', function($record) {
				//
				$order = Model::ShopOrder($record['idorder']);

				return $order->client . ' (#' . $order->id . ')';
			});

			UI::set('FORMAT.status', function($record) {
				//
				return '<span style="color:'.($record['status'] == 3 ? 'red' : ($record['status'] == 2 ? 'blue' : ($record['status'] == 1 ? 'green' : 'black'))).'">'.
							ShopDeliveries::$STATUS[ $record['status'] ].'</span>';
			});

			parent::GET_List( $page );
		}

		static function GET_New()
		{
			$fields = static::EditForm( static::Model() );

			if (intval( Request::URL('id') )) {
				//
				$fields['idorder']->Value( intval( Request::URL('id') ) );
			}

			UI::set('TITLE', 'New Record');
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Send()
		{
			$model = Model::ShopDelivery( intval( Request::URL('id') ) );
			$model->date = '@NOW()';

			$model->save();
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if (!intval($model->date) && $model->status == 1) {
				//
				$model->date = '@NOW()';
				$model->save();
			}

			if (!$model->ord) {
				//
				$model->ord = 1; //$model->order->deliveries->found();
				$model->save();
			}

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$fields['idorder']->Type(Input::F_HIDDEN);
			$fields['scheduled']->Type(Input::F_DATE);
			$fields['date']->Type(Input::F_DATE);

			$fields['status']->Type(Input::F_SELECT)->Options( self::$STATUS );

			unset($fields['date']);

			return $fields;
		}

	}

?>