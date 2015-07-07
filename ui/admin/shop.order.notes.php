
	<!-- Begin one column window -->
	<div class="onecolumn">

		<div class="header">
			<span>Notes</span>

			<br class="clear">
		</div>

		<br class="clear"/>
		<div class="content">


			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

				<tr><th style="width:20%">User</th>
					<th style="width:80%">Comment</th></tr>

				{foreach $ORDER.NOTES as $NOTE}
				<tr><td style="vertical-align:top"><strong>{$NOTE.admin}</strong><br>
						{$NOTE.date}</td>
					<td><p>{print Util::Links($NOTE.content)}</p>
						{if $NOTE.file}
							<p><br></p>
							<p><strong>Attachment:</strong> {$NOTE.file.original} ({$NOTE.file.size} bytes) &nbsp; - &nbsp; <a href="{print Request::$URL}/download/{$NOTE.file.id}">Download</a></p>
						{/if}
						</td></tr>
				{/foreach}

				<tr><td style="vertical-align:top"><strong>{print Session::Get('ACCOUNT.NAME')}</strong><br>
						{print DB::Fetch('SELECT NOW()')}</td>
					<td>
					    <form class="ajax" action="{print Request::$URL}/note/{$ORDER.id}" method="post" enctype="multipart/form-data">
							<textarea class="wysiwyg" name="VALUES[content]" style="width:100%; height:200px;"></textarea>
							<input type="button" value="Submit Note" style="margin-top:10px; margin-bottom:5px;"
								onclick="javascript:$(this).closest('form').submit();">
							<span style="float:right; margin-top:10px;">Attach File: <input type="file" name="file" /></span>
						</form></td></tr>

			</table>

		</div>

	</div>
	<!-- End one column window -->


	<script type="text/javascript">

	</script>