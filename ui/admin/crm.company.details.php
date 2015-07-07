
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Company</label></th>
					<th>{$COMPANY.name}</th></tr>
				<tr><td><label>Size</label></td>
					<td>{$SIZE[ $COMPANY.size ]}</td></tr>
				<tr><td><label>Labels</label></td>
					<td>
						{foreach $COMPANY.LABELS as $label}
							<span style="float:left; margin-right:10px; display:block; width:12px; height:12px; border:1px solid black; background-color:#{$label.color};"></span>
						{/foreach}
						</td></tr>
				<tr><td><label>Phone</label></td>
					<td><a href="skype:{$COMPANY.phone}">{$COMPANY.phone}</a></td></tr>
				<tr><td><label>Email</label></td>
					<td><a href="mailto:{$COMPANY.email}">{$COMPANY.email}</a></td></tr>
				<tr><td><label>Website</label></td>
					<td><a href="{$COMPANY.url}">{$COMPANY.url}</a></td></tr>
				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Edit Details" rel="modal" href="{print Request::$URL}/edit/{$COMPANY.id}"></td></tr>

				<tr><td><label>Address</label></td>
					<td>{$COMPANY.address}</td></tr>
				<tr><td><label>City</label></td>
					<td>{$COMPANY.city}</td></tr>
				<tr><td><label>Country</label></td>
					<td>{$COMPANY.country}</td></tr>
				<tr><td><label>Postcode</label></td>
					<td>{$COMPANY.postcode}</td></tr>
				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Edit Address" rel="modal" href="{print Request::$URL}/address/{$COMPANY.id}"></td></tr>
			</table>
		</div>

		<div class="right">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Contact</th><th nowrap>Position</th>
					<th nowrap>Phone</th><th>Email</th></tr>

				{foreach $COMPANY.CONTACTS as $contact}
				<tr><td nowrap>{$contact.fullname}</td><td nowrap>{$contact.position}</td>
					<td nowrap><a href="skype:{$contact.phone}">{$contact.phone}</a></td><td><a href="mailto:{$contact.email}">{$contact.email}</a></td>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="New Contact" rel="modal" href="{print Request::$URL}/../contacts/new">
					</td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Color</th>
					<th style="width:100%">Label</th></tr>

				{foreach $COMPANY.LABELS as $label}
				<tr><td nowrap>
					<span style="margin:auto; display:block; width:12px; height:12px; border:1px solid black; background-color:#{$label.color};"></span></td>
					<td>{$label.name}</td></tr>
				{/foreach}

				<tr><td colspan=5>
					<input class="btn_modal" style="float:right;" type="button" value="Labels" rel="modal" href="{print Request::$URL}/labels/{$COMPANY.id}">
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