
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Order</label></th>
					<th>#{$ORDER.id}</th></tr>
				<tr><td><label>Issued on</label></td>
					<td>{$ORDER.date}</td></tr>
				<tr><td><label>Amount Owed</label></td>
					<td>{print number_format($ORDER.total, 2)} {$CURRENCY}</td></tr>
				<tr><td><label>Amount Paid</label></td>
					<td>{print number_format($ORDER.paid, 2)} {$CURRENCY}</td></tr>
				<tr><td><label>Status</label></td>
					<td><span style="color:{print ($ORDER.status == 0 ? 'blue' : ($ORDER.status == 1 ? 'green' : ($ORDER.status == 2 ? 'black' : 'red')))}">
							{$STATUS[ $ORDER.status ]}</span>
						{foreach $STATUS as $id => $status}
						{if $id != $ORDER.status}
							<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="{$STATUS[ $id ]}" href="{print Request::$URL}/status/{$ORDER.id}" data="status={$id}">
						{/if}
						{/foreach}
						</td></tr>

				<tr><td><label>Recurrence</label></td>
					<td>{$ORDER.recurrence}
						<input class="btn_modal" style="margin-left:10px; float:right;" type="button" value="Subscription" href="{print Request::$URL}/recurrence/{$ORDER.id}">
						</td></tr>

				<!--
				<input class="btn_modal" style="margin-left:10px; float:right;" type="button" value="Setup Recurrence" href="{print Request::$URL}/recurrence/{$ORDER.id}">
				-->

				<tr><td colspan=2>&nbsp;</td></tr>

				<tr><th><label>Client</label></th>
					<th>{$ORDER.CONTACT.fullname}</th></tr>
				<tr><td><label>Email</label></td>
					<td><a href="mailto:{$ORDER.CONTACT.email}">{$ORDER.CONTACT.email}</a></td></tr>
				<tr><td><label>Phone</label></td>
					<td><a href="skype:{$ORDER.CONTACT.phone}">{$ORDER.CONTACT.phone}</a></td></tr>
				<tr><td><label>Address</label></td>
					<td>{$ORDER.CONTACT.postal.street}</td></tr>
				<tr><td><label>City</label></td>
					<td>{$ORDER.CONTACT.postal.city}</td></tr>
				<tr><td><label>Country</label></td>
					<td>{$ORDER.CONTACT.postal.country}</td></tr>
				<tr><td><label>Postcode</label></td>
					<td>{$ORDER.CONTACT.postal.postcode}</td></tr>
				<tr><td colspan=2>
					<input class="btn_link" style="float:right;" type="button" value="Client Details" href="{print Request::$URL}/../../crm/contacts/details/{$CONTACT.id}"></td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

				<tr><th>Transaction Date</th>
					<th>Amount</th>
					<th>Status</th></tr>

				{if !count($ORDER.TRANSACTIONS)}
				<tr><td colspan=3>No transactions found.</td></tr>
				{/if}

				{foreach $ORDER.TRANSACTIONS as $TRANSACTION}
				<tr><td><label>{$TRANSACTION.date}</label></td>
					<td>{print number_format($TRANSACTION.amount, 2)} {$CURRENCY}</td>
					<td><a rel="modal" href="{print Request::$URL}/trx/{$TRANSACTION.id}">
							<span style="color:{if $TRANSACTION.status == 3}red{elseif $TRANSACTION.status == 2}blue{elseif $TRANSACTION.status == 1}green{else}black{/if}">
								{print $TRXSTATUS[ $TRANSACTION.status ]}</span></a>
						{if intval($TRANSACTION.authorized) && !intval($TRANSACTION.captured)}
							<a rel="post" href="{print Request::$URL}/capture/{$TRANSACTION.id}" style="float:right">Capture</a>
						{/if}
						</td></tr>
				{/foreach}

				<tr><td colspan=3>
					<input class="btn_modal" style="float:right;" type="button" value="New Transaction" rel="modal" href="{print Request::$URL}/transaction/{$ORDER.id}">
					</td></tr>

			</table>
		</div>

		<div class="right">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>ID</th><th nowrap>Product</th>
					<th nowrap>Price</th><th>Quantity</th><th>&nbsp;</th></tr>

				{if !count($ORDER.PRODUCTS)}
				<tr><td>&nbsp;</td>
					<td colspan=4>No products found.</td></tr>
				{/if}

				{foreach $ORDER.PRODUCTS as $product}
				<tr><td nowrap>#{$product.idproduct}</td><td style="width:100%">{$product.name}</td>
					<td nowrap style="text-align:center">{print number_format($product.price, 2)} {$CURRENCY}</td><td style="text-align:center">{$product.quantity}</td>
					<td nowrap>
						<input class="btn_status" type="button" value="-" href="{print Request::$URL}/quantity/{$product.id}" data="add=-1">
						<input class="btn_status" type="button" value="+" href="{print Request::$URL}/quantity/{$product.id}" data="add=+1">
					</td></tr>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="Add Product" rel="modal" href="{print Request::$URL}/product/{$ORDER.id}">
					</td></tr>

				<tr><td colspan=5>&nbsp;</td></tr>
			</table>

			<table class="data" style="width:100%;" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Discount</th>
					<th nowrap style="text-align:center">Value</th></tr>

				{if !count($ORDER.DISCOUNTS)}
				<tr><td>&nbsp;</td>
					<td colspan=2>No discounts applied.</td></tr>
				{/if}

				{foreach $ORDER.DISCOUNTS as $discount}
				<tr><td style="width:80%">{$discount.name}</td>
					<td nowrap style="text-align:center">
						{if $discount.type == 0}
							{$discount.value} {$CURRENCY}
						{/if}
						{if $discount.type == 1}
							{$discount.value}%
						{/if}
						</td></tr>
				{/foreach}

				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Discounts" rel="modal" href="{print Request::$URL}/discount/{$ORDER.id}">
					</td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>
			</table>

			<table class="data" style="width:100%;" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Tax</th>
					<th nowrap style="text-align:center">Value</th></tr>

				{if !count($ORDER.TAXES)}
				<tr><td>&nbsp;</td>
					<td colspan=2>No taxes applied.</td></tr>
				{/if}

				{foreach $ORDER.TAXES as $tax}
				<tr><td style="width:80%">{$tax.name}</td>
					<td nowrap style="text-align:center">
						{if $tax.type == 0}
							{$tax.value} {$CURRENCY}
						{/if}
						{if $tax.type == 1}
							{$tax.value}%
						{/if}
						</td></tr>
				{/foreach}

				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Taxes" rel="modal" href="{print Request::$URL}/tax/{$ORDER.id}">
					</td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

				<tr><th>Delivery</th><th>Number</th>
					<th>Status</th><th style="width:80px; text-align:center;">Options</th></tr>

				{if !count($ORDER.DELIVERIES)}
				<tr><td colspan=4>No deliveries found.</td></tr>
				{/if}

				{foreach $ORDER.DELIVERIES as $DELIVERY}
				<tr><td><label>{$DELIVERY.scheduled}</label>
					{if intval($DELIVERY.date)}
						<br/>Sent on {$DELIVERY.date}
					{else}
						<br/><a rel="post" href="{print Request::$URL .'/../deliveries/send/'. $DELIVERY.id}">Mark as Sent</a>
					{/if}</td>
					<td>#{$DELIVERY.number}</td>
					<td><span style="color:{if $DELIVERY.status == 3}red{elseif $DELIVERY.status == 2}blue{elseif $DELIVERY.status == 1}green{else}black{/if}">
						{print $DSTATUS[ $DELIVERY.status ]}</span></td>
					<td nowrap style="text-align:center;">
						<a rel="modal" id="edit_{$address.id}" class="" href="{print Request::$URL .'/../deliveries/details/'. $DELIVERY.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png" alt="Edit Delivery" class="help" title="Edit Delivery"/>
						</a>&nbsp;
						<a rel="modal" id="delete_{$address.id}" class="" href="{print Request::$URL .'/../deliveries/delete/'. $DELIVERY.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png" alt="Delete Delivery" class="help" title="Delete Delivery"/>
						</a>&nbsp;
					</td></tr>
				{/foreach}

				<tr><td colspan=4>
					<input class="btn_modal" style="float:right;" type="button" value="New Delivery" rel="modal" href="{print Request::$URL}/../deliveries/new/{$ORDER.id}">
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
				href: $(this).attr('href'),
				onComplete: function(){

					$("#popup .datepicker").datepicker({
						nextText: '&raquo;',
						prevText: '&laquo;',
						showAnim: 'slideDown',
						dateFormat:"yy-mm-dd",
						firstDay:1
					});
				}
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