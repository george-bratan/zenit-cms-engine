
<div style="min-width:600px">
	{if isset($TARGET)}
	<form action="{$TARGET}" class="ajax save" method="post">
	{else}
	<form action="{print Request::$URL}/links{if isset($ITEM.id)}/{$ITEM.id}{/if}" class="ajax save" method="post">
	{/if}

		<table>
			<tr><th nowrap><label>Title</label></th>
				<th>&nbsp;</th>
				<th style="width:100%"><label>URL</label></th></tr>

			{for $i = 0; $i < 5; $i++}
				<tr><td nowrap><input type="text" name="LINKS[title][]" value="{if isset($LINKS[ $i ])}{$LINKS[$i].title}{/if}" style="width:250px;"></td>
					<td nowrap>&nbsp; &raquo; &nbsp;</td>
					<td style="width:100%"><input type="text" name="LINKS[url][]" value="{if isset($LINKS[ $i ])}{$LINKS[$i].url}{/if}" style="width:300px;"></td></tr>
			{/for}
		</table>

		<br class="clear">

		<p>
			<input type="button" value="   Save   " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>