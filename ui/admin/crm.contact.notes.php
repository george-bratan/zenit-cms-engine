
	<!-- Begin one column window -->
	<div class="onecolumn">

		<div class="header">
			<span>Contact History</span>
			<br class="clear">
		</div>

		<br class="clear"/>
		<div class="content">

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

				<tr><th style="width:200px">Administrator</th>
					<th style="">Notes</th></tr>

				{foreach $CONTACT.NOTES as $NOTE}
				<tr><td style="vertical-align:top">
						<strong>{$NOTE.admin}</strong><br>
						{$METHOD[ $NOTE.method ]}<br>
						{$NOTE.date}</td>
					<td>
						<p> <strong>Subject: </strong> {$NOTE.subject} </p>
						<p> <strong>Resolution: </strong> {$NOTE.flagnames} </p>
						<p> &nbsp; </p>
						<p> {$NOTE.content}</p>
						</td></tr>
				{/foreach}

				<tr><td style="vertical-align:top"><strong>{$ACCOUNT.NAME}</strong><br>
						{print DB::Fetch('SELECT NOW()')}</td>
					<td>
					    <input id="note" type="button" class="btn_modal" value="Add Note" style="margin-top:10px; margin-bottom:5px;" href="{print Request::$URL}/note/{$CONTACT.id}">
					  </td></tr>

			</table>

		</div>

	</div>
	<!-- End one column window -->


	<script type="text/javascript">

 	$(document).ready(function(){

	 	$('div.toolbar a[rel=tab]').click(function(){
			//switch menu
			$(this).parent().parent().find('a[rel=tab]').removeClass('active');
			$(this).addClass('active');

			//show tab1 content
			$('.tab').addClass('hide');
			$('#' + $(this).attr('tab')).removeClass('hide');

			$.cookie("ADMIN.SUPPORT.LAST", $(this).attr('id'));
		});

		if ($('#' + $.cookie("ADMIN.SUPPORT.LAST")).length)
			$('#' + $.cookie("ADMIN.SUPPORT.LAST")).click();
		else
			$('div.toolbar a[rel=tab]').filter(':first').click();

	});

	</script>