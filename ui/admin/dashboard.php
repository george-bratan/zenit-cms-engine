

					<div id="chart_wrapper" class="chart_wrapper"></div>

					<!--
					<br class="clear"/>
					<div class="alert_info">
						<p>
							<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/info.png" alt="success" class="mid_align"/>
							Click on each row to update the graph.
						</p>
					</div>
					-->

					<br class="clear"/>
					<form id="form_data" name="form_data" action="" method="post">
						<table id="chart" class="data" rel="{if isset($CHART)}{$CHART}{else}area{/if}" cellpadding="0" cellspacing="0" width="100%">
						<caption>Short Overview</caption>
						<thead>
							<tr>
								<td class="no_input">&nbsp;</td>

								{foreach $REPORT as $key => $data}
									<th>{$key}</th>
								{/foreach}

							</tr>
						</thead>

						<tbody>

							{foreach $SERIES as $key => $title}
							<tr>
								<th><input id="id{$key}" type="checkbox" checked> &nbsp; <label for="id{$key}">{$title}</label></th>
								{foreach $REPORT as $date => $result}
								 <td>{if isset($result[$key])}{$result[$key]}{else}0{/if}</td>
								{/foreach}
							</tr>
							{/foreach}

						</tbody>
						</table>
						<!--div id="chart_wrapper" class="chart_wrapper"></div-->
					<!-- End bar chart table-->
					</form>
