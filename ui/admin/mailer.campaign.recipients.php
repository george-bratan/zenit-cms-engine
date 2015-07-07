
<div id="inspector">
	<div class="tab" id="tab_recp" style="height:540px; max-height:540px; overflow-y:auto; overflow-x:hidden;">

		<!--h4>Recipients</h4>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in porta lectus. Maecenas dignissim enim quis ipsum
			mattis aliquet. Maecenas id velit et elit gravida bibendum. Duis nec rutrum lorem. Donec egestas metus a risus
			euismod ultricies. Maecenas lacinia orci at neque commodo commodo. Donec egestas metus a risus
			euismod ultricies.
		</p-->

		<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
			<tr><th>Recipient</th>
				<th>Status</th></tr>

			{if !count($RECIPIENTS)}
			<tr><td colspan=2>No Recipients</td></tr>
			{/if}

			{foreach $RECIPIENTS as $RECIPIENT}
			<tr><td>{$RECIPIENT.name}<br /><em>{$RECIPIENT.email}</em></td>
				<td><span style="color:{if $RECIPIENT.status == 0}blue{/if}{if $RECIPIENT.status == 1}green{/if}{if $RECIPIENT.status == 2}red{/if}">
					{$STATUS[ $RECIPIENT.status ]}</span><br />
					<span style="color:{if $RECIPIENT.open == 0}blue{/if}{if $RECIPIENT.open == 1}green{/if}{if $RECIPIENT.open == 2}red{/if}">
					{$OPENED[ $RECIPIENT.open ]}</span></td></tr>
			{/foreach}

		</table>

	</div>

	<div class="tab" id="tab_report">

		<h4>Campaign Results</h4>

		<div style="margin-bottom:10px;">
			<label>Processed</label>
			<div id="pb-proc">
				<span class="ui-progressbar-label">{$STATS.processed}%</span>
			</div>
		</div>

		<div style="margin-bottom:10px;">
			<label>Sent</label>
			<div id="pb-sent">
				<span class="ui-progressbar-label">{$STATS.sent}%</span>
			</div>
		</div>

		<div style="margin-bottom:10px;">
			<label>Errors</label>
			<div id="pb-errs">
				<span class="ui-progressbar-label">{$STATS.errors}%</span>
			</div>
		</div>

		<div style="margin-bottom:10px;">
			<label>Bounced</label>
			<div id="pb-bounced">
				<span class="ui-progressbar-label">{$STATS.bounced}%</span>
			</div>
		</div>

		<div style="margin-bottom:10px;">
			<label>Opened</label>
			<div id="pb-open">
				<span class="ui-progressbar-label">{$STATS.open}%</span>
			</div>
		</div>

		<div style="margin-bottom:10px;">
			<label>Clicked</label>
			<div id="pb-clicked">
				<span class="ui-progressbar-label">{$STATS.errors}%</span>
			</div>
		</div>

	</div>
</div>

<style type="text/css">
.ui-progressbar {
  width: 100%;
  height: 20px;
}

.ui-progressbar-value {
  background: url(/admin/filemanager/scripts/jquery.uploader/pb-red.png);
  height: 20px;
  text-align:center;
  color:#FFF;
  line-height:20px;
  font-weight:normal;
  font-size:10px;
}

.ui-progressbar-label {
	position:absolute;
	margin-left:5px;
	line-height:20px;
	color:#FFF;
	text-shadow:none;
}

#pb-proc .ui-progressbar-value {
	background: url(/admin/filemanager/scripts/jquery.uploader/pb-blue.png);
}

#pb-sent .ui-progressbar-value {
	background: url(/admin/filemanager/scripts/jquery.uploader/pb-green.png);
}

#pb-errs .ui-progressbar-value {
	background: url(/admin/filemanager/scripts/jquery.uploader/pb-red.png);
}

.ui-progressbar {
	border:1px solid #666;
	background:url(/admin/filemanager/scripts/jquery.uploader/pb-gray.png);
}
</style>

<script type="text/javascript">

	$(function() {
		$( "#pb-proc" ).progressbar({
			value: {print intval($STATS.processed)}
		});
		$( "#pb-sent" ).progressbar({
			value: {print intval($STATS.sent)}
		});
		$( "#pb-errs" ).progressbar({
			value: {print intval($STATS.errors)}
		});
		$( "#pb-bounced" ).progressbar({
			value: {print intval($STATS.bounced)}
		});
		$( "#pb-open" ).progressbar({
			value: {print intval($STATS.open)}
		});
		$( "#pb-clicked" ).progressbar({
			value: {print intval($STATS.clicked)}
		});

		{if $STATS.processed < 100}

		setInterval(function(){
			//
			$.get('{print Request::$URL}/stats/{print Request::URL('id')}', function(data) {
				//
				data = $.parseJSON(data);

				$( "#pb-proc" ).progressbar( "value" , data.processed );
				$( "#pb-sent" ).progressbar( "value" , data.sent );
				$( "#pb-errs" ).progressbar( "value" , data.errors );
				$( "#pb-bounced" ).progressbar( "value" , data.bounced );
				$( "#pb-open" ).progressbar( "value" , data.open );
				$( "#pb-clicked" ).progressbar( "value" , data.clicked );

				$( "#pb-proc .ui-progressbar-label" ).html( data.processed + '%' );
				$( "#pb-sent .ui-progressbar-label" ).html( data.sent + '%' );
				$( "#pb-errs .ui-progressbar-label" ).html( data.errors + '%' );
				$( "#pb-bounced .ui-progressbar-label" ).html( data.bounced + '%' );
				$( "#pb-open .ui-progressbar-label" ).html( data.open + '%' );
				$( "#pb-clicked .ui-progressbar-label" ).html( data.clicked + '%' );

			});
		}, 1000);

		{/if}

	});

</script>