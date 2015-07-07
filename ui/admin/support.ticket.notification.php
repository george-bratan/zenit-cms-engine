
{php $COMMENTS = array_slice($COMMENTS, -3)}

<p style="font-family:Helvetica, Arial;">
	A new message has been posted on Ticket #{$TICKET.id}: {$TICKET.subject}.<br />
	The last {print count($COMMENTS)} comments are included below:
</p>

<p style="font-family:Helvetica, Arial;">
	<table style="width:100%; font-family:Helvetica, Arial;" cellpadding="0" cellspacing="0">

		<tr><th style="width:20%; border-bottom:1px solid black; padding:7px; font-weight:bold; background-color:#EEE;">User</th>
			<th style="width:80%; border-bottom:1px solid black; padding:7px; font-weight:bold; background-color:#EEE;">Comment</th></tr>

		{foreach $COMMENTS as $COMMENT}
		<tr><td style="vertical-align:top; border-bottom:1px solid #CCC; padding:7px;">
				<strong>{$COMMENT.user}</strong><br>
				{$COMMENT.company}<br>
				{$COMMENT.date}
				{if $COMMENT.new}
					<span style="color:red">(new)</span>
				{/if}
				</td>
			<td style="vertical-align:top; border-bottom:1px solid #CCC; padding:7px;">
				<p>{print Util::Links($COMMENT.content)}</p>
				{if $COMMENT.file}
					<p><br></p>
					<p><strong>Attachment:</strong> {$COMMENT.file.original} ({$COMMENT.file.size} bytes) &nbsp; - &nbsp; <a href="{$URL}">Login to Download</a></p>
				{/if}
				</td></tr>
		{/foreach}

	</table>
</p>

<p style="font-family:Helvetica, Arial;">
	To reply to this message please visit the <a href="{$URL}">message thread</a>.
</p>

<p style="font-family:Helvetica, Arial;">
	This is an automated message. Thank you.
</p>

