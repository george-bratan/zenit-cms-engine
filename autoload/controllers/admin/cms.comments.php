<?php

	// CmsComments

	class CmsComments extends AdminList
	{
		static
			$TITLE  = 'Comments',
			$IDENT  = 'cms.comments';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/comments.png',
				'LARGE' => 'icon.large/comments.png',
			);

		static
			$AUTH = 'cms.comments';


		static function Model($id = NULL)
		{
			return Model::CmsArticleComment($id);
		}

		static function GET_List($page = 0)
		{
			UI::set('FORMAT.article', function($record) {
				//
				$article = Model::CmsArticle( $record['idarticle'] );
				return $article->title;
			});

			UI::nset('FIXED', array('status' => 'Status'));
			UI::nset('FORMAT.status', function($record){
				//
				if ($record['status']) {
					//
					return '<a rel="post" href="'.Request::$URL.'/status/'.$record['id'].'" style="color:'.($record['status']==1 ? 'green' : 'red').'">
								<span >'.($record['status'] == 1 ? 'Published' : 'Rejected').'</span>
							</a>';
				}

				return '<a rel="post" href="'.Request::$URL.'/status/'.$record['id'].'" style="color:blue">
							<span >Pending</span>
						</a>';
			});

			parent::GET_List($page);
		}

		static function POST_Status()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$model->status = ($model->status == 1) ? -1 : 1;
			$model->save();
		}

		static function FilterForm()
		{
			$filters = parent::FilterForm();

			$filters['date']->Type(Input::F_DATERANGE);

			return $filters;
		}

	}

?>