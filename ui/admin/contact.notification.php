
{php $COMMENTS = array_slice($COMMENTS, -3)}

<p style="font-family:Helvetica, Arial;">
	<!--
	A new message has been posted on Ticket #{$TICKET.id}: {$TICKET.subject}.<br />
	-->
	A new message has been posted on your ticket: <strong>{$MESSAGE.uid}</strong><br />
	The last {print count($COMMENTS)} comments are included below:
</p>

<p style="font-family:Helvetica, Arial;">
	<table style="width:100%; font-family:Helvetica, Arial;" cellpadding="0" cellspacing="0">

		<tr><th style="width:20%; border-bottom:1px solid black; padding:7px; font-weight:bold; background-color:#EEE;">User</th>
			<th style="width:80%; border-bottom:1px solid black; padding:7px; font-weight:bold; background-color:#EEE;">Comment</th></tr>

		{foreach $COMMENTS as $COMMENT}
		<tr><td style="vertical-align:top; border-bottom:1px solid #CCC; padding:7px;"><strong>{print $COMMENT.admin ? $COMMENT.admin : $MESSAGE.name}</strong><br>
				{$COMMENT.date}</td>
			<td style="vertical-align:top; border-bottom:1px solid #CCC; padding:7px;"><p>{print Util::Links($COMMENT.content)}</p>
				</td></tr>
		{/foreach}

	</table>
</p>

{if strpos($URL, '@uid') !== false}
<p style="font-family:Helvetica, Arial;">
	To reply to this message please visit the <a href="{print str_replace('@uid', $MESSAGE.uid, $URL)}">message thread</a>.
</p>
{elseif $URL}
<p style="font-family:Helvetica, Arial;">
	To reply to this message please visit <a href="{$URL}">our website</a> and use the unique thread identifier <strong>{$MESSAGE.uid}</strong> to locate your message.
</p>
{/if}

<p style="font-family:Helvetica, Arial;">
	This is an automated message. Thank you.
</p>

