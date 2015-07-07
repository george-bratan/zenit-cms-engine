

	<div id="tab_preview" class="tab">
		<iframe src="{print Request::$URL}/template/{print Request::URL('id')}" id="editor_preview" name="editor_preview">
			<p>Your browser does not support iframes.</p>
		</iframe>
	</div>


	<script type="text/javascript">

	$(document).ready(function() {

		$('#btn_more, #btn_hide').click(function(){
			//
			if ($('div.column_left').is('div.column_large')) {
				//
				$('div.column_right').css('display', 'none');
				$('div.column_left').removeClass('column_large').addClass('column_full');

				$('#btn_more').html(' &laquo; More ');
				$.cookie("ADMIN.CMS.TEMPLATES.COLLAPSED", 'TRUE');
			}
			else {
				//
				$('div.column_right').css('display', '');
				$('div.column_left').removeClass('column_full').addClass('column_large');

				$('#btn_more').html(' Hide &raquo; ');
				$.cookie("ADMIN.CMS.TEMPLATES.COLLAPSED", 'FALSE');
			}
		});

		if ($.cookie("ADMIN.CMS.TEMPLATES.COLLAPSED") == 'TRUE') {
			//
			$('#btn_more').click();
		}

		//
		$.fancybox.showActivity();

		$('#editor_preview').load(function(){
			//
			$.fancybox.hideActivity();
			$(this).animate({height: $(this).contents().height()});
		});

		$('#btn_send').click(function(){
			//
			$.fancybox.showActivity();
			$.get($(this).attr('href'), function(data) {
				//
				$.fancybox.hideActivity();
			});

			return false;
		});

	});

	</script>

	<style type="text/css" media="screen">

	#editor_preview {
		margin: 0;
		height:600px;
		position:relative;
	}
	#editor_preview {
		width:100%;
	}
	.column_left .content {
		padding: 0;
	}

	</style>