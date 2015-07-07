{php /* MENU TEMPLATE */ }

	<ul class="menu">
		{foreach $MENU.items as $ITEM}
		<li class="menu-item">
			<a href="{$ITEM.url}">{$ITEM.caption}</a>

			{if count($ITEM.children)}
			<ul class="submenu">
				{foreach $ITEM.children as $CHILD}
				<li class="menu-item">
					<a href="{$CHILD.url}">{$CHILD.caption}</a>
				</li>
				{/foreach}
			</ul>
			{/if}

		</li>
		{/foreach}
	</ul>