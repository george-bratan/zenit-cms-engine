
	<!-- Begin one column window -->
	<div class="onecolumn">

		<div class="header">
			<span>Comments</span>

			<br class="clear">
		</div>

		<br class="clear"/>
		<div class="content">


			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

				<tr><th style="width:20%">User</th>
					<th style="width:80%">Comment</th></tr>

				{foreach $ARTICLE.COMMENTS as $COMMENT}
				<tr><td style="vertical-align:top">

						{if $COMMENT.status == 1}
							<span style="float:right; color:green;">Published</span>
						{/if}
						{if $COMMENT.status == 0}
							<span style="float:right; color:blue;">Pending</span>
						{/if}
						{if $COMMENT.status == -1}
							<span style="float:right; color:red;">Rejected</span>
						{/if}
						<strong>{$COMMENT.name}</strong><br />
						<a href="mailto:{$COMMENT.email}">{$COMMENT.email}</a><br />
						{if $COMMENT.url}<a href="{$COMMENT.url}" target="_blank">{$COMMENT.url}</a><br />{/if}
						{$COMMENT.date}</td>
					<td><p>{print Util::Links($COMMENT.content)}</p>
						<br /><br />
						{if $COMMENT.status != 1}
							<input class="btn_status" style="margin-right:5px;" type="button" value="Publish" href="{print Request::$URL}/accept/{$COMMENT.id}">
						{/if}
						{if $COMMENT.status != -1}
							<input class="btn_status" style="margin-right:5px;" type="button" value="Reject" href="{print Request::$URL}/reject/{$COMMENT.id}">
						{/if}
						</td></tr>
				{/foreach}

				{if $ARTICLE.cancomment}
				<tr><td>&nbsp;</td>
					<td>Comments are <strong>Enabled</strong> - <a class="btn_status" style="color:red" href="{print Request::$URL}/cancomment/{$ARTICLE.id}" data="status=0">Disable</a>
						</td></tr>

				<tr><td style="vertical-align:top"><strong>{print Session::Get('ACCOUNT.NAME')}</strong><br>
						{print DB::Fetch('SELECT NOW()')}</td>
					<td>
					    <form class="ajax" action="{print Request::$URL}/comment/{$ARTICLE.id}" method="post" enctype="multipart/form-data">
							<textarea id="wysiwyg" class="wysiwyg" name="VALUES[content]" style="width:100%; height:200px;"></textarea>
							<input type="button" value="Submit Comment" style="margin-top:10px; margin-bottom:5px;"
								onclick="javascript:$(this).closest('form').submit();">
						</form></td></tr>
				{else}
				<tr><td>&nbsp;</td>
					<td><strong>Comments are Disabled - <a class="btn_status" style="color:green" href="{print Request::$URL}/cancomment/{$ARTICLE.id}" data="status=1">Enable</a></strong>
						</td></tr>
				{/if}

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