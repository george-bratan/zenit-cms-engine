<?php

	// PublicServer

	class PublicServer
	{
		static function Render( $id )
		{
			$model = Model::CmsTemplate( intval( $id ) );

			$result = $model->html;

			$result = preg_replace_callback('/\{(EDITABLE )(.*)\}/iU',
				function($matches){ return ''; }, $result);

			$result = preg_replace_callback('/\{(BLOCK )(.*)\}/iU',
				function($matches){ return Admin::HtmlFeed($matches[2]); }, $result);

			$result = str_replace('</head>', '<style type="text/css">'."\n".($model->css)."\n".'</style>'."\n".'</head>', $result);

			return $result;
		}

		static function Serve($id = 0, $mime = 'text/html')
		{
			if (!$id) {
				//
				$id = Model::Settings('cms.notfound')->value;
			}

			$STATUS = Model::Settings('cms.status')->value;
			if ($STATUS != 'OPEN') {
				//
				$id = Model::Settings('cms.closed')->value;
			}

			if (!$id) {
				//
				Application::Error(404);
				return;
			}

			$out = self::Render($id);

			if (PHP_SAPI != 'cli') {
				// Send HTTP header with appropriate character set
				header(Request::HTTP_Content.': '.$mime.'; '.'charset='.Conf::get('HTTP:ENCODING'));
			}

			DB::Execute("INSERT INTO cms_views SET ip = ?, date = NOW(), request = ?", array(Request::SERVER('REMOTE_ADDR'), Request::$METHOD .' '. Request::$URI));

			print $out;
		}
	}

?>