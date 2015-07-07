

{foreach $SOCIAL as $id => $SERVICE}
{php $SERVICE.id = isset($SERVICE.id) ? $SERVICE.id : $id}
<table class="data" style="float:left; margin-right:15px; width:100%; margin-bottom:20px;" cellpadding="0" cellspacing="0">
	<tr>
		<th colspan=2>{$SERVICE.name}</th>
	</tr>

	<tr>
		<td style="width:200px;">{$SERVICE.name} Account</td>
		<td><strong>{print isset($SERVICE.account) ? $SERVICE.account : '-'}</strong>{if isset($SERVICE.profile)} - <a target="_blank" href="{$SERVICE.profile}">Profile &raquo;</a>{/if}</td>
	</tr>
	<tr>
		<td style="width:200px;">{$SERVICE.name} Token</td>
		<td><strong style="font-family:monospace">{print isset($SERVICE.token) ? implode('<br />', str_split($SERVICE.token, 100)) : '-'}</strong></td>
	</tr>

	{if isset($SERVICE.options)}
	<tr>
		<td style="width:200px;">&nbsp;</td>
		<td>
			<select id="option_{$SERVICE.id}" name="VALUES[{$SERVICE.id}][option]" style="width:300px">
			{foreach $SERVICE.options as $id => $option}
				<option value="{$id}" {if $id == $SERVICE.value}selected{/if}>{$option}</option>
			{/foreach}
			</select>
			<input rel="post" serialize="#option_{$SERVICE.id}" href="{print Request::$URL}/{$SERVICE.id}/option" type="button" value="Update" style="margin-left:15px;">
		</td>
	</tr>
	{/if}

	<tr>
		<td>&nbsp;</td>
		<td>
			{if isset($SERVICE.account)}
			<input rel="modal" href="{print Request::$URL}/{$SERVICE.id}/signout" type="button" value="Sign out of {$SERVICE.name}">
			{else}
			<input rel="popup" href="{print Request::$URL}/{$SERVICE.id}/signin" popup-width="850" popup-height="600" type="button" value="Sign in to {$SERVICE.name}">
			{/if}
		</td>
	</tr>

</table>
{/foreach}

<br class="clear"/>
<p>

</p>
<br class="clear"/>
