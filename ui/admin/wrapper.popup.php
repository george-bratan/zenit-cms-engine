
<div style="max-width:1200px;">
	<div class="modal_header">
		<span>{$TITLE}</span>
	</div>
	<div class="modal_content" id="popup" style="overflow-y:auto; overflow-x:hidden; max-height:650px;">

		<div id="alert_container">
			{include 'admin/.shared.alerts.php'}
		</div>

		{$CONTENT}

		<!--br class="clear"-->

	</div>
</div>

<script type="text/javascript">

	$('form.ajax').submit(function(){

		$.fancybox.showActivity();

		$(this).ajaxSubmit({
			success:function(responseText){
				if (responseText && responseText != '<head></head><body></body>') {
					$('#alert_container').html(responseText);
					$.fancybox.hideActivity();
					$.fancybox.resize();
					return;
				}

				$.fancybox.close();
				$.fancybox.showActivity();

				//window.location = '{print Request::$URL}'; //window.location;
				/*
				var loc = '' + window.location;
				if (loc.indexOf('{print Request::$URL}') == -1)
					window.location.reload();
				else
					window.location = '{print Request::$URL}';
				*/
				window.location = window.location;
			},
			error:function(a){
				$.fancybox.hideActivity();

				//if (a.status == 403) {
				if (true) {
					//
					$.fancybox(a.responseText, {
						padding: 0
					});
				}
			}
		});
		return false;
	});
</script>
