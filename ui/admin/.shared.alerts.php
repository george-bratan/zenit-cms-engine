
	{if isset($MESSAGE)}

		{if isset($MESSAGE.INFO)}
		<div class="alert_info">
			<p>
				<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/info.png" alt="success" class="mid_align"/>
				{$MESSAGE.INFO}
			</p>
		</div>
		{/if}

		{if isset($MESSAGE.SUCCESS)}
		<div class="alert_info">
			<p>
				<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/accept.png" alt="success" class="mid_align"/>
				{$MESSAGE.SUCCESS}
			</p>
		</div>
		{/if}

		{if isset($MESSAGE.WARNING)}
		<div class="alert_info">
			<p>
				<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/warning.png" alt="success" class="mid_align"/>
				{$MESSAGE.WARNING}
			</p>
		</div>
		{/if}

		{if isset($MESSAGE.ERROR)}
		<div class="alert_info">
			<p>
				<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/error.png" alt="success" class="mid_align"/>
				{$MESSAGE.ERROR}
			</p>
		</div>
		{/if}

	{/if}
