
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="confirm" value="true"/>

		<table style="float:left;">
		<tr><td>

			<table style="width:320px; margin-bottom:15px;">
			<tr><td style="border-bottom:1px solid #CCC; padding-bottom:4px;">
				<input type="button" name="open" value="{if $STATUS == 'OPEN'}Close Website{else}Open Website{/if}" class="btn_modal" style="float:right" href="{print Request::$URL}/status" />
				<label style="line-height:26px;">Website is {if $STATUS == 'OPEN'}<span style="color:green">OPEN{else}</span><span style="color:red">CLOSED</span>{/if}</label>
			</td></tr>
			</table>

			{foreach $SETTINGS as $input}

				{print $input->Render()}

			{/foreach}

		</td><td style="padding-left:30px;">

			<!--
			<table class="data" style="width:100%; margin-left:0px; margin-bottom:20px;" cellpadding="0" cellspacing="0">
				<tr>
					<th colspan=2>Service Status</th>
				</tr>
				<tr><td><label>Website is</label></td>
					<td style="padding-right:15px; text-align:right;"><label>{if $STATUS == 'OPEN'}<span style="color:green">OPEN</span>{else}<span style="color:red">CLOSED</span>{/if}</label></td>
				</tr>
				<tr><td colspan=2 style="text-align:right">
					<input type="button" name="open" value="{if $STATUS == 'OPEN'}Close Website{else}Open Website{/if}" class="btn_modal" href="{print Request::$URL}/status" />
				</td></tr>
			</table>
			-->

			<table class="data" style="margin-left:0px; width:100%; margin-bottom:20px;" cellpadding="0" cellspacing="0">
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

				{if isset($SERVICE.account)}
				<tr>
					<td style="width:200px;">Profile Feed</td>
					<td>
						<select name="SETTINGS[cms.analytics.feed]" style="width:100%">
						{foreach $SERVICE.feeds as $id => $name}
							<option value="{$id}" {if $id == $SERVICE.feed}selected{/if}>{$name}</option>
						{/foreach}
						</select>
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

		</td></tr>
		</table>

		<br class="clear"/><br/>
		<p>
			<input type="submit" name="save" value="Save Settings" onclick="javascript:$('#form_data').submit();" />
		</p>

	</form>

	<script type="text/javascript">

		$('.btn_modal').click(function(){

 			$.fancybox({
				padding: 0,
				titleShow: false,
				overlayColor: '#333333',
				overlayOpacity: .5,
				showNavArrows: false,
				disableNavButtons: false,
				href: $(this).attr('href')
			});

 		});

 	</script>
