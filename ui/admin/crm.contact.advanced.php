
<div style="min-width:1115px">
	<form action="{print Request::$URL}/advanced" class="ajax filters" method="post">

		<div class="left" style="width:25%">
			<p>
				<label>Match records by contact details:</label>
			</p><br/>

			{if isset($FILTER.CONTACTS)}
				{foreach $FILTER.CONTACTS as $key => $input}

					{print $input->Render()}

				{/foreach}
			{/if}
		</div>

		<div class="left" style="width:25%">
			<p>
				<label>Having been contacted:</label>
			</p><br/>

			{if isset($FILTER.ANYNOTES)}
				{foreach $FILTER.ANYNOTES as $key => $input}

					{print $input->Render()}

				{/foreach}
			{/if}
		</div>

		<div class="left" style="width:25%">
			<p>
				<label>Having NOT been contacted:</label>
			</p><br/>

			{if isset($FILTER.NOTNOTES)}
				{foreach $FILTER.NOTNOTES as $key => $input}

					{print $input->Render()}

				{/foreach}
			{/if}
		</div>

		<div class="left" style="width:25%">
			<p>
				<label>With their last contact:</label>
			</p><br/>

			{if isset($FILTER.LASTNOTE)}
				{foreach $FILTER.LASTNOTE as $key => $input}

					{print $input->Render()}

				{/foreach}
			{/if}
		</div>



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