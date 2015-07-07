
<div style="min-width:650px">
	<form action="{print Request::$URL}/filter" class="ajax filters" method="post">

		<div class="left">
			<p>
				<label>Use the filters below to search for matching records.</label>
			</p><br/>

			{if isset($FILTER)}
				{foreach $FILTER as $key => $input}

					{print $input->Render()}

				{/foreach}
			{/if}
		</div>

		{if isset($FIELDS)}
		<div class="right filter-fields">
			<p>
				<label>Select the fields you want displayed in your record list</label>
			</p><br/>

			{if isset($FIELDS)}
				{foreach $FIELDS as $key => $input}

					{print $input->Render()}

				{/foreach}
			{/if}
		</div>
		{/if}

		<br class="clear">

		<p>
			<input type="button" value="Filter" style="float:right" onclick="$('form.filters').submit();"/>
			<input type="button" value="Reset" style="float:right" onclick="$('form.reset').submit();"/>
			<input type="button" value="Close" onclick="$.fancybox.close();"/>
		</p>

	</form>

	<form action="{print Request::$URL}/reset" class="ajax reset" method="post">
	</form>
</div>