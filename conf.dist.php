<?php

	Conf::Set('APP:NAME', 'Zenit Systems - Demo');
	Conf::Set('APP:VERSION', '1.0');

	Conf::set('APP:ROOT', Util::fixslashes(__DIR__).'/');
	Conf::set('WWW:ROOT', Util::fixslashes(preg_replace('/\/[^\/]+$/', '', $_SERVER['SCRIPT_NAME'])));

	//print Conf::get('WWW:ROOT'); die();
	//Conf::set('WWW:ROOT', '/zenit.support.tmp');

	// framework files
	Conf::set('APP:LIBRARY', Util::fixslashes(__DIR__).'/');

	// on-the-fly autoload classes
	Conf::set('APP:AUTOLOAD',
		Util::fixslashes(realpath(__DIR__)).'/autoload/'.
		'|'.
		Util::fixslashes(realpath(__DIR__)).'/autoload/models/'.
		'|'.
		Util::fixslashes(realpath(__DIR__)).'/autoload/controllers/'.
		'|'.
		Util::fixslashes(realpath(__DIR__)).'/autoload/controllers/admin/'.
		''
	);

	// cache location, will be created if not present
	Conf::set('APP:CACHE', Util::fixslashes(__DIR__).'/.cache/');

	// temporary location, will be created if not present
	Conf::set('APP:TMP', Util::fixslashes(__DIR__).'/.tmp/');

	// location for UI template files
	Conf::set('APP:UI', Util::fixslashes(__DIR__).'/ui/');

	// upload path
	Conf::set('APP:UPLOAD', Util::fixslashes(__DIR__).'/uploads/');

	// timezone
	date_default_timezone_set('GMT');


    // Database
	Conf::set('DB:DSN', 'mysql:host=localhost;port=3306;dbname=zenit');
	Conf::set('DB:USER', 'root');
	Conf::set('DB:PASS', '');

	// Debug
	Conf::set('DEBUG:LEVEL', 3);

	// disable caching
	Conf::set('APP:CACHE', FALSE);

	// twitter app keys
	Conf::set('SOCIAL:TWITTER:KEY', '');
	Conf::set('SOCIAL:TWITTER:SECRET', '');

	// facebook app keys
	Conf::set('SOCIAL:FACEBOOK:KEY', '');
	Conf::set('SOCIAL:FACEBOOK:SECRET', '');

	// google maps keys
	Conf::set('MAPS:KEY', '');
	Conf::set('MAPS:KEY', '');

?>