{php /* PRODUCT LIST TEMPLATE*/ }

{if !count($PRODUCTS)}
	<div class="empty">No products found.</div>
{/if}

{foreach $PRODUCTS as $PRODUCT}

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
			{php $cut = strpos($PRODUCT.description, chr(60).'hr')}
			{print $cut ? substr($PRODUCT.description, 0, $cut-1) : $PRODUCT.description}
			<div class="more"><a href="/products/{print Util::URL( $PRODUCT.name )}">Read More ...</a></div>
		</div>

		<br class="clear" />

	</div>

{/foreach}

{if $PAGE.count > 1}
	<div class="pages">
	{for ($i = 0; $i < $PAGE.count; $i++)}
		<a href="?page={$i}">{print $i+1}</a>
	{/for}
	</div>
{/if}