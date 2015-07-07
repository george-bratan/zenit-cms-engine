
<div style="min-width:600px">
	{if isset($TARGET)}
	<form action="{$TARGET}" class="{if !isset($AJAX)}ajax{/if} save" method="post">
	{else}
	<form action="{print Request::$URL}/save{if isset($ITEM.id)}/{$ITEM.id}{/if}" class="ajax save" method="post">
	{/if}

		{php $right = false}
		{foreach $FIELDS as $key => $input}

			{if $input->align == 'right'}
				{php $right = true}
			{/if}

		{/foreach}


		{if $right}

			<div class="left" style="width:350px">
			{foreach $FIELDS as $key => $input}
				{if $input->align == 'left'}

					{print $input->Render()}
				{/if}
			{/foreach}
			</div>

			<div class="right" style="width:250px">
			{foreach $FIELDS as $key => $input}
				{if $input->align == 'right'}

					{print $input->Render()}
				{/if}
			{/foreach}
			</div>

		{else}

			<p>
			{foreach $FIELDS as $key => $input}

				{print $input->Render()}

			{/foreach}
			</p>

		{/if}

		<br class="clear">

		<p>
			<input type="button" value="   Save   " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>