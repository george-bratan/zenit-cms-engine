

	{if isset($TABBAR)}

		<div class="toolbar" id="tabbar_{print md5(print_r($TABBAR, true))}" rel="tabs" style="float:right">
			{php $index = 0}
			{foreach $TABBAR as $key => $title}
				{php $tab_type = 'middle'}
				{php $index++}

				{if $index == 1}
					{php $tab_type = 'left'}
				{/if}

				{if $index == count($TABBAR)}
					{php $tab_type = 'right'}
				{/if}

				<a id="btn_{$key}" tab="tab_{$key}" class="{$tab_type}" href="javascript:void(0);" rel="tab">
					{$title}
				</a>
			{/foreach}
		</div>

	{/if}