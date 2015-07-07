{php /* CONTACT THREAD TEMPLATE */ }

	<div class="message-subject">{$MESSAGE.subject}</div>
	<div class="message-name">{$MESSAGE.name}</div>
	<div class="message-date">{$MESSAGE.date}</div>
	<div class="message-uid">Unique ID: {$MESSAGE.uid}</div>

	<div class="comments">

		{if !count($MESSAGE.comments)}
			<div class="nocomments">No comments yet!</div>
		{/if}

		{foreach $MESSAGE.comments as $COMMENT}
		<div class="comment">

			{if $COMMENT.idadmin}
				<div class="admin-name">
					{$COMMENT.admin}
				</div>
			{else}
				<div class="contact-name">
					{$COMMENT.contact}
				</div>
			{/if}

			<div class="date">{$COMMENT.date}</div>
			<div class="body">{$COMMENT.content}</div>
		</div>
		{/foreach}

		<div class="comment-form-container">
			<div class="error">{$FORM.error}</div>

			<form action="" method="post">
				<ul class="comment-form">
					<li><label>Comment</label>
						{$FORM.comment}</li>

					<li>{$FORM.submit}</li>
				</ul>
			</form>
		</div>

	</div>