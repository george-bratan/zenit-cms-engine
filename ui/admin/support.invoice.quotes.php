
<div style="min-width:600px">
	<form action="{$TARGET}" class="ajax save" method="post">

		<div style="max-height:400px; overflow-y:auto;">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th style="width:10px"><input type="checkbox" id="check_all_quotes" name="check_all_quotes" style="margin:0" /></th>
					<th>Ticket</th>
					<th>Quote</th>
					<th style="width:80px">Status</th></tr>

				{if !count($QUOTES)}
				<tr><td>&nbsp;</td><td colspan=4>No Quotes Found</td></tr>
				{/if}

				{foreach $QUOTES as $quote}
				<tr><td><input class="quote" id="item_{$quote.id}" type="checkbox" name="items[]" value="{$quote.id}" style="margin:0" /></td>
					<td>
						<strong>{$quote.ticket}</strong><br />
						{$quote.details}</td>
					<td>{$quote.time}</td>
					<td nowrap>
						{if $quote.status}
							{print $quote.status == 1 ? '<span style="color:green">Accepted</span>' : '<span style="color:red">Rejected</span>'}
						{else}
							<span style="color:blue">Pending</span>
						{/if}
					</td></tr>
				{/foreach}

			</table>
		</div>

		<br class="clear">

		<p>
			<input type="button" value="  Add Quotes  " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>


<script type="text/javascript">

	$(document).ready(function(){

		$('#check_all_quotes').click(function(){
			if($(this).is(':checked'))
			{
				$('form.save input:checkbox').each(function(){
					//
					$(this).attr('checked', true);
					$(this).parents('tr:first').addClass('hover');
				});
			}
			else
			{
				$('form.save input:checkbox').each(function(){
					//
					$(this).attr('checked', false);
					$(this).parents('tr:first').removeClass('hover');
				});
			}
		});

	});

	</script>