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

	<div class="modal_header">
		<span>{$TITLE}</span>
	</div>
	<div class="modal_content" id="popup" style="overflow-y:auto; overflow-x:hidden; max-height:650px;">

		<div id="alert_container">
			{include 'admin/.shared.alerts.php'}
		</div>

		{$CONTENT}

		<br class="clear">

	</div>

</body>
</html>
