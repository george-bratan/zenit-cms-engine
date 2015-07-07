
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Number</label></th>
					<th>#{$TICKET.id}</th></tr>
				<tr><td><label>Subject</label></td>
					<td>{$TICKET.subject}</td></tr>
				<tr><td><label>Type</label></td>
					<td>{$TICKET.type}</td></tr>
				<tr><td><label>Issued on</label></td>
					<td>{$TICKET.issued}</td></tr>
				<tr><td><label>Issued by</label></td>
					<td>{$TICKET.user} ({$TICKET.company})</td></tr>
				<tr><td><label>Priority</label></td>
					<td>{if $TICKET.priority}{if $TICKET.priority == 1}<span style="color:green">{else}<span style="color:red">{/if}{else}<span>{/if}{$PRIORITY[ $TICKET.priority ]}</span>
						<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="Increase" href="{print Request::$URL}/priority/{$TICKET.id}" data="priority=increase">
						<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="Decrease" href="{print Request::$URL}/priority/{$TICKET.id}" data="priority=decrease">
						<!--
						{foreach $PRIORITY as $id => $status}
							<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="{$PRIORITY[ $id ]}" href="{print Request::$URL}/priority/{$TICKET.id}" data="priority={$id}">
						{/foreach}
						-->
						</td></tr>
				<tr><td><label>Companies</label></td>
					<td>{$TICKET.companynames}
						<input class="btn_modal" style="float:right;" type="button" value="Invite" rel="modal" href="{print Request::$URL}/invite/{$TICKET.id}"></td></tr>
				{if intval($TICKET.solved)}
				<tr><td><label>Solved on</label></td>
					<td>{$TICKET.solved}</td></tr>
				{/if}
				{if intval($TICKET.closed)}
				<tr><td><label>Closed on</label></td>
					<td>{$TICKET.closed}</td></tr>
				{/if}
				<tr><td><label>Delivery on</label></td>
					<td><input type="text" class="datepicker" style="width:200px" id="delivery" name="delivery" value="{if intval($TICKET.delivery)}{$TICKET.delivery}{/if}" />
						<input class="btn_status" style="float:right;" type="button" value="Update" href="{print Request::$URL}/delivery/{$TICKET.id}" serialize="delivery"></td></tr>
				<tr><td><label>Status</label></td>
					<td>{$STATUS[ $TICKET.status ]}
						{foreach $STATUS as $id => $status}
						{if $id != $TICKET.status}
							<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="{$STATUS[ $id ]}" href="{print Request::$URL}/status/{$TICKET.id}" data="status={$id}">
						{/if}
						{/foreach}
						</td></tr>
				<tr><td colspan=2>
					{print Util::Links($TICKET.details)}</td></tr>
				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Attach File" rel="modal" href="{print Request::$URL}/file/{$TICKET.id}">
					{if count($TICKET.files)}
					<ul style="list-style:none;">
					{foreach $TICKET.files as $file}
						<li>{$file.original} ({$file.size} bytes) &nbsp; - &nbsp; <a href="{print Request::$URL}/download/{$file.id}">Download</a></li>
					{/foreach}
					</ul>
					{/if}
					</td></tr>
			</table>
		</div>

		<div class="right">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>From</th><th nowrap>To</th>
					<th nowrap>Quote</th><th style="width:100%">Details</th><th nowrap>Status</th></tr>

				{foreach $QUOTES as $quote}
				<tr><td nowrap>{$quote.from}</td><td nowrap>{$quote.to}</td>
					<td nowrap>{$quote.amount}</td><td>{$quote.details}</td>
					<td nowrap>
						{if !$quote.status && $quote.idto == Session::Get('SUPPORT.COMPANY.ID')}
							<input class="btn_status" type="button" value="Accept" href="{print Request::$URL}/accept/{$quote.id}" data="status=1">
							<input class="btn_status" type="button" value="Reject" href="{print Request::$URL}/accept/{$quote.id}" data="status=2">
						{else}
							<span style="color:{if $quote.status == 1}green{else}black{/if}">{$QSTATUS[ $quote.status ]}</span>
						{/if}
					</td></tr>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="New Quote" rel="modal" href="{print Request::$URL}/quote/{$TICKET.id}">
					</td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>User</th><th nowrap>Time</th>
					<th style="width:100%">Details</th></tr>

				{foreach $TIMES as $time}
				<tr><td nowrap>{$time.user}</td><td nowrap>{$time.amount}</td>
					<td>{$time.details}</td></tr>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="New Time Entry" rel="modal" href="{print Request::$URL}/time/{$TICKET.id}">
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