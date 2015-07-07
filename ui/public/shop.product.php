{php /* PRODUCT DETAIL TEMPLATE*/ }

{if $PRODUCT}
	<div class="product">

		{if count($PRODUCT.images)}
		<div class="thumbnail">

			<a href="/products/{print Util::URL( $PRODUCT.name )}" class="thumbnail" title="{$PRODUCT.name}">
				<img src="{$PRODUCT.images[0].url}" class="thumbnail" alt="{$PRODUCT.images[0].title}" title="{$PRODUCT.images[0].title}" /></a>

		</div>
		{/if}

		<h4><a href="/products/{print Util::URL( $PRODUCT.name )}" rel="bookmark" title="{$PRODUCT.name}">{$PRODUCT.name}</a></h4>
		<div class="categories">
			<a href="/products/category/{print Util::URL($PRODUCT.category)}" title="{$PRODUCT.category}" rel="category">{print strtoupper($PRODUCT.category)}</a>
		</div>

		<div class="description">
			{$PRODUCT.description}
		</div>

		<br class="clear" />

		{if count($PRODUCT.images) > 1}
		<ul class="gallery">
			{foreach $PRODUCT.images as $IMAGE}
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

		{if $PRODUCT.keywords}
		<div class="keywords">
			{php $keywords = explode(',', $PRODUCT.keywords)}
			{foreach $keywords as $n => $keyword}
				{php $keyword = trim($keyword)}
				<a href="/products/keyword/{print Util::URL($keyword)}" title="{$keyword}" rel="keyword">{$keyword}</a>{if ($n+1 < count($keywords))},{/if}
			{/foreach}
		</div>
		{/if}

		<br class="clear" />

	</div>
{/if}