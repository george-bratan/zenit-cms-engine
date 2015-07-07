<div style="min-width:400px">

	<table class="data" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>Name</td>
			<td>{$CONTACT.fullname}
				<a href="{print Request::$URL}/details/{$CONTACT.id}" style="float:right">
					<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/user.png" style="float:left; margin-right:5px;" />Details &raquo;</a>
				</td></tr>
		<tr>
			<td>Address</td>
			<td>{if $CONTACT.address}{$CONTACT.address}{else}-{/if}</td></tr>
		<tr>
			<td>Phone</td>
			<td>{if $CONTACT.phone}
				<a href="skype:{$CONTACT.phone}">{$CONTACT.phone}</a>
				<a href="skype:{$CONTACT.phone}" style="float:right" onclick="javascript: setTimeout( function() { $('#note_{$CONTACT.id}').click(); }, 100);">
					<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/phone.sound.png" style="float:left; margin-right:5px;" />Call &raquo;</a>
				{else}-{/if}</td></tr>
		<tr>
			<td>Email</td>
			<td>{if $CONTACT.email}
				<a href="mailto:{$CONTACT.email}">{$CONTACT.email}</a>
				<a href="mailto:{$CONTACT.email}" style="float:right" onclick="javascript: setTimeout( function() { $('#note_{$CONTACT.id}').click(); }, 100);">
					<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/email.png" style="float:left; margin-right:5px;" />Email &raquo;</a>
				{else}-{/if}</td></tr>
	</table>

	<br class="clear">

	<p>
		<input type="button" value="   Quick Note  " style="float:right" onclick="javascript: setTimeout( function() { $('#note_{$CONTACT.id}').click(); }, 100);" />

		<input type="button" value="Cancel" onclick="$.fancybox.close();"/>
	</p>

</div>