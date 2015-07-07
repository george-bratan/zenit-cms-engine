<?php

	// CmsDocuments

	class CmsDocuments extends AdminPage
	{
		static
			$TITLE = 'Documents';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/folder.png',
				'LARGE' => 'icon.large/folder.png',
			);

		static
			$HELP = 'cms.documents';

		static
			$AUTH = 'cms.documents';

		static
			$CONFIG = Array();

		static function onLoad()
		{
			/**
			 *	Language settings
			 */
			$config['culture'] = 'en';

			/**
			 *	PHP date format
			 *	see http://www.php.net/date for explanation
			 */
			$config['date'] = 'd M Y H:i';

			/**
			 *	Icons settings
			 */
			$config['icons']['path'] = 'images/fileicons/';
			$config['icons']['directory'] = '_Open.png';
			$config['icons']['default'] = 'default.png';

			/**
			 *	Upload settings
			 */
			$config['upload']['overwrite'] = false; // true or false; Check if filename exists. If false, index will be added
			$config['upload']['size'] = false; // integer or false; maximum file size in Mb; please note that every server has got a maximum file upload size as well.
			$config['upload']['imagesonly'] = false; // true or false; Only allow images (jpg, gif & png) upload?

			/**
			 *	Images array
			 *	used to display image thumbnails
			 */
			$config['images'] = array('jpg', 'jpeg','gif','png');


			/**
			 *	Files and folders
			 *	excluded from filtree
			 */
			$config['unallowed_files'] = array('.htaccess');
			$config['unallowed_dirs'] = array('_thumbs','.CDN_ACCESS_LOGS', 'cloudservers');

			/**
			 *	FEATURED OPTIONS
			 *	for Vhost or outside files folder
			 */
			$config['root'] = Conf::Get('APP:ROOT') . 'www/admin/filemanager/';
			$config['doc_root'] = rtrim( Conf::Get('APP:UPLOAD'), '/' ); // No end slash


			/**
			 *	Optional Plugin
			 *	rsc: Rackspace Cloud Files: http://www.rackspace.com/cloud/cloud_hosting_products/files/
			 */
			$config['plugin'] = null;
			//$config['plugin'] = 'rsc';

			$config['connector'] = Request::$URL;


			self::$CONFIG = $config;
		}

		static function FM()
		{
			$fm = new CmsDocumentsManager(self::$CONFIG);

			if(isset($_GET['mode']) && $_GET['mode']!='') {

				switch($_GET['mode']) {

				default:

					$fm->error($fm->lang('MODE_ERROR'));
					break;

				case 'getinfo':

					if($fm->getvar('path')) {
						$response = $fm->getinfo();
					}
					break;

				case 'getfolder':

					if($fm->getvar('path')) {
						$response = $fm->getfolder();
					}
					break;

				case 'rename':

					if($fm->getvar('old') && $fm->getvar('new')) {
						$response = $fm->rename();
					}
					break;

				case 'delete':

					if($fm->getvar('path')) {
						$response = $fm->delete();
					}
					break;

				case 'addfolder':

					if($fm->getvar('path') && $fm->getvar('name')) {
						$response = $fm->addfolder();
					}
					break;

				case 'download':

					if($fm->getvar('path')) {
						$fm->download();
					}
					break;

				case 'preview':

					if($fm->getvar('path')) {
						$fm->preview();
					}
					break;

				}

			}

			if(isset($_POST['mode']) && $_POST['mode']!='') {

				switch($_POST['mode']) {

					default:

						$fm->error($fm->lang('MODE_ERROR'));
						break;

					case 'add':

						if($fm->postvar('currentpath')) {
							$fm->add();
						}
						break;

				}

			}

			if (isset($response)) {
				print json_encode($response);
			}
		}

		static function Delete()
		{
			if (Request::URL('handler') == 'upload') {
				//
				self::GET_Upload();
				return;
			}

			Application::Error(404);
		}

		static function Get()
		{
			if (Request::URL('handler') == 'upload') {
				//
				self::GET_Upload();
				return;
			}

			if (Request::URL('handler') == 'select') {
				//
				self::GET_Select();
				return;
			}

			if (Request::GET('mode') || Request::POST('mode')) {
				//
				self::FM();
				return;
			}

			UI::set('CONTENT', UI::Render('admin/cms.documents.php'));

			parent::Get();
		}

		static function GET_Select()
		{
			UI::set('QS', http_build_query( Request::GET() ));

			if (Request::GET('height')) {
				UI::set('HEIGHT', Request::GET('height'));
			}

			UI::set('TITLE', 'Select File');
			UI::set('CONTENT', UI::Render('admin/cms.documents.php'));

			parent::Popup();
		}

		static function GET_Upload()
		{
			include( 'cms.documents.uploader.php' );
		}

		static function Post()
		{
			static::Get();
		}

	}

?>