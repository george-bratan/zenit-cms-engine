
<table style="float:left; width:300px; margin-bottom:20px;" cellpadding="0" cellspacing="0">
	<tr>
		<td>Licensed to </td>
		<td><strong>{if $LICENSE.company}{$LICENSE.company}{else}<span style="color:red">NO LICENSE</span>{/if}</strong></td>
	</tr>
	<tr>
		<td>Issued on </td>
		<td><strong>{if $LICENSE.issued}{print date('F j, Y', $LICENSE.issued)}{/if}</strong></td>
	</tr>
	<tr>
		<td>Expires on </td>
		<td><strong>{if $LICENSE.expires}{print date('F j, Y', $LICENSE.expires)}{/if}</strong></td>
	</tr>

</table>

<br class="clear"/>
<p>
	If you received a license file please copy the contents below and hit "Save Settings"
</p>
<br class="clear"/>
