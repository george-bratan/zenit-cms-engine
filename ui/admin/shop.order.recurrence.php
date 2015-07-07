
<div style="min-width:500px">
	{if isset($TARGET)}
	<form action="{$TARGET}" class="{if !isset($AJAX)}ajax{/if} save" method="post">
	{else}
	<form action="{print Request::$URL}/save" class="ajax save" method="post">
	{/if}

		<table class="data" cellspacing="0" cellpadding="0" style="width:100%;">

			<tr><td colspan=3>
				<input class="recurrence" type="radio" name="subscription[type]" value="0" id="recurrence_none" {if $ORDER.subtype == 0}checked{/if} /><label for="recurrence_none">No Recurrence</label>
				</td></tr>

			<tr><td>
					<input class="recurrence" type="radio" name="subscription[type]" value="1" id="recurrence_week" {if $ORDER.subtype == 1}checked{/if} /><label for="recurrence_week" style="width:50px; display:inline-block;">Weekly: </label>
					</td>
				<td>on &nbsp;
					<select class="recurrence" name="subscription[wday]" style="width:100px">
						{foreach $CONF.WDAYS as $day => $name}
						<option value="{$day}" {if $ORDER.subtype == 1 && $ORDER.subday == $day}selected{/if}>{$name}</option>
						{/foreach}
					</select></td>
				<td>of every &nbsp;
					<select class="recurrence" name="subscription[week]" style="width:100px">
						<option value="1" {if $ORDER.subtype == 1 && $ORDER.subinterval == 1}selected{/if}>Week</option>
						<option value="2" {if $ORDER.subtype == 1 && $ORDER.subinterval == 2}selected{/if}>2 Weeks</option>
						<option value="3" {if $ORDER.subtype == 1 && $ORDER.subinterval == 3}selected{/if}>3 Weeks</option>
						<option value="4" {if $ORDER.subtype == 1 && $ORDER.subinterval == 4}selected{/if}>4 Weeks</option>
						<option value="5" {if $ORDER.subtype == 1 && $ORDER.subinterval == 5}selected{/if}>5 Weeks</option>
						<option value="6" {if $ORDER.subtype == 1 && $ORDER.subinterval == 6}selected{/if}>6 Weeks</option>
					</select>
					</td></tr>

			<tr><td>
					<input class="recurrence" type="radio" name="subscription[type]" value="2" id="recurrence_month" {if $ORDER.subtype == 2}checked{/if} /><label for="recurrence_month" style="width:50px; display:inline-block;">Monthly: </label>
					</td>
				<td>on &nbsp;
					<select class="recurrence" name="subscription[mday]" style="width:100px">
						{foreach $CONF.MDAYS as $day => $name}
						<option value="{$day}" {if $ORDER.subtype == 2 && $ORDER.subday == $day}selected{/if}>{$name}</option>
						{/foreach}
					</select></td>
				<td>of every &nbsp;
					<select class="recurrence" name="subscription[month]" style="width:100px">
						<option value="1" {if $ORDER.subtype == 2 && $ORDER.subinterval == 1}selected{/if}>Month</option>
						<option value="2" {if $ORDER.subtype == 2 && $ORDER.subinterval == 2}selected{/if}>2 Months</option>
						<option value="3" {if $ORDER.subtype == 2 && $ORDER.subinterval == 3}selected{/if}>3 Months</option>
						<option value="4" {if $ORDER.subtype == 2 && $ORDER.subinterval == 4}selected{/if}>4 Months</option>
						<option value="5" {if $ORDER.subtype == 2 && $ORDER.subinterval == 5}selected{/if}>5 Months</option>
						<option value="6" {if $ORDER.subtype == 2 && $ORDER.subinterval == 6}selected{/if}>6 Months</option>
					</select>
					</td></tr>
		</table>

		<br class="clear">

		<p>
			<input type="button" value="   Save   " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="$.fancybox.close();"/>
		</p>

	</form>
</div>