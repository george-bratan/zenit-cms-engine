
	<form id="form" name="article" class="ajax" action="{print Request::$URL}/files/{$ARTICLE.id}" method="post" enctype="multipart/form-data">

        <div class="left">
        	<textarea id="article-body" name="VALUES[body]" class="wysiwyg">{$ARTICLE.body}</textarea>

        	<div id="gallery" class="gallery" style="margin-top:20px">
	        	<div class="main_image">
				    {php $IMAGE = count($ARTICLE.IMAGES) ? $ARTICLE.IMAGES[0] : FALSE}
				    <img class="preview" src="{if $IMAGE}{$CONF.WWW.UPLOAD}{$IMAGE.disk}{/if}" alt="" />
				    <div class="desc">
				        <!--a href="#" class="collapse">Close Me!</a-->
				        <div class="block">

				            {if $IMAGE}
				            <div class="edit">
				            	<a rel="modal" href="{print Request::$URL}/file/{$IMAGE.id}">
				            		<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png"></a>
				            	<br />
				            	<a rel="modal" href="{print Request::$URL}/detach/{$IMAGE.id}">
					            	<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png"></a>
				            </div>
				            {/if}

				            <h2>{if $IMAGE}{$IMAGE.title}{/if}</h2>
				            <div class="small">{if $IMAGE}{$IMAGE.name}{/if}</div>
				            <div class="large">{if $IMAGE}{$IMAGE.description}{/if}</div>
				        </div>
				    </div>
				</div>
				<div class="image_thumb">
				    <div class="upload">
				    	<input id="files" name="files[]" type="file" multiple style="opacity:0; position:absolute; left: -9999px;" />
				    	<input id="upload-button" type="button" value="Upload Files">
				    </div>
				    <ul>
				        {foreach $ARTICLE.IMAGES as $IMAGE}
				        <li>
				            <input type="hidden" name="items[]" value="{$IMAGE.id}">
				            <a href="{$CONF.WWW.UPLOAD}{$IMAGE.disk}"><img class="thumb" src="{$CONF.WWW.UPLOAD}{$IMAGE.disk}" alt="{$IMAGE.name}" /></a>
				            <div class="block">
				                <div class="edit">
					            	<a rel="modal" href="{print Request::$URL}/file/{$IMAGE.id}">
					            		<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/edit.png"></a>
					            	<br />
					            	<a rel="modal" href="{print Request::$URL}/detach/{$IMAGE.id}">
					            		<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/delete.png"></a>
					            	<br />
					            	<img class="move" src="{$CONF.WWW.ROOT}/admin/images/icon.small/move.png">
					            </div>
				                <h2>{$IMAGE.title}</h2>
				                <div class="small">{$IMAGE.name}</div>
				                <div class="large">{$IMAGE.description}</div>
				            </div>
				        </li>
				        {/foreach}

				        <li>
				        	<div class="fileupload-content">
						        <table class="files"></table>
						    </div>
				        </li>
				    </ul>
				</div>
				<br class="clear">
			</div>
        </div>

        <div class="right">
			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th><label>Article</label></th>
					<th>#{$ARTICLE.id}</th></tr>

				<tr><td><label>Title</label></td>
					<td><input type="text" name="VALUES[title]" value="{$ARTICLE.title}" style="width:300px"/></td></tr>
				<tr><td><label>Keywords</label></td>
					<td><input type="text" name="VALUES[keywords]" value="{$ARTICLE.keywords}" style="width:300px"/></td></tr>
				<tr><td><label>Category</label></td>
					<td><select name="VALUES[idcategory]" style="width:312px;">
							{foreach $CATEGORIES as $id => $category}
							<option value="{$id}" {if $ARTICLE.idcategory == $id}selected{/if}>{$category}</option>
							{/foreach}
						</select></td></tr>

				<tr><td><label>Publication Date</label></td>
					<td><input type="text" class="datepicker" style="width:300px" name="VALUES[pubdate]" value="{$ARTICLE.pubdate}" /></td></tr>
				<tr><td><label>Start Date</label></td>
					<td><input type="text" class="datepicker" style="width:300px" name="VALUES[startdate]" value="{$ARTICLE.startdate}" /></td></tr>
				<tr><td><label>End Date</label></td>
					<td><input type="text" class="datepicker" style="width:300px" name="VALUES[enddate]" value="{$ARTICLE.enddate}" /></td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>

				<tr><td><label>Status</label></td>
					<td><span style="color:{print ($ARTICLE.status == 0 ? 'red' : ($ARTICLE.status == 1 ? 'green' : 'black'))}">
							{$STATUS[ $ARTICLE.status ]}</span>
							{foreach $STATUS as $id => $status}
							{if $id != $ARTICLE.status}
								<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="Set to: {$STATUS[ $id ]}" href="{print Request::$URL}/status/{$ARTICLE.id}" data="status={$id}">
							{/if}
							{/foreach}
						</td></tr>
				<tr><td><label>Comments</label></td>
					<td>{if $ARTICLE.cancomment}
							<span style="color:green">Enabled</span>
							<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="Disable" href="{print Request::$URL}/cancomment/{$ARTICLE.id}" data="status=0">
						{else}
							<span style="color:red">Disabled</span>
							<input class="btn_status" style="margin-left:10px; float:right;" type="button" value="Enable" href="{print Request::$URL}/cancomment/{$ARTICLE.id}" data="status=1">
						{/if}
						</td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>

				<tr><th colspan=2>
					Contact Details</th></tr>

				<tr><td><label>Contact Name</label></td>
					<td>{if $ARTICLE.contact}{$ARTICLE.contact}{else}-{/if}</td></tr>
				<tr><td><label>Email</label></td>
					<td>{if $ARTICLE.email}<a href="mailto:{$ARTICLE.email}">{$ARTICLE.email}</a>{else}-{/if}</td></tr>
				<tr><td><label>Phone</label></td>
					<td>{if $ARTICLE.phone}{$ARTICLE.phone}{else}-{/if}</td></tr>
				<tr><td><label>Website</label></td>
					<td>{if $ARTICLE.url}<a href="{$ARTICLE.url}">{$ARTICLE.url}</a>{else}-{/if}</a></td></tr>
				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Contact Details" rel="modal" href="{print Request::$URL}/contact/{$ARTICLE.id}"></td></tr>

				<tr><td colspan=2>&nbsp;</td></tr>

				<tr><th colspan=2>
					Location Details</th></tr>

				<tr><td><label>Venue</label></td>
					<td>{if $ARTICLE.location}{$ARTICLE.location}{else}-{/if}</a></td></tr>
				<tr><td><label>Address</label></td>
					<td>{if $ARTICLE.address}{print nl2br($ARTICLE.address)}{else}-{/if}</a>
						{if $ARTICLE.coords || $ARTICLE.address}
						<div class="toolbar" style="padding:0; margin:0;"><a id="map-button" style="float:right;">Map &raquo;</a></div>
						{/if}
						</td></tr>
				<tr id="map-container"><td style="vertical-align:top"><label>Latitude</label><br><span id="lat">-</span><br><br><label>Longitude</label><br><span id="lng">-</span></td>
					<td><div id="map"></div></td></tr>

				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Setup Location" rel="modal" href="{print Request::$URL}/location/{$ARTICLE.id}"></td></tr>

			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Color</th>
					<th style="width:100%">Label</th></tr>

				{if !count($ARTICLE.LABELS)}
				<tr><td colspan=2>
					No Labels</td></tr>
				{/if}

				{foreach $ARTICLE.LABELS as $label}
				<tr><td nowrap>
					<span style="margin:auto; display:block; width:12px; height:12px; border:1px solid black; background-color:#{$label.color};"></span></td>
					<td>{$label.name}</td></tr>
				{/foreach}

				<tr><td colspan=2>
					<input class="btn_modal" style="float:right;" type="button" value="Setup Labels" rel="modal" href="{print Request::$URL}/labels/{$ARTICLE.id}">
					</td></tr>
			</table>

			<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap>Title</th>
					<th>&nbsp;</th>
					<th style="width:100%">URL</th></tr>

				{if !count($ARTICLE.LINKS)}
				<tr><td colspan=3>
					No Links</td></tr>
				{/if}

				{for $i = 0; $i < 5; $i++}
				{if isset($ARTICLE.LINKS[ $i ])}
					<tr><td nowrap>{$ARTICLE.LINKS[$i].title}</td>
						<td nowrap>&nbsp; &raquo; &nbsp;</td>
						<td style="width:100%"><a href="{$ARTICLE.LINKS[$i].url}">{$ARTICLE.LINKS[$i].url}</a></td></tr>
				{/if}
				{/for}

				<tr><td colspan=3>
					<input class="btn_modal" style="float:right;" type="button" value="Setup Links" rel="modal" href="{print Request::$URL}/links/{$ARTICLE.id}">
					</td></tr>
			</table>

		</div>

		<br class="clear">

	</form>

	<style type="text/css">
	#article-body {
		height:500px;
		width:100%;
	}
	#article-other {
		height:150px;
		width:100%;
	}
	#map {
		width:100%;
		height:300px;
		background:gray;
	}
	#map-container td {
		display:none;
	}

	table.files {
		width:100%;
	}
	table.files td.preview a {
		display:block;
		width:84px;
	}
	table.files td.preview, table.files td.error, table.files td.progress {
		float:left;
	}
	table.files td.error, table.files td.progress {
		padding:12px 0;
	}
	table.files td.delete, table.files td.start, table.files td.cancel {
		display:none;
	}

	.fileupload-content .ui-progressbar {
	  width: 120px;
	  height: 16px;
	  border:1px solid #666;
	  background:url(/admin/images/progress.gray.png);
	}
	.fileupload-content .ui-progressbar-value {
	  background: url(/admin/images/progress.red.png);
	  height: 16px;
	  text-align:center;
	  color:#FFF;
	  line-height:14px;
	  font-weight:normal;
	  font-size:10px;
	}
	.fileupload-content canvas {
	    border: 1px solid #ccc;
	    padding: 5px;
	    background: #fff;
	    float: left;
	    width: 50px;
	    height:38px;
	    margin: 12px 10px;
	}
	</style>

	<!--
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	-->

	<script src="{$CONF.WWW.ROOT}/admin/filemanager/scripts/jquery.uploader/jquery.iframe-transport.js"></script>
	<script src="{$CONF.WWW.ROOT}/admin/filemanager/scripts/jquery.uploader/jquery.fileupload.js"></script>
	<script src="{$CONF.WWW.ROOT}/admin/filemanager/scripts/jquery.uploader/jquery.fileupload-ui.js"></script>

	<script type="text/javascript" src="//maps.google.com/maps?file=api&v=2&key={$CONF.MAPS.KEY}"></script>
	<script type="text/javascript" src="{$CONF.WWW.ROOT}/admin/js/jquery.gmaps.js"></script>
	<script type="text/javascript" src="{$CONF.WWW.ROOT}/admin/js/jquery.dragsort.js"></script>

	<script type="text/javascript">

 	$(document).ready(function(){

 		$('#btn_save').click(function(e){
 			//
 			e.preventDefault();

 			$('#form').attr('action', $(this).attr('href'));
 			$('#form').submit();
 		});

 		$('#gallery').gallery({
 			onchange: function(){
 				//
 				$('.main_image div.edit a[rel=modal]').fancybox({
					padding: 0,
					titleShow: false,
					overlayColor: '#333333',
					overlayOpacity: .5,
					showNavArrows: false,
					disableNavButtons: false,
					// href: '',
					onComplete: function(){

						if ($('#popup').height() >  $('#fancybox-inner').height() - 30 - 33 ) {
							$('#popup').height( $('#fancybox-inner').height() - 30 - 33 );
						}

						$('.modal_content .wysiwyg').wysiwyg({
					   		css : $('#ROOT').val() + "/admin/css/wombat.wysiwyg.css"
					   	});

						$('.modal_content .wysiwyg iframe').each(function(){
							$(this).css( { minHeight: parseInt($(this).css('min-height')) - 28 + 'px', height: $(this).height() - 28 + 'px' } );
						});
					}
				});
 			}
 		});

 		$("#gallery .image_thumb ul").dragsort({
 			dragSelector: 'li img.move',
 			dragEnd: function(){
 				//
 				//var order = $("#gallery .image_thumb ul li").map(function() { return $(this).find('input.imageid').val(); }).get();
 				$.fancybox.showActivity();
 				$.ajax({
					type: 'POST',
					url: '{print Request::$URL}/reorder/{$ARTICLE.id}',
					data: $('#gallery .image_thumb ul li input').serialize(),
					success: function(a){
						if (a.responseText) {
							//
							$.fancybox(a.responseText, {
								padding: 0
							});
						}
						else {
							//window.location.reload();
							//window.location = window.location;
							$.fancybox.hideActivity();
						}
					},
					error: function(a){
						$.fancybox.hideActivity();

						//if (a.status == 403) {
						if (true) {
							//
							$.fancybox(a.responseText, {
								padding: 0
							});
						}
					}
				});
 			}
 		});

 		$('#upload-button').unbind().click(function(){
			//
			$('#files').click();
		});

 		$('#gallery').fileupload({
		    url: $('#article').attr('action'),
		    sequentialUploads: true,
		    previewMaxWidth: 50,
		    previewMaxHeight: 50
		}).bind('fileuploadadd', function (e, data) {
			//
			data.autoUpload = true;

		}).bind('fileuploadstop', function (e, data) {
			//
			$.fancybox.showActivity();

			setTimeout(function(){
				window.location.reload();
			}, 200);
		});


		gmap = false;
		$('#map-button').click(function(){
			//
			if ($('#map-container td:first').css('display') == 'none') {
				//
				$('#map-container td').slideDown(function(){
					if (!gmap) {
						//
						$("#map").gMap({
							markers: [{
								address: '{if $ARTICLE.coords}{$ARTICLE.coords}{else}{print str_replace(array("\r", "\n"), ' ', $ARTICLE.address)}{/if}',
								html: "_address",
								//latitude: '',
								//longitude: '',
								//popup: true,
								//draggable: true,
								//ondrop: function( value ) { $('#frm_').val( value ); }
								onlatlng: function( latlng ) { $('#lat').html( latlng.lat() ); $('#lng').html( latlng.lng() ); }
							}],
							//address: '{print str_replace(array("\r", "\n"), ' ', $ARTICLE.address)}',
							zoom: 8
						});

						gmap = true;
					}
				});
				$('#map-button').html("&laquo Map");
			}
			else {
				//
				$('#map-container td').slideUp();
				$('#map-button').html("Map &raquo;");
			}
		});


 		$('.btn_modal').click(function(){

 			$.fancybox({
				padding: 0,
				titleShow: false,
				overlayColor: '#333333',
				overlayOpacity: .5,
				showNavArrows: false,
				disableNavButtons: false,
				href: $(this).attr('href')
			});

 		});

 		$('.btn_link').click(function(){

 			$.fancybox.showActivity();
 			window.location = $(this).attr('href');

 		});

 		$('.btn_status').click(function(e){

 			e.preventDefault();
 			$.fancybox.showActivity();

 			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				data: $(this).attr('data') ? $(this).attr('data') : ($(this).attr('serialize') ? $('#' + $(this).attr('serialize')).serialize() : ''),
				success: function(responseText){
					if (responseText) {
						//
						$.fancybox(responseText, {
							padding: 0
						});
					}
					else {
						//window.location.reload();
						window.location = window.location;
					}
				},
				error: function(a){
					$.fancybox.hideActivity();

					//if (a.status == 403) {
					if (true) {
						//
						$.fancybox(a.responseText, {
							padding: 0
						});
					}
				}
			});

 		});

 	});

 	</script>