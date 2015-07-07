<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

	<!-- Website Title -->
	<title>{print Conf::Get('APP:NAME')} {if isset($MODULE)}| {$MODULE}{/if}</title>

	{include 'admin/.shared.meta.php'}

	{include 'admin/.shared.includes.php'}

</head>
<body>
	<input type="hidden" name="ROOT" id="ROOT" value="{$CONF.WWW.ROOT}">
	<input type="hidden" name="UPLOAD" id="UPLOAD" value="{$CONF.WWW.UPLOAD}">
	<input type="hidden" name="REQUEST" id="REQUEST" value="{print Request::$URL}">

	<div class="content_wrapper">

	<!-- Begin header -->
	<div id="header">
		<div id="logo">
			<img src="{$CONF.WWW.ROOT}/admin/images/logo.zenit.png" alt="logo" style="width:160px; margin-top:-6px;" />
		</div>
		<!--
		<div id="search">
			<form action="{$CONF.WWW.ROOT}{print Admin::$ROOT}/search" id="search_form" name="search_form" method="get">
				<input type="text" id="q" name="q" title="Search" class="search noshadow"/>
			</form>
		</div>
		-->
		<div id="account_info">
			<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/online.png" alt="Online" class="mid_align"/>
			Hello <a href="{$CONF.WWW.ROOT}{print Admin::$ROOT}/index">{print Session::Exists('ACCOUNT.NAME') ? Session::Get('ACCOUNT.NAME') : Session::Get('SUPPORT.NAME');}</a> <!--(<a href="{0}">{0}</a>)-->
			| <a href="{$CONF.WWW.ROOT}{print Admin::$ROOT}/settings">Settings</a>
			| <a href="{$CONF.WWW.ROOT}{print Admin::$ROOT}/logout">Logout</a>
		</div>
	</div>
	<!-- End header -->


	<!-- Begin left panel -->
	<a href="javascript:;" id="show_menu">&raquo;</a>
	<div id="left_menu">
		<a href="javascript:;" id="hide_menu">&laquo;</a>

		{include 'admin/.shared.menu.php'}

		<br class="clear"/>

		<!-- Begin left panel calendar -->
		<div id="calendar"
			action="{$CONF.WWW.ROOT}{print Admin::$ROOT}/index?FILTER[date]="></div>
		<!-- End left panel calendar -->

	</div>
	<!-- End left panel -->


	<!-- Begin content -->
	{$CONTENT}
	<!-- End content -->

	</div>
</body>
</html>
