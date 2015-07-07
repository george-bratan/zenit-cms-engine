
<div style="min-width:600px">
	<form action="{print Request::$URL}/comment/{$VIDEO}" class="ajax save" method="post">

		<p>
			{foreach $FIELDS as $key => $input}

				{print $input->Render()}

			{/foreach}
		</p>

		<br class="clear">

		<p>
			<input type="button" value="  Submit  " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

		<br class="clear">

		<div style="max-height:300px; overflow-y:auto;">
			<table class="data" width="100%" cellspacing="0" cellpadding="0">
			<tr><th>User</th>
					<th>Comment</th>
				</tr>

			{if !count($COMMENTS)}
				<tr><td colspan=2>No comments found.</td></tr>
			{/if}

			{foreach $COMMENTS as $COMMENT}
				<tr><td nowrap><a href="{$COMMENT.profile}" target="_blank">{$COMMENT.user}</a><br />
						{print date(Conf::get('FORMAT:DATE:LONG'), strtotime($COMMENT.date))}</td>
					<td>{$COMMENT.content}</td>
				</tr>
			{/foreach}
			</table>
		</div>

		<br class="clear">

	</form>
</div>