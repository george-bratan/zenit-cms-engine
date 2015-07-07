
<div style="min-width:600px">
	{if isset($TARGET)}
	<form action="{$TARGET}" class="ajax save" method="post">
	{else}
	<form action="{print Request::$URL}/save{if isset($ITEM.id)}/{$ITEM.id}{/if}" class="ajax save" method="post">
	{/if}

		<div class="left" style="width:350px; vertical-align:top;">
		{foreach $FIELDS as $key => $input}

			{print $input->Render()}

		{/foreach}
		</div>

		<div class="right" style="width:250px">
			<label>Internal Page</label><br>
			<select multiple="true" style="width:250px; height:130px;" onchange="javascript: $('#url').val( $(this).val() );">
			{foreach $PAGES as $PAGE}
				<option value="{$PAGE.url}">{$PAGE.name}</option>
			{/foreach}
			</select>
		</div>

		<br class="clear"><br/>

		<p>
			<input type="button" value="   Save   " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>