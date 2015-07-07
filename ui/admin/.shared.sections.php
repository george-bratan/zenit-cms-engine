

{if isset($SECTIONS)}
{if count($SECTIONS)}
	<!-- Begin shortcut menu -->
	<ul id="shortcut">

		{foreach $SECTIONS as $id => $item}
  		<li>
  		  <a href="{$CONF.WWW.ROOT}{$item.url}" id="shortcut{print str_replace('/', '_', $id)}" title="{$item.title}">
		    <img src="{$CONF.WWW.ROOT}/admin/images/{$item.icon}" alt="{$item.title}" style="width:32px;" /><br/>
		    <strong>{$item.title}</strong>
		  </a>
		</li>
		{/foreach}

		{if $HELP}
		<li style="float:right; margin-right:0">
  		  <a href="{$CONF.WWW.ROOT}{$HELP.url}" title="{$HELP.title}" rel="modal">
		    <img src="{$CONF.WWW.ROOT}/admin/images/{$HELP.icon}" alt="{$HELP.title}" style="width:32px;" /><br/>
		    <strong>{$HELP.title}</strong>
		  </a>
		</li>
		{/if}
	</ul>
	<!-- End shortcut menu -->


	<!-- Begin shortcut notification -->
	<div id="shortcut_notifications">
		{foreach $SECTIONS as $id => $item}
			{if isset($item.notification)}
			{if intval($item.notification)}
				<span class="notification" rel="shortcut{print str_replace('/', '_', $id)}">{$item.notification}</span>
			{/if}
			{/if}
		{/foreach}
	</div>
	<!-- End shortcut noficaton -->


	<br class="clear"/>
{/if}
{/if}