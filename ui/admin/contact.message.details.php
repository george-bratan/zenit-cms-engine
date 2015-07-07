
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Message</label></th>
					<th>#{$MESSAGE.id}</th></tr>

				<tr><td><label>Contact Name</label></td>
					<td>{$MESSAGE.name}</td></tr>
				<tr><td><label>Contact Email</label></td>
					<td><a href="mailto:{$MESSAGE.email}">{$MESSAGE.email}</a></td></tr>
				<tr><td><label>Phone</label></td>
					<td>{if $MESSAGE.phone} <a href="skype:{$MESSAGE.phone}">{$MESSAGE.phone}</a> {else} - {/if}</td></tr>
				<tr><td><label>Issued on</label></td>
					<td>{$MESSAGE.date}</td></tr>

				<tr><td><label>Flags</label></td>
					<td>
						{foreach $MESSAGE.FLAGS as $flag}
							<img src="{print Conf::Get('WWW:ROOT')}/admin/images/icon.small/flag.{$flag.color}.png" />
						{/foreach}
						</td></tr>
				<tr><td><label>Unique Code</label></td>
					<td>{$MESSAGE.uid}</td></tr>

				<tr><td><label>Status</label></td>
					<td><span style="color:{print ($MESSAGE.status == 0 ? 'red' : ($MESSAGE.status == 1 ? 'green' : 'black'))}">
							{$STATUS[ $MESSAGE.status ]}</span>
							{foreach $STATUS as $id => $status}
							{if $id != $MESSAGE.status}
								<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="{$STATUS[ $id ]}" href="{print Request::$URL}/status/{$MESSAGE.id}" data="status={$id}">
							{/if}
							{/foreach}
						</td></tr>

			</table>
		</div>

		<div class="right">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Subject</th><th nowrap>{$MESSAGE.subject}</th></tr>

				<tr><td colspan=2>{if count($MESSAGE.COMMENTS)}{$MESSAGE.COMMENTS[0].content}{else} - {/if}</td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>
			</table>

			<table class="data" style="width:100%;" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Flag</th>
					<th nowrap style="text-align:center">Flag Name</th></tr>

				{if !count($MESSAGE.FLAGS)}
				<tr><td>&nbsp;</td>
					<td colspan=2>Not flagged.</td></tr>
				{/if}

				{foreach $MESSAGE.FLAGS as $flag}
				<tr><td style="width:10px">
						<img src="{print Conf::Get('WWW:ROOT')}/admin/images/icon.small/flag.{$flag.color}.png" /></td>
					<td nowrap>
						{$flag.name}</td></tr>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="Flags" rel="modal" href="{print Request::$URL}/flags/{$MESSAGE.id}">
						</td></tr>
			</table>
		</div>

		<br class="clear">

	</form>

	<script type="text/javascript">

 	$(document).ready(function(){

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

 		$('.btn_link').click(function(){

 			$.fancybox.showActivity();
 			window.location = $(this).attr('href');

 		});

 		$('.btn_status').click(function(){

 			$.fancybox.showActivity();

 			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				data: $(this).attr('data') ? $(this).attr('data') : ($(this).attr('serialize') ? $('#' + $(this).attr('serialize')).serialize() : ''),
				success: function(responseText){
					if (responseText) {
						//
						$.fancybox(responseText, {
							padding: 0
						});
					}
					else {
						//window.location.reload();
						window.location = window.location;
					}
				},
				error: function(a){
					$.fancybox.hideActivity();

					//if (a.status == 403) {
					if (true) {
						//
						$.fancybox(a.responseText, {
							padding: 0
						});
					}
				}
			});

 		});

 	});

 	</script>