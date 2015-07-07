
<div style="min-width:300px">
	<form action="{print Request::$URL}/delete" class="ajax delete" method="post">

		<p>
			{if isset($CONTENT)}{$CONTENT}{/if}

			{if isset($RECORDS)}
				<ul class="normal">
				{foreach $RECORDS as $id => $name}
					<li>{$name}
						<input type="hidden" name="items[]" value="{$id}">
					</li>
				{/foreach}
				</ul>
			{/if}
		</p>

		<br class="clear">

		<p>
			{if isset($CONTENT)}
				<input type="button" value="Delete" style="float:right" onclick="$('form.delete').submit();" />
				<input type="button" value="Cancel" onclick="$.fancybox.close();" />
			{else}
				<input type="button" value="     OK     " style="float:right" onclick="$.fancybox.close();" />
			{/if}
		</p>

	</form>
</div>