
<div style="min-width:300px">
	<form action="{$TARGET}" class="ajax select" method="post">

		<p>
			{if isset($CONTENT)}{$CONTENT}{/if}

			{if !isset($SELECTED)}
				{php $SELECTED = Array()}
			{/if}

			{if isset($RECORDS)}
				<ul class="normal" style="list-style:none">
				{foreach $RECORDS as $record}
					<li>
						<input type="checkbox" id="item_{$record.id}" name="items[]" value="{$record.id}" {if in_array($record.id, $SELECTED)}checked{/if}>
						<label for="item_{$record.id}">{$record.name}</label>
					</li>
				{/foreach}
				</ul>
			{/if}
		</p>

		<br class="clear">

		<p>
			<input type="button" value="     OK     " style="float:right" onclick="$('form.select').submit();" />
			<input type="button" value="Cancel" onclick="$.fancybox.close();" />
		</p>

	</form>
</div>