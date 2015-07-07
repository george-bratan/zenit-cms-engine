
	<ul id="main_menu">

		{php $MENU = Admin::Menu()}

		{foreach $MENU as $url => $item}

		<li>
			<a href="{if $item.url}{$CONF.WWW.ROOT}{$item.url}{/if}"><img src="{$CONF.WWW.ROOT}/admin/images/{$item.icon}" alt="{$item.title}"/>{$item.title}</a>
			{if isset($item.children)}
			<ul>
				{foreach $item.children as $child}
				<li><a href="{$CONF.WWW.ROOT}{$child.url}" {if isset($child.rel)}rel="{$child.rel}"{/if}>{$child.title}</a></li>
				{/foreach}
			</ul>
			{/if}
		</li>

		{/foreach}
	</ul>


