
<table cellpadding=5 cellspacing=5 style="width:100%">
<tr><td style="vertical-align:top">

	<div id="shopcalendar" style=""></div>

</td><td style="width:100%; vertical-align:top">

	<div style="">

		<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
			<tr><th>Order</th><th>Scheduled for</th>
				<th>Status</th><th style="text-align:center; width:80px;">Options</th></tr>

			{if !count($DELIVERIES)}
			<tr><td></td><td colspan=3>No records found for this day</td></tr>
			{/if}

			{foreach $DELIVERIES as $DELIVERY}
			<tr><td nowrap>#{$DELIVERY.idorder}: {$DELIVERY.client}</td><td>{$DELIVERY.scheduled}</td>
				<td nowrap>
					<span style="color:{if $DELIVERY.status == 3}red{elseif $DELIVERY.status == 2}blue{elseif $DELIVERY.status == 1}green{else}black{/if}">
						{print $STATUS[ $DELIVERY.status ]}</span></td>
				<td nowrap style="text-align:center;">
					<a rel="modal" id="edit_{$address.id}" class="" href="{print Request::$URL .'/../deliveries/details/'. $DELIVERY.id}">
						<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png" alt="Edit Delivery" class="help" title="Edit Delivery"/>
					</a>&nbsp;
					<a rel="modal" id="delete_{$address.id}" class="" href="{print Request::$URL .'/../deliveries/delete/'. $DELIVERY.id}">
						<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png" alt="Delete Delivery" class="help" title="Delete Delivery"/>
					</a>&nbsp;
				</td></tr>
			{/foreach}

		</table>

	</div>

</td></tr>
</table>
<br class="clear" />


<script type="text/javascript">

$(document).ready(function(){

	function parseDate(input) {
	  var parts = input.match(/(\d+)/g);
	  return new Date(parts[0], parts[1]-1, parts[2]);
	}

	//parseDate('2011-01-03'); // Mon Jan 03 2011 00:00:00

	//
	$("#shopcalendar").datepicker({
		nextText: '&raquo;',
		prevText: '&laquo;',
		dateFormat: "yy-mm-dd",

		{if isset($DATE)}
		defaultDate: parseDate('{$DATE}'),
		{/if}

		onSelect: function(date) {

			$.fancybox.showActivity();
			window.location = '{print Request::$URL}' + '/' + date;

		}
	});

});

</script>

<style type="text/css">
#shopcalendar .ui-datepicker {
    display: block;
    font-size:24px;
    width:auto;
    float:left;
}
#shopcalendar .ui-datepicker td a {
	width:50px;
}
#shopcalendar .ui-datepicker td span,
#shopcalendar .ui-datepicker td.ui-datepicker-current-day a,
#shopcalendar .ui-datepicker td span,
#shopcalendar .ui-datepicker td.ui-datepicker-current-day a:hover {
	background-image:url({print Request::$URL}/../../images/calendar.active.large.png);
}
</style>