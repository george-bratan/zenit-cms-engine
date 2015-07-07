
	<form id="form" class="ajax" action="{print Request::$URL}/save/{$RECIPIENTLIST.id}" method="post" enctype="multipart/form-data">

        <div class="left" style="width:60%; height:500px;">


			<div id="recipientlist" style="max-height:500px; overflow-x:hidden; overflow-y:auto;">

				<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
					<tr><th>Recipient</th>
						<th>Email</th></tr>

					{if isset($FEED)}
						{if !count($FEED.RESULT)}
							<tr><td colspan=2>No Recipients Found</td></tr>
						{/if}

						{foreach $FEED.RESULT as $RECIPIENT}
							<tr><td><strong>{$RECIPIENT.name}</strong><br />
									<em>{$RECIPIENT.other}</em></td>
								<td>{$RECIPIENT.email}</td></tr>

								<!--
								<td nowrap style="text-align:center;">
									<a rel="modal" id="edit_{$item.id}" class="" href="{print Request::$URL .'/recipient/details/'. $item.id}">
										<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png" alt="Edit Recipient" class="help" title="Edit Recipient"/>
									</a>&nbsp;
									<a rel="modal" id="delete_{$item.id}" class="" href="{print Request::$URL .'/recipient/delete/'. $item.id}">
										<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png" alt="Delete Recipient" class="help" title="Delete Recipient"/>
									</a>&nbsp;
								</td>
								-->

						{/foreach}
					{else}
						<tr><td colspan=2>No Recipient Feed Selected</td></tr>
					{/if}

				</table>

			</div>

		</div>

		<div class="right" style="width:39%">

			<table class="data ignore" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th colspan=2>Filters</th></tr>

				<tr><td colspan=2>
					<select name="VALUES[feed]" id="feed" style="width:100%;">
                        <option value="0" style="font-weight:bold; font-style:normal;">-</option>

						{foreach $FEEDS as $MODULE => $feeds}
						<optgroup label="{$MODULE}" style="font-weight:bold; font-style:normal;">
							{foreach $feeds as $key => $feed}
								{if $key == $RECIPIENTLIST.feed}
									<option value="{$key}" style="font-weight:bold; font-style:normal;" selected>{$feed}</option>
								{else}
									<option value="{$key}" style="font-weight:bold; font-style:normal;">{$feed}</option>
								{/if}
							{/foreach}
						</optgroup>
						{/foreach}

					</select>
					</td></tr>

				<tr><td colspan=2>
						<p id="filters">
						{if isset($FEED)}
							{if count($FEED.FILTERS)}
								{foreach $FEED.FILTERS as $key => $input}

									{php $value = isset($RECIPIENTLIST.filters[ $input->name ]) ? $RECIPIENTLIST.filters[ $input->name ] : ''}

									{print $input->Context('FILTERS')->Render()}

								{/foreach}
							{else}
								<em>No Filters Available</em>
							{/if}
						{else}
							<em>Select a Recipient Feed</em>
						{/if}
						</p>
						</td></tr>

				<tr><td colspan=2>
					<input type="button" id="btn_refresh" value="Filter Recipients"></td></tr>

			</table>

		</div>

		<br class="clear">

	</form>



	<style type="text/css" media="screen">

	#recipient-container tr th {
		background: none repeat scroll 0 0 #EEEEEE;
    	font-weight: bold;
    	border-bottom: 1px solid #CCCCCC;
    	xpadding: 7px;
	}
	#editor_container tr td {
    	padding: 7px 0;
    	padding-bottom:9px;
    	border-bottom: 1px solid #CCCCCC;
	}
	</style>


	<script type="text/javascript">

 	$(document).ready(function(){

 		$('#btn_save').click(function(e){
 			//
 			$.fancybox.showActivity();
 			$('#form').attr('action', '{print Request::$URL}/save/{$RECIPIENTLIST.id}').submit();

			return false;
 		});

 		$('#btn_refresh').click(function(e){
 			//
 			$.fancybox.showActivity();
 			$('#form').attr('action', '{print Request::$URL}/refresh/{$RECIPIENTLIST.id}').submit();

			return false;
 		});

 		$('#feed').change(function(){
 			//
 			$.fancybox.showActivity();
 			$('#form').attr('action', '{print Request::$URL}/feed/{$RECIPIENTLIST.id}').submit();
 		});

 	});

 	</script>