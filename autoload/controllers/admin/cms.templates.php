<?php

	// CmsTemplates

	class CmsTemplates extends AdminModule
	{
		static
			$TITLE  = 'Templates',
			$IDENT  = 'cms.templates';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/page.png',
				'LARGE' => 'icon.large/page.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Templates',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'cms.templates';

		static
			$TYPE = 'cms.template';

		static
			$VERSIONS = Array(
				0 => 'DEVELOPMENT',
				-1 => 'SESSION',
			);


		static function Model($id = NULL)
		{
			return Model::CmsTemplate($id);
		}

		static function Where($model)
		{
			if (static::$TYPE) {
				//
				$model->where('type = ?', static::$TYPE);
			}

			$model->order('ord ASC');

			return $model;
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

			UI::set('FORMAT.name', function($record) {
				//
				$parents = explode('-', $record['ord']);
				array_pop($parents);

				$count = 0;
				foreach ($parents as $parent) {
					$type = DB::Fetch('SELECT type FROM cms_templates WHERE id = ?', array($parent));
					if ($type == $record['type']) {
						$count++;
					}
				}

				$result = '';
				for ($i = 0; $i < $count; $i++) {
					$result .= ' &nbsp; &nbsp; &nbsp; ';
				}

				if ($count) {
					$result .= ' <span style="position:relative; top:-3px;">&lfloor;</span> &nbsp; ';
				}

				return $result . $record['name'];
			});

			UI::nset('FORMAT.status', function($record){
					return '<span style="color:'.($record['version'] ? 'green' : 'red').'">'.
						($record['version'] ? 'Published' : 'Draft').'</span>';
				}
			);

			parent::GET_List($page);
		}

		static function Toolbar()
		{
			//
		}

		static function GET_Details()
		{
			$model = static::Model( intval( Request::URL('id') ) );
			$model->version = 0;

			UI::set('TEMPLATE', $model->record());
			UI::set('TEMPLATE.ELEMENTS', $model->elements);
			UI::set('TEMPLATE.CONTENT', $model->content->record());

			UI::nset('TOOLBAR.more', array(
					'url' => 'javascript:void(0);',
					'rel' => '#',
					'id' => 'btn_more',
					'title' => 'Hide &raquo;'
				)
			);

			UI::nset('TOOLBAR.save', array(
					'url' => 'javascript:void(0);',
					'rel' => '#',
					'id' => 'btn_save',
					'icon' => 'icon.small/disk.png',
					'title' => 'Save'
				)
			);

			UI::nset('TOOLBAR.publish', array(
					'url' => Request::$URL . '/publish/' . $model->id,
					'rel' => 'modal',
					'icon' => 'icon.small/accept.png',
					'title' => 'Publish'
				)
			);

			UI::nset('TABBAR', array(
			    	'preview' => 'Preview',
					'html' => 'HTML',
					'css' => 'CSS',
					'js' => 'JS',
				)
			);

			static::Toolbar();

			UI::set('CONTENT', UI::Render('admin/cms.template.main.php'));


			$versions = Model::CmsTemplateVersion();
			$versions->where('idtemplate = ? AND number > 0', $model->id)
				->order('date DESC')->execute();

			UI::set('VERSIONS', $versions->export());

			/*
			UI::set('PANELS.inspector', Array(
				'TITLE' => 'Properties',
				'TABBAR' => array('iseo' => 'SEO', 'icss' => 'CSS'),
				'CONTENT' => UI::Render('admin/cms.template.inspector.php'),
			));
			*/
			UI::set('PANELS.meta', Array(
				'TITLE' => 'Meta',
				'TOOLBAR' => array('hide' => array(
					'url' => 'javascript:void(0);',
					'rel' => '#',
					'id' => 'btn_hide',
					'title' => 'Hide &raquo;'
				)),
				'CONTENT' => UI::Render('admin/cms.template.meta.php'),
			));

			UI::set('PANELS.history', Array(
				'TITLE' => 'History',
				'CONTENT' => UI::Render('admin/cms.template.history.php'),
			));

			UI::set('SECTION', 'Template: '.$model->name);

			static::Wrapper($columns = 'two');
		}

		static function GET_Content()
		{
			UI::set('TABBAR', array('article' => 'Article', 'image' => 'Picture', 'link' => 'Link To', 'other' => 'Other'));

			$feed = Admin::HtmlFeed();
			UI::set('FEED', $feed);

			$pages = DB::AssociativeColumn("SELECT url, name FROM cms_templates WHERE type = 'cms.page' AND version > 0 AND status > -1 AND url != '' ORDER BY name ASC");
			UI::set('PAGES', $pages);

			UI::set('TITLE', 'Add Content');
			UI::set('CONTENT', UI::Render('admin/cms.template.content.php'));

			parent::Popup();
		}

		static function GET_Revert()
		{
			$version = Model::CmsTemplateVersion( intval(Request::URL('id')) );

			UI::set('TARGET', Request::$URL . '/revert/' . $version->id);
			UI::set('CONTENT', 'Are you sure you want to revert to this version ?<br />'.
				'This action will publish a different version of your website, as published on <strong>'.($version->date).'</strong> by <strong>'.($version->author).'</strong>:'.
				'<br /><br /><em style="font-style:italic">'.($version->details).'</em>' );

			UI::set('TITLE', 'Revert to Version');
			UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

			parent::Popup();
		}

		static function POST_Revert()
		{
			$version = Model::CmsTemplateVersion( intval(Request::URL('id')) );
			$template = Model::CmsTemplate( $version->idtemplate );

			$template->version = $version->number;
			$template->save();
		}

		static function GET_Load()
		{
			$version = Model::CmsTemplateVersion( intval(Request::URL('id')) );

			UI::set('TARGET', Request::$URL . '/load/' . $version->id);
			UI::set('CONTENT', 'Are you sure you want to load this version in the editor ?<br />'.
				'You will lose all your current changes that have not been published yet.' );

			UI::set('TITLE', 'Load Version');
			UI::set('CONTENT', UI::Render('admin/.shared.confirm.php'));

			parent::Popup();
		}

		static function POST_Load()
		{
			$version = Model::CmsTemplateVersion( intval(Request::URL('id')) );
			$template = Model::CmsTemplate( $version->idtemplate );

			static::MoveVersion($template->id, $version->number, 0);
		}

		static function GET_Publish()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$input = new Input('details');
			$input->Type(Input::F_LONGTEXT)->Title('Changes in this version')->Context('VALUES')->Width('98%')->Value( '' );
			$fields = array( $input );

			UI::set('TITLE', 'Version Notes');
			UI::set('TARGET', Request::$URL . '/publish/' . $model->id);
			UI::set('FIELDS', $fields);

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Publish()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			// get new max, move dev/0 to new version
			$version = DB::Fetch("SELECT MAX(number) FROM cms_template_versions WHERE idtemplate = ?", array($model->id)) + 1;
			static::MoveVersion($model->id, 0, $version);

			$model->version = $version;
			$model->save();
		}

		static function GET_Preview($v = 0)
		{
			$model = static::Model( intval( Request::URL('id') ) );
			$model->version = $v;

			$result = $model->html;

			$result = preg_replace_callback('/\{(EDITABLE |BLOCK )(.*)\}/iU',
				"self::_replace", $result);

			$result = str_replace('</head>', '<style type="text/css">'."\n".($model->css)."\n".'</style>'."\n".'</head>', $result);
			$result = str_replace('</head>', '<script type="text/javascript">'."\n".($model->js)."\n".'</script>'."\n".'</head>', $result);

			/*
			// set opacity to all elements, except editable ones
			if (count($model->elements) && $model->idparent) {
				//
				$result = str_replace('<body', '<body opacity="on"', $result);
			}
			*/

			// only inject the editor if we have editable elements
			if (count($model->elements)) {
				//
				/*
				if (strpos($result, 'jquery') === false) {
					$result = str_replace('</head>', '<script type="text/javascript" src="'.Conf::Get('WWW:ROOT').'/admin/js/jquery.js"></script>'."\n".'</head>', $result);
				}

				$result = str_replace('</head>', '<link href="'.Conf::Get('WWW:ROOT').'/admin/js/contextmenu/jquery.contextmenu.css" rel="stylesheet" type="text/css" media="all">'."\n".'</head>', $result);

				$result = str_replace('</head>', '<script type="text/javascript" src="'.Conf::Get('WWW:ROOT').'/admin/js/contextmenu/jquery.contextmenu.js"></script>'."\n".'</head>', $result);
				$result = str_replace('</head>', '<script type="text/javascript" src="'.Conf::Get('WWW:ROOT').'/admin/js/wombat.editor.js"></script>'."\n".'</head>', $result);
				*/

				$result = str_replace('</head>', '<link href="'.Conf::Get('WWW:ROOT').'/admin/css/wombat.editor.css" rel="stylesheet" type="text/css" media="all">'."\n".'</head>', $result);
			}

			// remove links, replace with #
			$result = preg_replace_callback('/\<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>/iU',
						function($matches){ return str_replace($matches[2], 'javascript:void(0)', $matches[0]); }, $result);

			if (PHP_SAPI != 'cli') {
				// Send HTTP header with appropriate character set
				header(Request::HTTP_Content.': text/html; '.'charset='.Conf::get('HTTP:ENCODING'));
			}

			print $result;
		}

		static function POST_Preview()
		{
			$version = 0;

			if (Request::POST('action') == 'save') {
				//
				$version = 0;
			}

			if (Request::POST('action') == 'preview') {
				//
				$version = -1;
			}

			static::SaveVersion( $version );
			static::GET_Preview( $version );
		}

		static function _replace($matches)
		{
			switch ($matches[1])
			{
				case 'EDITABLE ':
					//
					$result = '<div class="z-slot"><div class="z-cell">'.$matches[0].'</div></div>';
				break;

				case 'BLOCK ':
					//
					//$result = '<div class="z-block"><div class="z-cell">'.$matches[0].'</div></div>';
					$result = Admin::HtmlFeed($matches[2]);
				break;
			}

			if ($result) {
				//
				return $result;
			}

			return $matches[0];
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			$model->ord = $model->id;
			if ($model->idparent) {
				//
				$model->ord = $model->parent->ord . '-' . $model->id;
			}

			$model->type = static::$TYPE;
			$model->save();

			// CREATE DEVELOPMENT VERSION

			$version = Model::CmsTemplateVersion();
			$version->defaults();
			$version->idtemplate = $model->id;
			$version->details = self::$VERSIONS[ 0 ];

			$version->save();

			return $model;
		}

		static function POST_Meta()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			$model->url = Request::POST('url');
			$model->meta_title = Request::POST('meta_title');
			$model->meta_keywords = Request::POST('meta_keywords');
			$model->meta_description = Request::POST('meta_description');

			$model->save();
		}

		static function SaveVersion( $v = 0 )
		{
			$model = static::Model( intval( Request::URL('id') ) );
			$model->version = $v;

			$version = $model->content;
			if (!$version->found()) {
				$version->defaults();
			}
			$version->idtemplate = $model->id;
			$version->number = $model->version;
			$version->details = self::$VERSIONS[ $v ] ? self::$VERSIONS[ $v ] : $version->details;
			$version->css = Request::POST('css');
			$version->js = Request::POST('js');
			$version->save();

			$elements = Request::POST('html');
			if (is_array($elements)) {
				//
				foreach ($elements as $slot => $content) {
					//
					$element = Model::CmsTemplateElement();
					$element->where('idtemplate = ? AND slot = ? AND version = ?', $model->id, $slot, $model->version)
						->execute();

					$element->defaults();

					$element->idtemplate = $model->id;
					$element->version = $model->version;
					$element->slot = $slot;
					$element->content = $content;
					$element->save();
				}
			}
		}

		static function MoveVersion($id, $src, $dst)
		{
			$model = static::Model( $id );
			$model->version = $src;

			// OVERWRITE DESTINATION VERSION
			$version = Model::CmsTemplateVersion();
			$version->where('idtemplate = ? AND number = ?', $model->id, $dst)
				->execute();
			$version->defaults();

			$version->idtemplate = $model->id;
			$version->number = $dst;
			$version->details = Request::POST('VALUES.details');
			$version->css = $model->content->css;
			$version->js = $model->content->js;
			$version->save();

			// OVERWRITE NEW VERSION ELEMENTS
			$elements = $model->content->elements;
			if ($elements->found()) {
				//
				$elements->reset();
				while ($elements->next()) {
					//
					$element = Model::CmsTemplateElement();
					$element->where('idtemplate = ? AND version = ? AND slot = ? ', $model->id, $version->number, $elements->slot)
						->execute();
					$element->defaults();

					$element->idtemplate = $model->id;
					$element->version = $version->number;
					$element->slot = $elements->slot;
					$element->content = $elements->content;
					$element->save();
				}
			}
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			list($type) = explode('.', static::$TYPE);
			$parents = DB::AssociativeColumn("SELECT id, CONCAT(REPEAT(' &nbsp; ', (LENGTH(ord) - LENGTH(REPLACE(ord, '-', ''))) / LENGTH('-')), IF((LENGTH(ord) - LENGTH(REPLACE(ord, '-', ''))) / LENGTH('-') > 0, ' &lfloor; ', ''), name) FROM cms_templates WHERE version > 0 AND type LIKE '{$type}.template' ORDER BY ord ASC");
			//$parents = DB::AssociativeColumn("SELECT id, CONCAT(REPEAT(' &nbsp; ', (LENGTH(ord) - LENGTH(REPLACE(ord, '-', ''))) / LENGTH('-')), IF((LENGTH(ord) - LENGTH(REPLACE(ord, '-', ''))) / LENGTH('-') > 0, ' &lfloor; ', ''), name) FROM cms_templates WHERE version > 0 AND type = 'cms.template' ORDER BY ord ASC");

			$a_parents = array('-');
			foreach ($parents as $k => $v) {
				$a_parents[ $k ] = $v;
			}

			$fields['idparent']->Type(Input::F_SELECT)->Options( $a_parents );

			return $fields;
		}

		static function FilterForm()
		{
			$fields = parent::FilterForm();

			return $fields;
		}

	}

?>