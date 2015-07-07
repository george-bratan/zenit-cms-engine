<?php

	// CmsBlocks

	class CmsBlocks extends AdminModule
	{
		static
			$TITLE  = 'Content',
			$IDENT  = 'cms.blocks';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/cut.png',
				'LARGE' => 'icon.large/cut.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Blocks',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'cms.blocks';


		static function Model($id = NULL)
		{
			return Model::CmsBlock($id);
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}

			parent::GET_List($page);
		}

		static function GET_Details($feed = NULL, $html = NULL)
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (Session::Get('TMP.BLOCKS.FEED') || Session::Get('TMP.BLOCKS.HTML')) {
				$model->feed = Session::Get('TMP.BLOCKS.FEED');
				$model->html = Session::Get('TMP.BLOCKS.HTML');
				$model->css = Session::Get('TMP.BLOCKS.CSS');
				$model->js = Session::Get('TMP.BLOCKS.JS');

				Session::Clear('TMP.BLOCKS');
			}

			if ($feed || $html) {
				$model->feed = $feed;
				$model->html = $html;
				$model->css = $css;
				$model->js = $js;
			}

			UI::set('BLOCK', $model->record());

			$feeds = Admin::DataFeedEx();
			UI::set('FEEDS', array_merge(array('' => '-'), $feeds));

			if ($model->feed) {
				//
				$feed = Admin::DataFeed( $model->feed );
				UI::set('FEED', $feed);
			}

			UI::set('CONTENT', UI::Render('admin/cms.block.details.php'));
			UI::set('TOOLBAR.save', array(
					'id' => 'btn_save',
					'url' => Request::$URL . '/save/'. $model->id,
					'rel' => 'none',
					'icon' => 'icon.small/disk.png',
					'title' => 'Save'
				)
			);

			UI::set('SECTION', 'Content Block: '.$model->name);

			self::Wrapper();
		}

		static function GET_Edit()
		{
			parent::GET_Details();
		}

		static function POST_Feed()
		{
			Session::Set('TMP.BLOCKS.FEED', Request::POST('VALUES.feed'));
			Session::Set('TMP.BLOCKS.HTML', Request::POST('VALUES.html'));
			Session::Set('TMP.BLOCKS.CSS', Request::POST('VALUES.css'));
			Session::Set('TMP.BLOCKS.JS', Request::POST('VALUES.js'));
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if (Request::POST('VALUES.html') !== null) {
				$model->html = Request::POST('VALUES.html');
			}

			if (Request::POST('VALUES.css') !== null) {
				$model->css = Request::POST('VALUES.css');
			}

			if (Request::POST('VALUES.js') !== null) {
				$model->js = Request::POST('VALUES.js');
			}

			if (Request::POST('VALUES.feed') !== null) {
				$model->feed = Request::POST('VALUES.feed');
			}

			if (Request::POST('FILTERS')) {
				//
				$model->params = http_build_query( Request::POST('FILTERS') );
			}

			$model->save();

			return $model;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			return $fields;
		}

		static function HtmlFeed( $feed = NULL )
		{
			if (!$feed) {
				//
				$blocks = Model::CmsBlock();
				$blocks->where('status > -1')->execute();

				$feeds = Admin::DataFeed();

				$result = array();
				if ($blocks->found()) {
					//
					$blocks->reset();
					while ($blocks->next()) {
						//
						$hint = '';
						if ($blocks->feed) {
							//
							if (is_array($feeds[ $blocks->feed ]))
								$hint = $feeds[ $blocks->feed ]['hint'];
						}

						$result['CMS.BLOCK.'.$blocks->id] = $hint ? array('title' => $blocks->name, 'hint' => $hint) : $blocks->name;
					}
				}

				return $result;
			}

			$id = str_replace('CMS.BLOCK.', '', $feed);

			$block = Model::CmsBlock( $id );

			$html = $block->html;
			if ($block->feed) {
				//
				$data = Admin::DataFeed( $block->feed,  $block->filters);

				$html = UI::RenderHtml( $html, $data['RESULT'] );
			}

			if ($block->css) {
				$html .= '<style type="text/css">'."\n".($block->css)."\n".'</style>';
			}

			if ($block->js) {
				$html .= '<script type="text/javascript">'."\n".($block->js)."\n".'</script>';
			}

			return $html;
		}

	}

?>