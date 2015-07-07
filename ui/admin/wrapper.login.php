<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

	<!-- Website Title -->
	<title>WeAdmin | Login</title>

	{include 'admin/.shared.meta.php'}

	{include 'admin/.shared.includes.php'}

	<!--[if IE]>
		<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<![endif]-->

	<script type="text/javascript" charset="utf-8">
	$(function(){
	    // find all the input elements with title attributes
	    //$('input[title!=""]').hint();
	    $('#login_info').click(function(){
			$(this).fadeOut('fast');
		});
	});
	</script>
</head>
<body class="login">

	<!-- Begin login window -->
	<div id="login_wrapper">

		{if isset($ERROR)}
		<div id="login_info" class="alert_warning noshadow" style="width:350px;margin:auto;padding:auto;">
			<p>{$ERROR}</p>
		</div>
		{/if}

		<br class="clear"/>
		<div id="login_top_window">
			<img src="{$CONF.WWW.ROOT}/admin/images/theme.blue/top_login_window.png" alt="top window"/>
		</div>

		<!-- Begin content -->
		{$CONTENT}
		<!-- End content -->

		<div id="login_footer_window">
			<img src="{$CONF.WWW.ROOT}/admin/images/theme.blue/footer_login_window.png" alt="footer window"/>
		</div>
		<div id="login_reflect">
			<img src="{$CONF.WWW.ROOT}/admin/images/theme.blue/reflect.png" alt="window reflect"/>
		</div>

		<div id="content">
			<div class="inner">
			</div>
		</div>

	</div>
	<!-- End login window -->

</body>
</html>
