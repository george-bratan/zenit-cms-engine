
<table cellpadding=5 cellspacing=5 style="width:100%">
<tr><td style="vertical-align:top">

	<div id="crmcalendar" style=""></div>

</td><td style="width:100%; vertical-align:top">

	<div style="">

		<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
			<tr><th>Time</th><th>Administrator</th>
				<th>Contact</th><th style="width:50%">Note</th></tr>

			{if !count($NOTES)}
			<tr><td></td><td colspan=3>No records found for this day</td></tr>
			{/if}

			{foreach $NOTES as $note}
			<tr><td nowrap>{print date('H:i:s', strtotime($note.date))}</td><td>{$note.admin}</td>
				<td nowrap><a href="{print Request::$URL}/../contacts/details/{$note.idcontact}">{$note.contact} &raquo;</a></td>
				<td>{$note.content}</td></tr>
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
	$("#crmcalendar").datepicker({
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
#crmcalendar .ui-datepicker {
    display: block;
    font-size:24px;
    width:auto;
    float:left;
}
#crmcalendar .ui-datepicker td a {
	width:50px;
}
#crmcalendar .ui-datepicker td span,
#crmcalendar .ui-datepicker td.ui-datepicker-current-day a,
#crmcalendar .ui-datepicker td span,
#crmcalendar .ui-datepicker td.ui-datepicker-current-day a:hover {
	background-image:url({print Request::$URL}/../../images/calendar.active.large.png);
}
</style>