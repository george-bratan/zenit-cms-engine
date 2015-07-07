
<div style="min-width:600px">
	{if isset($TARGET)}
	<form action="{$TARGET}" class="ajax confirm" method="post">
	{else}
	<form action="{print Request::$URL}/confirm" class="ajax save" method="post">
	{/if}

		<p>
			{$CONTENT}
		</p>

		<br class="clear">

		<p>
			<input type="button" value="Confirm" style="float:right" onclick="$('form.confirm').submit();"/>
			<input type="button" value="Cancel" onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>