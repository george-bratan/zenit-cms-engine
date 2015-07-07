
	<form id="bulk" name="form_data" action="" method="post" enctype="multipart/form-data">

        <div class="left" style="width:59%">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th style="width:10px"><input type="checkbox" id="check_all" name="check_all"/></th>
					<th>Menu Item</th>
					<th style="width:80px">Status</th>
					<th style="width:100px; min-width:100px; text-align:center;">Options</th></tr>

				{foreach $MENU.ITEMS as $item}
				<tr><td><input class="menuitem" id="item_{$item.id}" type="checkbox" name="items[]" value="{$item.id}" /></td>
					<td nowrap>
						{if $item.level}
							{for ($i = 0; $i < $item.level; $i++)}
								&nbsp; &nbsp; &nbsp;
							{/for}
							<span style="position:relative; top:-3px;">&lfloor;</span> &nbsp;
						{/if}
						{$item.caption}</td>
					<td nowrap>
						<a rel="post" href="{print Request::$URL.'/items/status/'.$item.id}" style="color:{print $item.status ? 'green' : 'red'}">
							<span>{print $item.status ? 'Enabled' : 'Disabled'}</span></a>
					</td>
					<td nowrap style="text-align:center;">
						<a rel="modal" id="edit_{$item.id}" class="" href="{print Request::$URL .'/items/details/'. $item.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png" alt="Edit Menu Item" class="help" title="Edit Menu Item"/>
						</a>&nbsp;
						<a rel="modal" id="delete_{$item.id}" class="" href="{print Request::$URL .'/items/delete/'. $item.id}">
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png" alt="Delete Menu Item" class="help" title="Delete Menu Item"/>
						</a>&nbsp;
					</td></tr>
				{/foreach}

				<tr><td colspan=2>
						<input class="btn_move" style="float:left; margin-right:5px;" type="button" value="&lArr;" serialize=".menuitem" href="{print Request::$URL}/items/left">
						<input class="btn_move" style="float:left; margin-right:5px;" type="button" value="&uArr;" serialize=".menuitem" href="{print Request::$URL}/items/up">
						<input class="btn_move" style="float:left; margin-right:5px;" type="button" value="&dArr;" serialize=".menuitem" href="{print Request::$URL}/items/down">
						<input class="btn_move" style="float:left; margin-right:5px;" type="button" value="&rArr;" serialize=".menuitem" href="{print Request::$URL}/items/right"></td>
					<td colspan=2>
						<input class="btn_modal" style="float:right;" type="button" value="New Menu Item" rel="modal" href="{print Request::$URL}/items/new/{$MENU.id}"></td></tr>
			</table>
		</div>

		<div class="right" style="width:39%">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Menu ID</th><th nowrap>#{$MENU.id}</th></tr>
				<tr><td nowrap>Name</td><td nowrap>{$MENU.name}</td></tr>

				<tr><td colspan=4>
					<input class="btn_modal" style="float:right;" type="button" value="Edit" rel="modal" href="{print Request::$URL}/edit/{$MENU.id}">
					</td></tr>
			</table>
		</div>

		<br class="clear">

	</form>

	<script type="text/javascript">

 	$(document).ready(function(){

 		items = $.cookie('menu.items.selected');

 		if (items) {
 			//
	 		$('.menuitem').each(function(){
	 			//
	 			//if ($(this).val() in items.split('&')) {
	 			if ($.inArray( $(this).val(), items.split('&') ) > -1) {
	 				//
	 				$(this).attr('checked', true);
	 				$(this).parents('tr:first').addClass('hover');
	 			}
	 		});
 		}


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

 		$('.btn_status, .btn_move').click(function(){

 			$.fancybox.showActivity();

 			$.cookie('menu.items.selected', $( $(this).attr('serialize') ).serialize().replace(/items%5B%5D=/g, '') );

 			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				data: $(this).attr('data') ? $(this).attr('data') : ($(this).attr('serialize') ? $( $(this).attr('serialize') ).serialize() : ''),
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