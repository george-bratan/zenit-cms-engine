
	<form id="bulk" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left" style="width:49%">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th style="width:10px"><input type="checkbox" id="check_all" name="check_all" style="margin:0" /></th>
					<th>Ticket</th>
					<th>Quote</th>
					<th style="width:80px">Status</th>
					<th style="width:100px; min-width:100px; text-align:center;">Options</th></tr>

				{if !count($INVOICE.QUOTES)}
				<tr><td>&nbsp;</td><td colspan=4>No Quotes Found</td></tr>
				{/if}

				{php $minutes = 0}

				{foreach $INVOICE.QUOTES as $quote}
				{php $minutes += intval($quote.hours)*60 + intval($quote.minutes)}
				<tr><td><input class="quote" id="item_{$quote.id}" type="checkbox" name="items[]" value="{$quote.id}" style="margin:0" /></td>
					<td>
						<strong>{$quote.ticket}</strong><br />
						{$quote.details}</td>
					<td>{$quote.time}</td>
					<td nowrap>
						{if $quote.status}
							{print $quote.status == 1 ? '<span style="color:green">Accepted</span>' : '<span style="color:red">Rejected</span>'}
						{else}
							<span style="color:blue">Pending</span>
						{/if}
					</td>
					<td nowrap style="text-align:center;">
						<a rel="post" id="remove_{$quote.id}" class="" href="{print Request::$URL .'/remove/'. $INVOICE.id}" data="items[]={$quote.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png" alt="Remove Quote" class="help" title="Remove Quote"/>
						</a>&nbsp;
					</td></tr>
				{/foreach}

				<tr><td colspan=2>
						<input rel="post" style="float:left; margin-right:5px;" type="button" value="Remove" serialize=".quote" href="{print Request::$URL}/remove/{$INVOICE.id}">
					<td nowrap>
						<strong>{print intval($minutes / 60)}:{print intval($minutes % 60)}</strong></td>
					<td colspan=2>
						<input rel="modal" style="float:right;" type="button" value="Add Quote" rel="modal" href="{print Request::$URL}/add/{$INVOICE.id}"></td></tr>
			</table>
		</div>

		<div class="right" style="width:49%">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Invoice</th>
					<th nowrap>#{$INVOICE.name}</th></tr>

				<tr><td nowrap>Sent to</td>
					<td nowrap>{$INVOICE.to}</td></tr>
				<tr><td nowrap>On</td>
					<td nowrap>{$INVOICE.date}</td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>

				<tr><td nowrap>Start Date</td>
					<td nowrap>{$INVOICE.start}</td></tr>
				<tr><td nowrap>End Date</td>
					<td nowrap>{$INVOICE.end}</td></tr>
				<tr><td nowrap>Quotes Totaling</td>
					<td nowrap>{print intval($minutes / 60)}:{print intval($minutes % 60)} minutes</td></tr>

				<tr><td colspan=2>
					<input rel="modal" style="float:right;" type="button" value="Edit Information" rel="modal" href="{print Request::$URL}/edit/{$INVOICE.id}">
					</td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>

				<tr><td nowrap>Status</td>
					<td nowrap>
						{if $INVOICE.status}
							<input rel="post" style="margin-left:10px; float:right;" type="button" value="Pending" href="{print Request::$URL}/status/{$INVOICE.id}" data="status=0">
						{else}
							<input rel="post" style="margin-left:10px; float:right;" type="button" value="Paid" href="{print Request::$URL}/status/{$INVOICE.id}" data="status=1">
						{/if}
						{print $INVOICE.status ? '<span style="color:green">Paid</span>' : '<span style="color:blue">Pending</span>'}

						</td></tr>

				<tr><td nowrap>File</td>
					<td>
						<input rel="modal" style="float:right;" type="button" value="Attach Invoice File" href="{print Request::$URL}/file/{$INVOICE.id}">

						{if $INVOICE.file}
							<a href="{print Request::$URL}/download/{$INVOICE.id}">{$INVOICE.file}</a>
						{else}
							<em>No file uploaded</em>
						{/if}
						</td></tr>
			</table>
		</div>

		<br class="clear">

	</form>

	<script type="text/javascript">

 	$(document).ready(function(){

 		//

 	});

 	</script>