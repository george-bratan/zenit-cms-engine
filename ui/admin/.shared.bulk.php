

{if (isset($BULK))}

		<!-- Begin bulk options -->
		<div class="bulk">
			<select name="bulk">
				<option value="">Bulk ...</option>

				{foreach $BULK as $key => $title}
					<option value="{$key}">{$title}</option>
				{/foreach}

			</select>
			<input type="button" value="Apply">
		</div>
		<!-- End bulk options -->


	<script type="text/javascript">
	$('form#bulk input[type=button]').click(function(){
		$('form#bulk').submit();
	});

	$('form#bulk').submit(function(){

		$.fancybox.showActivity();

		$(this).ajaxSubmit({
			success:function(responseText){
				if (responseText) {
					//
					$.fancybox(responseText, {
						padding: 0
					});
				}
				else {
					//window.location.reload();
					window.location = window.location;
				}
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

{/if}