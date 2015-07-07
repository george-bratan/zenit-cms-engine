{php /* ARTICLE DETAIL TEMPLATE*/ }

{if $ARTICLE}
	<div class="article">

		{if count($ARTICLE.images)}
		<div class="thumbnail">

			<a href="/articles/{print Util::URL( $ARTICLE.title )}" class="thumbnail" title="{$ARTICLE.title}">
				<img src="{$ARTICLE.images[0].url}" class="thumbnail" alt="{$ARTICLE.images[0].title}" title="{$ARTICLE.images[0].title}" /></a>

			{if count($ARTICLE.links)}
			<div class="links">
				{foreach $ARTICLE.links as $n => $LINK}
					<a href="{$LINK.url}" title="{$LINK.title}" rel="link">{$LINK.title}</a>{if ($n+1 < count($ARTICLE.links))},{/if}
				{/foreach}
			</div>
			{/if}

		</div>
		{/if}

		<h4><a href="/articles/{print Util::URL( $ARTICLE.title )}" rel="bookmark" title="{$ARTICLE.title}">{$ARTICLE.title}</a></h4>
		<div class="date">{$ARTICLE.pubdate}</div>
		<div class="categories">
			<a href="/articles/category/{print Util::URL($ARTICLE.category)}" title="{$ARTICLE.category}" rel="category">{print strtoupper($ARTICLE.category)}</a>
		</div>

		<div class="body">
			{$ARTICLE.body}
		</div>

		<br class="clear" />

		{if count($ARTICLE.images) > 1}
		<ul class="gallery">
			{foreach $ARTICLE.images as $IMAGE}
				<li>
					<a href="{$IMAGE.url}" title="{$IMAGE.title}">
						<img src="{$IMAGE.url}" alt="{$IMAGE.title}" title="{$IMAGE.title}" /></a>
					<div class="title">{$IMAGE.title}</div>
					<div class="description">{$IMAGE.description}</div>
				</li>
			{/foreach}
		</ul>
		{/if}

		<br class="clear" />

		{if $ARTICLE.keywords}
		<div class="keywords">
			{php $keywords = explode(',', $ARTICLE.keywords)}
			{foreach $keywords as $n => $keyword}
				{php $keyword = trim($keyword)}
				<a href="/articles/keyword/{print Util::URL($keyword)}" title="{$keyword}" rel="keyword">{$keyword}</a>{if ($n+1 < count($keywords))},{/if}
			{/foreach}
		</div>
		{/if}

		<br class="clear" />

		{if $ARTICLE.cancomment}
		<div class="comments">

			{if !count($ARTICLE.comments)}
				<div class="nocomments">No comments yet!</div>
			{/if}

			{foreach $ARTICLE.comments as $COMMENT}
			<div class="comment">
				<div class="name">
					{if $COMMENT.url}
						<a href="{$COMMENT.url}" target="_blank">{$COMMENT.name}</a>
					{else}
						{$COMMENT.name}
					{/if}
				</div>
				<div class="date">{$COMMENT.date}</div>
				<div class="body">{$COMMENT.content}</div>
			</div>
			{/foreach}

			<div class="comment-form-container">
				<div class="error">{$FORM.error}</div>

				<form action="" method="post">
					<ul class="comment-form">
						<li><label>Name</label>
							{$FORM.name}</li>
						<li><label>Email</label>
							{$FORM.email}</li>
						<li><label>Website</label>
							{$FORM.website}</li>
						<li><label>Comment</label>
							{$FORM.comment}</li>

                        <li>{$FORM.captcha}</li>
						<li>{$FORM.submit}</li>
					</ul>
				</form>
			</div>

		</div>
		{/if}

	</div>
{/if}