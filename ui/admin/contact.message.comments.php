
	<!-- Begin one column window -->
	<div class="onecolumn">

		<div class="header">
			<span>Conversation</span>

			<br class="clear">
		</div>

		<br class="clear"/>
		<div class="content">


			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

				<tr><th style="width:20%">User</th>
					<th style="width:80%">Comment</th></tr>

				{foreach $MESSAGE.COMMENTS as $COMMENT}
				<tr><td style="vertical-align:top"><strong>{print $COMMENT.admin ? $COMMENT.admin : $MESSAGE.name}</strong><br>
						{$COMMENT.date}</td>
					<td><p>{print Util::Links($COMMENT.content)}</p>
						</td></tr>
				{/foreach}

				<tr><td style="vertical-align:top"><strong>{print Session::Get('ACCOUNT.NAME')}</strong><br>
						{print DB::Fetch('SELECT NOW()')}</td>
					<td>
					    <form class="ajax" action="{print Request::$URL}/comment/{$MESSAGE.id}" method="post" enctype="multipart/form-data">
							<select id="replies" style="margin-bottom:10px; width:300px;">
								<option value="">-</option>
								{foreach $REPLIES as $reply}
								<option value="{print htmlentities($reply.content)}">{$reply.name}</option>
								{/foreach}
							</select>
							<textarea id="wysiwyg" class="wysiwyg" name="VALUES[content]" style="width:100%; height:200px;"></textarea>
							<input type="button" value="Submit Message" style="margin-top:10px; margin-bottom:5px;"
								onclick="javascript:$(this).closest('form').submit();">
						</form></td></tr>

			</table>

		</div>

	</div>
	<!-- End one column window -->


	<script type="text/javascript">

	$(document).ready(function(){
		//
		$('#replies').change(function(){
			//
			if ($('#replies').val()) {
				//
				$('#wysiwyg').wysiwyg('setContent', $('#replies').val());
				//$('.wysiwyg').val( $('#replies').val() );
			}
		});
	});

	</script>