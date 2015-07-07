
	<form id="form_data" name="form_data" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="confirm" value="true"/>

		{foreach $SETTINGS as $input}

			{print $input->Render()}

		{/foreach}

		<br/><br/>
		<p>
			<input type="submit" name="save" value="Save Settings" onclick="javascript:$('#form_data').submit();" />
		</p>

	</form>
