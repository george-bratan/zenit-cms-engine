
	{if $INPUT.type == Input::F_HIDDEN}
		<input type="hidden" name="{$INPUT.name}" value="{$INPUT.value}"/>
	{/if}

	{if $INPUT.type != Input::F_HIDDEN}
		<p class="field">
			{if $INPUT.title}
				{if $INPUT.type != Input::F_BOOL && $INPUT.type != Input::F_HIDDEN}
				<label>{$INPUT.title}:</label><br/>
				{/if}
			{/if}

			{if $INPUT.type == Input::F_KEY}
				{$INPUT.value}
				<input type="hidden" name="{$INPUT.name}" value="{$INPUT.value}"/>
			{/if}


			{if $INPUT.type == Input::F_TEXT}
				<input id="{$INPUT.id}" type="text" style="width:{$INPUT.width}" name="{$INPUT.name}" value="{$INPUT.value}" />
			{elseif $INPUT.type == Input::F_RANGE}
				<input type="text" style="width:{$INPUT.width}" name="{$INPUT.name}[0]" value="{if isset($INPUT.value[0])}{$INPUT.value[0]}{/if}" />
				<input type="text" style="width:{$INPUT.width}" name="{$INPUT.name}[1]" value="{if isset($INPUT.value[1])}{$INPUT.value[1]}{/if}" />
			{elseif $INPUT.type == Input::F_PASSWORD}
				<input type="password" style="width:{$INPUT.width}" name="{$INPUT.name}" value="{$INPUT.value}" />
			{elseif $INPUT.type == Input::F_FILE}
				<input type="file" style="width:{$INPUT.width};" name="{$INPUT.name}">
			{elseif $INPUT.type == Input::F_LONGTEXT}
				<textarea style="width:{$INPUT.width}; height:{$INPUT.height};" name="{$INPUT.name}">{$INPUT.value}</textarea>
			{elseif $INPUT.type == Input::F_RICHTEXT}
				<textarea style="width:{$INPUT.width}; height:200px;" name="{$INPUT.name}" class="wysiwyg">{$INPUT.value}</textarea>
			{elseif $INPUT.type == Input::F_DATE}
				<input type="text" class="datepicker" style="width:{$INPUT.width}" name="{$INPUT.name}" value="{$INPUT.value}" />
			{elseif $INPUT.type == Input::F_DATERANGE}
				<input type="text" class="datepicker" style="width:{$INPUT.width}" name="{$INPUT.name}[0]" value="{if isset($INPUT.value[0])}{$INPUT.value[0]}{/if}" />
				<input type="text" class="datepicker" style="width:{$INPUT.width}" name="{$INPUT.name}[1]" value="{if isset($INPUT.value[1])}{$INPUT.value[1]}{/if}" />
			{elseif $INPUT.type == Input::F_SELECT}
				<select style="width:{$INPUT.width}" name="{$INPUT.name}" {if $INPUT.multiple}multiple="true"{/if} {if $INPUT.onchange}onchange="{$INPUT.onchange}"{/if}>
					{foreach $INPUT.options as $key => $option}
						{if is_array($option)}
							<optgroup label="{$key}">
							{foreach $option as $k => $v}
								<option value="{$k}" {php print ($k == $INPUT.value) ? 'selected' : '';}>{$v}</option>
							{/foreach}
							</optgroup>
						{else}
							<option value="{$key}" {php print ($key == $INPUT.value) ? 'selected' : '';}>{$option}</option>
						{/if}
					{/foreach}
				</select>
			{elseif $INPUT.type == Input::F_COLOR}
				<select class="colorpicker" style="width:{$INPUT.width}; visibility:hidden;" name="{$INPUT.name}" >
					{foreach $INPUT.options as $key => $option}
						<option value="{$key}" {php print ($key == $INPUT.value) ? 'selected' : '';}>{$option}</option>
					{/foreach}
				</select>
			{elseif $INPUT.type == Input::F_BOOL}
				<input type="hidden" name="{$INPUT.name}" value="false"/>
				<input type="checkbox" name="{$INPUT.name}" id="{$INPUT.name}" value="true" {if $INPUT.value == TRUE}checked{/if} />
				<label for="{$INPUT.name}">{$INPUT.title}</label>
			{elseif $INPUT.type == Input::F_CHECKGROUP}
				{foreach $INPUT.options as $key => $option}
					{if is_array($option)}
						<label style="padding-top:10px; display:block;">{$key}</label>
						{foreach $option as $key => $suboption}
							<input type="hidden" name="{$INPUT.name}[{$key}]" value="false"/>
							<input type="checkbox" name="{$INPUT.name}[{$key}]" id="{$INPUT.name}[{$key}]" value="true" {if is_array($INPUT.value)}{if in_array($key, $INPUT.value)}checked{/if}{/if} />
							<label for="{$INPUT.name}[{$key}]" style="margin-right:20px;">{$suboption}</label>
							{if $INPUT.valign == 'vertical'}<br />{/if}
						{/foreach}
					{else}
						<input type="hidden" name="{$INPUT.name}[{$key}]" value="false"/>
						<input type="checkbox" name="{$INPUT.name}[{$key}]" id="{$INPUT.name}[{$key}]" value="true" {if is_array($INPUT.value)}{if in_array($key, $INPUT.value)}checked{/if}{/if} />
						<label for="{$INPUT.name}[{$key}]" style="margin-right:5px;">{$option}</label>
						{if $INPUT.valign == 'vertical'}<br />{/if}
					{/if}
				{/foreach}
			{elseif $INPUT.type == Input::F_RADIOGROUP}
				{foreach $INPUT.options as $key => $option}
					<input type="radio" name="{$INPUT.name}" id="{$INPUT.name}_{$key}" value="{$key}" {if $key == $INPUT.value}checked{/if} />
					<label for="{$INPUT.name}_{$key}" style="margin-right:20px;">{$option}</label>
					{if $INPUT.valign == 'vertical'}<br />{/if}
				{/foreach}
			{elseif $INPUT.type == Input::F_MAP}

				{php list($lat, $lng) = strpos($INPUT.value, ',') !== false ? explode(',', $INPUT.value) : array('', '');}

				<input id="val_{$INPUT.id}" type="hidden" name="{$INPUT.name}" value="{$INPUT.value}" />
				<input id="loc_{$INPUT.id}" type="text" style="width:80%" value="{if $INPUT.value}{$INPUT.value}{else}{$INPUT.alt}{/if}" />
				<input id="btn_{$INPUT.id}" type="button" value="Locate &raquo" />
				<div   id="map_{$INPUT.id}" style="width:100%; height:200px; background:gray;"></div>

				<script type="text/javascript">

				$('#btn_{$INPUT.id}').click(function(){
					//
					$("#map_{$INPUT.id}").gMap({
						markers: [{
							address: $('#loc_{$INPUT.id}').val(),
							html: "_address",
							draggable: true,
							ondrop: function( value ) { $('#val_{$INPUT.id}').val( value ); }
						}],
						address: $('#loc_{$INPUT.id}').val(),
						zoom: 8
					});
				});

				if ($('#loc_{$INPUT.id}').val()) {
					//
					setTimeout(function(){
						$('#btn_{$INPUT.id}').click();
					}, 100);
				}
				</script>
			{else}
			{/if}

			{if $INPUT.details && ($INPUT.type == Input::F_BOOL || $INPUT.type == Input::F_RADIOGROUP || $INPUT.type == Input::F_CHECKGROUP)}
			<br /><span class="details">{$INPUT.details}</span>
			{elseif $INPUT.details}
			<span class="details">{$INPUT.details}</span>
			{/if}
		</p>

	{/if}
