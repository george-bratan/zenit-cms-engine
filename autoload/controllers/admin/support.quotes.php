<?php

	// SupportQuotes

	class SupportQuotes extends AdminList
	{
		static
			$TITLE  = 'Quotes',
			$IDENT  = 'support.quotes';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/promotion.png',
				'LARGE' => 'icon.large/promotion.png',
			);

		/*
		static
			$PERMISSION = Array(
				'list' 		=> 'List Quotes',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);
		*/

		static
			$AUTH = 'support.quotes';


		static function Model($id = NULL)
		{
			return Model::SupportQuote($id);
		}

		static function GET_List($page = 0)
		{
			UI::nset('FIXED', array('status' => 'Status', 'paid' => 'Paid'));
			UI::nset('FORMAT.status', function($record){
				//
				if ($record['status']) {
					//
					return '<a rel="post" href="'.Request::$URL.'/status/'.$record['id'].'" style="color:'.($record['status']==1 ? 'green' : 'red').'">
								<span >'.($record['status'] == 1 ? 'Accepted' : 'Rejected').'</span>
							</a>';
				}

				return '<a rel="post" href="'.Request::$URL.'/status/'.$record['id'].'" style="color:blue">
							<span>Pending</span>
						</a>';
			});

			UI::nset('FORMAT.paid', function($record){
				//
				return ''.($record['paid'] == 1 ? '<span style="color:green">Paid</span>' : '<span style="color:black">Pending</span>').'';
			});

			parent::GET_List($page);
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$model->status = ($model->status == 1) ? 2 : 1;
			$model->save();
		}

	}

?>