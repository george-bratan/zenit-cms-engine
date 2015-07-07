
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Contact</label></th>
					<th>{$CONTACT.fullname}</th></tr>
				<tr><td><label>Labels</label></td>
					<td>
						{foreach $CONTACT.LABELS as $label}
							<span style="float:left; margin-right:10px; display:block; width:12px; height:12px; border:1px solid black; background-color:#{$label.color};"></span>
						{/foreach}
						</td></tr>
				<tr><td><label>Company</label></td>
					<td>{$CONTACT.COMPANY.name}</td></tr>
				<tr><td><label>Position</label></td>
					<td>{$CONTACT.position}</td></tr>
				<tr><td><label>Email</label></td>
					<td><a href="mailto:{$CONTACT.email}" onclick="javascript: setTimeout( function() { $('#note').click(); }, 100);">{$CONTACT.email}</a></td></tr>
				<tr><td><label>Phone</label></td>
					<td><a href="skype:{$CONTACT.phone}" onclick="javascript: setTimeout( function() { $('#note').click(); }, 100);">{$CONTACT.phone}</a></td></tr>
				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Edit Details" rel="modal" href="{print Request::$URL}/edit/{$CONTACT.id}"></td></tr>

				<tr><td><label>Postal Address</label></td>
					<td>{if $CONTACT.postal.name}{$CONTACT.postal.name}{else}-{/if}</td></tr>
				<tr><td><label>Billing Address</label></td>
					<td>{if $CONTACT.billing.name}{$CONTACT.billing.name}{else}-{/if}</td></tr>
				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Setup Addresses" rel="modal" href="{print Request::$URL}/addresses/{$CONTACT.id}"></td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th>Address</th>
					<th style="width:100px; text-align:center;">Options</th></tr>

				{if !count($CONTACT.ADDRESSES)}
					<tr><td colspan=2>No Addresses Defined</td></tr>
				{/if}

				{foreach $CONTACT.ADDRESSES as $address}
				<tr><td>
					{$address.street}<br />
					{$address.city}, {$address.state}, {print $CONF.COUNTRIES[ $address.country ]}, {$address.postcode}</td>
					<td nowrap style="text-align:center;">
						<a rel="modal" id="edit_{$address.id}" class="" href="{print Request::$URL .'/address/details/'. $address.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png" alt="Edit Address" class="help" title="Edit Address"/>
						</a>&nbsp;
						<a rel="modal" id="delete_{$address.id}" class="" href="{print Request::$URL .'/address/delete/'. $address.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png" alt="Delete Address" class="help" title="Delete Address"/>
						</a>&nbsp;
					</td></tr>
				{/foreach}

				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="New Address" rel="modal" href="{print Request::$URL}/address/new/{$CONTACT.id}"></td></tr>
			</table>
		</div>

		<div class="right">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Company</label></th>
					<th>{$CONTACT.COMPANY.name}</th></tr>
				<tr><td><label>Size</label></td>
					<td>{$SIZE[ $CONTACT.COMPANY.size ]}</td></tr>
				<tr><td><label>Phone</label></td>
					<td><a href="skype:{$CONTACT.COMPANY.phone}">{$CONTACT.COMPANY.phone}</a></td></tr>
				<tr><td><label>Email</label></td>
					<td><a href="mailto:{$CONTACT.COMPANY.email}">{$CONTACT.COMPANY.email}</a></td></tr>
				<tr><td><label>Website</label></td>
					<td><a href="{$CONTACT.COMPANY.url}">{$CONTACT.COMPANY.url}</a></td></tr>
				<tr><td colspan=2>
					<input class="btn_link" style="float:right;" type="button" value="Company Details" href="{print Request::$URL}/../companies/details/{$CONTACT.COMPANY.id}"></td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Color</th>
					<th style="width:100%">Label</th></tr>

				{foreach $CONTACT.LABELS as $label}
				<tr><td nowrap>
					<span style="margin:auto; display:block; width:12px; height:12px; border:1px solid black; background-color:#{$label.color};"></span></td>
					<td>{$label.name}</td></tr>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="Labels" rel="modal" href="{print Request::$URL}/labels/{$CONTACT.id}">
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