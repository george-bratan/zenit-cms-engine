{php /* ARTICLE LIST TEMPLATE*/ }

{if !count($ARTICLES)}
	<div class="empty">No articles found.</div>
{/if}

{foreach $ARTICLES as $ARTICLE}

	<div class="article">

		{if count($ARTICLE.images)}
		<div class="thumbnail">

			<a href="/articles/{print Util::URL( $ARTICLE.title )}" class="thumbnail" title="{$ARTICLE.title}">
				<img src="{$ARTICLE.images[0].url}" class="thumbnail" alt="{$ARTICLE.images[0].title}" title="{$ARTICLE.images[0].title}" /></a>

			{if count($ARTICLE.links)}
			<div class="links">
				{foreach $ARTICLE.links as $LINK}
					<a href="{$LINK.url}" title="{$LINK.title}" rel="link">{$LINK.title}</a>,
				{/foreach}
			</div>
			{/if}

		</div>
		{/if}

		<h4><a href="/articles/{print Util::URL( $ARTICLE.title )}" rel="bookmark" title="{$ARTICLE.title}">{$ARTICLE.title}</a></h4>
		<div class="date">{$ARTICLE.pubdate}</div>
		<div class="categories">
			<a href="/articles/category/{print Util::URL($ARTICLE.category)}" title="{$ARTICLE.category}" rel="category">{$ARTICLE.category}</a>
		</div>

		<div class="body">
			{php $cut = strpos($ARTICLE.body, chr(60).'hr')}
			{print $cut ? substr($ARTICLE.body, 0, $cut-1) : $ARTICLE.body}
			<div class="more"><a href="/articles/{print Util::URL( $ARTICLE.title )}">Read More ...</a></div>
		</div>

		<br class="clear" />

	</div>

{/foreach}

{if $PAGE.count}
	<div class="pages">
	{for ($i = 0; $i < $PAGE.count; $i++)}
		<a href="?page={$i}">{print $i+1}</a>
	{/for}
	</div>
{/if}