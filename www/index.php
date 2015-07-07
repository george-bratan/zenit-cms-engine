<?php

	$_SERVER['SCRIPT_NAME'] = str_replace('/www/', '/', $_SERVER['SCRIPT_NAME']);

	require __DIR__.'/../lib.php';

	require __DIR__.'/../conf.php';
	require __DIR__.'/../routes.php';

	DB::Connect();
	Session::Init();

	Application::Run();

?>