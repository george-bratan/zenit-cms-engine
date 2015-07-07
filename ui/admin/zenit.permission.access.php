
<div style="min-width:600px">
	<form action="{print Request::$URL}/save/{$USER.id}" class="ajax save" method="post">


		<table width="100%" cellpadding=0 cellspacing=0><tr>
		{php $last = ''}
		{foreach $ACCESS as $title => $option}

			{php $new = strpos($title, ':') ? substr($title, 0, strpos($title, ':')) : $title}
			{if $new != $last}
				<tr style="">
			{/if}
			{php $last = $new}

			<td nowrap style="padding:0px 10px; border-top:1px solid #CCC;">
				<label style="padding-top:10px; display:block;">{$title}</label>

				{foreach $option as $key => $suboption}
					<input type="hidden" name="VALUES[token][{$key}]" value="false"/>
					<input type="checkbox" name="VALUES[token][{$key}]" id="VALUES[token][{$key}]" value="true" {if in_array($key, $TOKEN)}checked{/if} />
					<label for="VALUES[token][{$key}]" style="margin-right:20px;">{$suboption}</label>
					<br />
				{/foreach}
			</td>

		{/foreach}
		</tr></table>

		<br class="clear">

		<p>
			<input type="button" value="   Save   " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>