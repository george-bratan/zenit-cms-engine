
	{if isset($HEADER)}
		{$HEADER}
	{/if}

	<form id="bulk" name="bulk" action="{print Request::$URL}/bulk" method="post">

		<table class="data" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:10px">
						<input type="checkbox" id="check_all" name="check_all"/>
					</th>


					{if count($FIELDS)}
					{foreach $FIELDS as $field => $title}
						<!--th style="width:{print intval(85/count($FIELDS))}%"-->
						<th nowrap>

							<a title="Order by: {$title}" href="{print Request::$URL}/order"
								data="ORDER[{$field}]={if isset($ORDER[$field])}{if $ORDER[$field] == 'ASC'}DESC{else}ASC{/if}{else}ASC{/if}" rel="post">
								{$title}
								<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/arrow.{if isset($ORDER[$field])}{if $ORDER[$field] == 'ASC'}down{else}up{/if}{else}none{/if}.png" alt="order" class="help" title="" />
							</a>

						</th>
					{/foreach}
					{/if}

					{if count($FIXED)}
					{foreach $FIXED as $field => $title}
						<th style="width:80px">{$title}</th>
					{/foreach}
					{/if}

					{if isset($OPTIONS)}
					<th style="width:100px; min-width:100px; text-align:center;">Options</th>
					{/if}
				</tr>
			</thead>
			<tbody>
				{if (!count($LIST))}
				<tr><td>&nbsp;</td><td colspan="{print count($FIELDS) + count($FIXED) + 1;}">No records found.</td></tr>
				{/if}

				{foreach $LIST as $item}
				<tr>
					<td>
						<input type="checkbox" name="items[]" value="{$item.id}" />
					</td>


					{foreach $FIELDS as $field => $title}
						{php $value = isset($FORMAT[$field]) ? call_user_func($FORMAT[$field], $item) : $item[$field]}
						<td>
							{if is_a($value, 'Model')}
								{php $value = $value->name}
							{/if}
							{if $value}{$value}{else}-{/if}
						</td>
					{/foreach}

					{foreach $FIXED as $field => $title}
						{php $value = isset($FORMAT[$field]) ? call_user_func($FORMAT[$field], $item) : $item[$field]}
						<td nowrap>
							{if $value}{$value}{else}-{/if}
						</td>
					{/foreach}

					{if isset($OPTIONS)}
					<td style="text-align:center">

						{if count($OPTIONS) == 0}
							-
						{/if}

						{foreach $OPTIONS as $option}
							{if !isset($option.url)}
								{php $option.url = Request::$URL .'/'. $option.handler .'/'. $item.id}
							{/if}
							{if !isset($option.title)}
								{php $option.title = ucwords($option.handler);}
							{/if}

							<a rel="{if isset($option.rel)}{$option.rel}{/if}" id="{$option.handler}_{$item.id}" class="{if isset($option.class)}{$option.class}{/if}" href="{$option.url}">
								{if isset($option.icon)}
									<img src="{$CONF.WWW.ROOT}/admin/images/{$option.icon}" alt="{$option.title}" style="width:16px" class="help" title="{$option.title}"/>
								{else}
									{$option.title}
								{/if}
							</a>
							&nbsp;
						{/foreach}

					</td>
					{/if}
				</tr>
				{/foreach}

			</tbody>
		</table>

		{include 'admin/.shared.bulk.php'}

	</form>

	{include 'admin/.shared.pagination.php'}

	{if isset($FOOTER)}
		{$FOOTER}
	{/if}