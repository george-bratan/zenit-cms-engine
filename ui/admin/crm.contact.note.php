

<div style="min-width:600px">

	<table class="data" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px">
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
				<a href="skype:{$CONTACT.phone}" style="float:right">
					<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/phone.sound.png" style="float:left; margin-right:5px;" />Call &raquo;</a>
				{else}-{/if}</td></tr>
		<tr>
			<td>Email</td>
			<td>{if $CONTACT.email}
				<a href="mailto:{$CONTACT.email}">{$CONTACT.email}</a>
				<a href="mailto:{$CONTACT.email}" style="float:right">
					<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/email.png" style="float:left; margin-right:5px;" />Email &raquo;</a>
				{else}-{/if}</td></tr>
	</table>


	<form action="{print Request::$URL}/note/{$CONTACT.id}" class="ajax save" method="post">

		<div class="left" style="width:350px">
		{foreach $FIELDS as $key => $input}
			{if !in_array($key, array('flags', 'content'))}
				{print $input->Render()}
			{/if}
		{/foreach}
		</div>

		<div class="right" style="width:200px">

			{print $FIELDS['flags']->Render()}

		</div>

		<br class="clear">

		<p>
			{print $FIELDS['content']->Render()}
		</p>

		<br class="clear">

		<p>
			<input type="button" value="Save" style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="Cancel" onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>