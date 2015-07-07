

	{if isset($TOOLBAR)}
		{foreach $TOOLBAR as $id => $button}
			{if is_numeric($id)}

				<div class="toolbar">
					{php $index = 0}
					{foreach $button as $url => $btn}
						{php $tab_type = 'middle'}
						{php $index++}

						{if $index == 1}
							{php $tab_type = 'left'}
						{/if}

						{if $index == count($button)}
							{php $tab_type = 'right'}
						{/if}

						{if is_array($btn)}
							<a {if isset($btn.id)}id="{$btn.id}"{/if} class="{$tab_type}" href="{$btn.url}" rel="{$btn.rel}">{$btn.title}</a>
						{else}
							<a class="{$tab_type}" href="{$url}">{$btn}</a>
						{/if}

					{/foreach}
				</div>

			{else}

				{if (!$button)}
					{php continue}
				{/if}

				{php $attr = ''}
				{if isset($button.attr)}
				{foreach $button.attr as $k => $v}
					{php $attr .= $k.'="'.$v.'" '}
				{/foreach}
				{/if}

				<div class="toolbar">
					<a {if isset($button.id)}id="{$button.id}"{/if} href="{$button.url}" rel="{if isset($button.rel)}{$button.rel}{else}modal{/if}" class="{if isset($button.class)}{$button.class}{/if}" {$attr}>
				    	{if isset($button.icon)}
				    		<img src="{$CONF.WWW.ROOT}/admin/images/{$button.icon}" alt="{$button.title}" style="width:16px" class="help" title="{$button.title}" />
						{/if}
						{$button.title}
					</a>
				</div>

			{/if}
		{/foreach}
	{/if}