
	<form id="form" class="ajax" action="{print Request::$URL}/save/{$BLOCK.id}" method="post" enctype="multipart/form-data">

        <div class="left" style="width:49%">
			<table id="editor_container" style="width:100%" cellpadding="0" cellspacing="0">
				<tr><th nowrap style="padding:7px 6px;"><div style="float:left">Layout</div></th>
					<th nowrap>
						<div class="toolbar" id="editor_tabs" style="margin:0; padding:0; top:4px; margin-right:5px; position:relative;">
							<a id="btn_rich" tab="tab_rich" class="left" href="javascript:void(0);" rel="tab">Rich Editor</a>
							<a id="btn_html" tab="tab_html" class="middle" href="javascript:void(0);" rel="tab">HTML</a>
							<a id="btn_css" tab="tab_css" class="middle" href="javascript:void(0);" rel="tab">CSS</a>
							<a id="btn_js" tab="tab_js" class="right" href="javascript:void(0);" rel="tab">JS</a>
						</div></th></tr>

				<tr><td colspan=2 style="padding-top:0">

					<input type="hidden" id="html" name="VALUES[html]" value="{print Util::htmlencode($BLOCK.html)}">
					<input type="hidden" id="css" name="VALUES[css]" value="{print Util::htmlencode($BLOCK.css)}">
					<input type="hidden" id="js" name="VALUES[js]" value="{print Util::htmlencode($BLOCK.js)}">

					<div id="tab_rich" style="width:99%">
						<textarea id="layout_rich" style="" class="wysiwyg">{$BLOCK.html}</textarea>
					</div>

					<div id="tab_html">
						<pre id="layout_html">{print Util::htmlencode($BLOCK.html)}</pre>
					</div>

					<div id="tab_css">
						<pre id="layout_css">{print Util::htmlencode($BLOCK.css)}</pre>
					</div>

					<div id="tab_js">
						<pre id="layout_js">{print Util::htmlencode($BLOCK.js)}</pre>
					</div>

					</td></tr>

			</table>
		</div>

		<div class="right" style="width:50%;">

			<div class="left" style="width:48%;">
				<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
					<tr><th>Block Name</th>
						<th>{$BLOCK.name}</th></tr>

					<tr><td colspan=2>
							<select name="VALUES[feed]" id="feed" style="width:100%;">
							<option value="0" style="font-weight:bold; font-style:normal;">-</option>

								{foreach $FEEDS as $MODULE => $feeds}
								<optgroup label="{$MODULE}" style="font-weight:bold; font-style:normal;">
									{foreach $feeds as $key => $feed}
										{if $key == $BLOCK.feed}
											<option value="{$key}" style="font-weight:bold; font-style:normal;" selected>{print is_array($feed) ? $feed.title : $feed}</option>
										{else}
											<option value="{$key}" style="font-weight:bold; font-style:normal;">{print is_array($feed) ? $feed.title : $feed}</option>
										{/if}
									{/foreach}
								</optgroup>
								{/foreach}

							</select>
							</td></tr>

					<tr><td colspan=2>&nbsp;</td></tr>
					<tr><th colspan=2>Feed Properties</th></tr>

					<tr><td colspan=2>
							{if isset($FEED)}
								<select id="properties" style="width:100%; height:385px;" multiple="true">
									{if isset($FEED.DEFAULT)}
										<option value="{print Util::htmlencode($FEED.DEFAULT)}">Default Template</option>
									{/if}
									{foreach $FEED.PROPERTIES as $key => $value}
										<option value="{$key}">{$value}</option>
									{/foreach}
								</select>
							{else}
								<em>No Data Feed</em>
							{/if}
							</td></tr>

				</table>
			</div>

			<div class="right" style="width:48%;">
				<table class="data" style="width:100%" cellpadding="0" cellspacing="0">
					<tr><th colspan=2>Preset Filters</th></tr>

					<tr><td colspan=2>
							<p id="filters">
							{if isset($FEED)}
								{if count($FEED.FILTERS)}
									{foreach $FEED.FILTERS as $key => $input}

										{php $value = isset($BLOCK.filters[ $input->name ]) ? $BLOCK.filters[ $input->name ] : ''}

										{print $input->Context('FILTERS')->Value( $value )->Render()}

									{/foreach}
								{else}
									<em>No Filters Available</em>
								{/if}
							{else}
								<em>No Data Feed</em>
							{/if}
							</p>
							</td></tr>

					{if isset($FEED.HINT)}
					<tr><td colspan=2>
						{$FEED.HINT}
						</td></tr>
					{/if}

				</table>
			</div>

		</div>

		<br class="clear">

	</form>


	<script src="{$CONF.WWW.ROOT}/admin/ace/ace.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/theme.eclipse.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/mode.html.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/mode.css.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/mode.javascript.js" type="text/javascript" charset="utf-8"></script>


	<style type="text/css" media="screen">
	#layout_html,#layout_css, #layout_js {
		margin: 0;
		height: 500px;
		width: 100%;
		position: relative;
	}
	#layout_rich {
		height: 463px;
		width: 100%;
	}
	#editor_container tr th {
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

		editor_html = ace.edit("layout_html");
	    editor_html.setTheme("ace/theme/eclipse");

		var HTMLMode = require("ace/mode/html").Mode;
		editor_html.getSession().setMode(new HTMLMode());

		editor_css = ace.edit("layout_css");
	    editor_css.setTheme("ace/theme/eclipse");

		var CSSMode = require("ace/mode/css").Mode;
		editor_css.getSession().setMode(new CSSMode());

		editor_js = ace.edit("layout_js");
	    editor_js.setTheme("ace/theme/eclipse");

		var JSMode = require("ace/mode/javascript").Mode;
		editor_js.getSession().setMode(new JSMode());

		var lastactive = '';

		$('#btn_rich').click(function(){
			//
			//if ($('#layout_rich').val() != editor_html.getSession().getValue())
			$('#layout_rich').wysiwyg('setContent', editor_html.getSession().getValue());
			lastactive = 'rich';

			$('#layout_richIFrame').focus();
		});

		$('#btn_html').click(function(){
			//
			var content = $('#layout_rich').val().replace(/%7B/g, '{').replace(/%7D/g, '}').replace(/%20/g, ' ').replace(/%28/g, '(').replace(/%29/g, ')').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

			editor_html.getSession().setValue( content );
			lastactive = 'html';

			editor_html.focus();
		});

		$('#btn_css').click(function(){
			//
			editor_css.focus();
		});

		$('#btn_js').click(function(){
			//
			editor_js.focus();
		});

		$.z_toolbars('#editor_tabs');


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

 		$('.btn_status, .btn_move').click(function(){

 			$.fancybox.showActivity();

 			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				data: $(this).attr('data') ? $(this).attr('data') : ($(this).attr('serialize') ? $( $(this).attr('serialize') ).serialize() : ''),
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

 		$('#form').submit(function(){
 			//
 			if ($('#btn_rich').is('.active') || lastactive == 'rich') {
 				//
 				$('#html').val( $('#layout_rich').val() );
 			}

 			if ($('#btn_html').is('.active') || lastactive == 'html') {
 				//
 				$('#html').val( editor_html.getSession().getValue() );
 			}

 			$('#css').val( editor_css.getSession().getValue() );
 			$('#js').val( editor_js.getSession().getValue() );
 		});

 		$('#btn_save').click(function(e){
 			//
 			$.fancybox.showActivity();
 			$('#form').attr('action', '{print Request::$URL}/save/{$BLOCK.id}').submit();

			return false;
 		});

 		$('#feed').change(function(){
 			//
 			$.fancybox.showActivity();
 			$('#form').attr('action', '{print Request::$URL}/feed/{$BLOCK.id}').submit();
 		});

 		$('#properties').dblclick(function(){
 			//
 			var content = $('#properties').val() + '';
 			var content = (content[0] == '{') ? content : '{'+'$'+ content +'}';

 			if ($('#btn_rich').is('.active')) {
 				//
 				$('#layout_rich').wysiwyg('insertHtml', content);
 				$('#layout_richIFrame').focus();
 			}

 			if ($('#btn_html').is('.active')) {
 				//
 				editor_html.getSession().setUseWrapMode(true);
 				editor_html.insert( content );
 				editor_html.getSession().setUseWrapMode(false);
 			}
 		});

 	});

 	</script>