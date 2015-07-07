<?php

	// ADMIN
	if (preg_match('/^'.preg_quote(Conf::get('WWW:ROOT'),'/').'\/admin\/(.*)/', rawurldecode($_SERVER['REQUEST_URI']))) {
		//
		Admin::Routes('/admin/');
	}

	Router::Route('GET /www/admin', function() {
		//
		Request::Redirect('/admin/');
	});

	// Catch-All
	//Router::Map('/admin/*', 'E404');

	// UPLOADS
	Router::Route('GET /uploads/*', 'GET_UploadedFile');

	function GET_UploadedFile()
	{
		$file = str_replace('/uploads', '', Request::$URI);

		header("Content-type: image/" .$ext = pathinfo(rawurldecode($file), PATHINFO_EXTENSION));
		header("Content-Transfer-Encoding: Binary");
		header("Content-length: ".filesize(Conf::Get('APP:UPLOAD') . rawurldecode($file)));
		header('Content-Disposition: inline; filename="' . basename(rawurldecode($file)) . '"');
		readfile(Conf::Get('APP:UPLOAD') . rawurldecode($file));
	}

	// PUBLIC
	$routes = DB::AssociativeColumn("SELECT id, url FROM cms_templates WHERE type = 'cms.page' AND version > 0 AND status > 0");
	foreach ($routes as $id => $multiroute) {
		//
		if ($multiroute) {
			//
			$multiroute = Util::split($multiroute);
			foreach ($multiroute as $route) {
				//
				if (trim($route)) {
					//
					Router::Route('GET ' . trim($route),
						array('PublicServer', 'Serve', $id) );

					Router::Route('POST ' . trim($route),
						array('PublicServer', 'Serve', $id) );
				}
			}
		}
	}

	$home = Model::Settings('cms.index')->value;
	Router::Route('GET /', array('PublicServer', 'Serve', $home) );

	// 404, match everything left
	Router::Route('GET /@route', array('PublicServer', 'Serve') );

?>