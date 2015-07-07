
<div style="min-width:550px;">

	<p>
		{if isset($CONTENT)}{$CONTENT}{/if}
	</p>

	<br class="clear">

	<p>
		<input type="button" value="     OK     " style="float:right; margin-right: 15px;"
			onclick="{if isset($RELOAD)}{if $RELOAD}window.location = window.location;{/if}{/if} $.fancybox.close();" />
	</p>

</div>