
<div style="min-width:600px">
	<form action="{$URL}" class="save" method="post" enctype="multipart/form-data" onsubmit="return checkForFile();">
	<input type="hidden" name="token" value="{$TOKEN}"/>

		<table class="data" width="100%" cellspacing="0" cellpadding="0">
			<tr><th colspan=2>Video Properties</th></tr>

			<tr><td>Title</td>
				<td>{$VIDEO.title}</td></tr>
			<tr><td>Description</td>
				<td>{$VIDEO.description}</td></tr>
			<tr><td>Keywords</td>
				<td>{$VIDEO.keywords}</td></tr>
			<tr><td>category</td>
				<td>{$VIDEO.category}</td></tr>

		</table>

		<p style="height:200px">
			<br class="clear">
			<br class="clear">
			Please make sure you verify your YouTube! account if you want to upload a file longer than 15 minutes.<br />
			You can confirm your account ownership <a href="http://www.youtube.com/my_videos_upload_verify" target="_blank">here</a>.
			<br class="clear">
			<br class="clear">
			<label>Video File</label><br>
			<input id="file" type="file" name="file" />
		</p>

		<br class="clear">

  		<p>
			<input type="button" value="  Upload  " style="float:right" onclick="$('form.save').submit();"/>
			<input type="button" value="  Cancel  " onclick="window.close();"/>
		</p>

	</form>
</div>

<script type="text/javascript">
  function checkForFile() {
    if (document.getElementById('file').value) {
      return true;
    }
    alert('Please select a video file for upload.');
    return false;
  }
</script>