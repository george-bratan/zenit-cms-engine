


	<form id="preview_form" action="{print Request::$URL}/preview/{$TEMPLATE.id}?r={print rand(999,9999)}" method="post" target="editor_preview">
		<input type="hidden" id="action" name="action" value="preview" />
		{foreach $TEMPLATE.ELEMENTS as $id => $content}
		<input type="hidden" id="html_{$id}" name="html[{$id}]" value="{print Util::htmlencode($content)}" />
		{/foreach}
		<input type="hidden" id="css" name="css" value="{print Util::htmlencode($TEMPLATE.CONTENT.css)}" />
		<input type="hidden" id="js" name="js" value="{print Util::htmlencode($TEMPLATE.CONTENT.js)}" />
	</form>

	<div id="tab_preview" class="tab">
		<iframe src="{print Request::$URL}/preview/{$TEMPLATE.id}" id="editor_preview" name="editor_preview">
			<p>Your browser does not support iframes.</p>
		</iframe>
	</div>

	<div id="tab_html" class="tab">

		{if !count($TEMPLATE.ELEMENTS)}
		<div class="alert_info" style="margin:20px;">
			<p>
				<img src="{$CONF.WWW.ROOT}/admin/images/icon.small/warning.png" alt="success" class="mid_align"/>
				There are no editable blocks in the starting template.
			</p>
		</div>
		{/if}

		<div class="left" style="width:150px">
		{foreach $TEMPLATE.ELEMENTS as $id => $content}
			<div id="tab_left_{$id}" class="tab_left" >{$id}</div>
		{/foreach}
		</div>

		{foreach $TEMPLATE.ELEMENTS as $id => $content}
		<div id="editor_container_{$id}" class="editor_container">
			<pre id="editor_html_{$id}">{print Util::htmlencode($content)}</pre>
		</div>
		{/foreach}

		<br class="clear"/>
	</div>

	<div id="tab_css" class="tab">
		<pre id="editor_css">{print Util::htmlencode($TEMPLATE.CONTENT.css)}</pre>
	</div>

	<div id="tab_js" class="tab">
		<pre id="editor_js">{print Util::htmlencode($TEMPLATE.CONTENT.js)}</pre>
	</div>

	<script src="{$CONF.WWW.ROOT}/admin/ace/ace.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/theme.eclipse.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/mode.html.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/mode.css.js" type="text/javascript" charset="utf-8"></script>
	<script src="{$CONF.WWW.ROOT}/admin/ace/mode.javascript.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript">

	//var editor_html, editor_css;

	var editor_html = new Array();

	$(document).ready(function() {

	    {if count($TEMPLATE.ELEMENTS)}
		    {php $keys = array_keys($TEMPLATE.ELEMENTS)}
			$.cookie("ADMIN.TABS.HTMLEDITOR", 'tab_left_{$keys[0]}');
		{/if}

		$('#btn_more, #btn_hide').click(function(){
			//
			if ($('div.column_left').is('div.column_large')) {
				//
				$('div.column_right').css('display', 'none');
				$('div.column_left').removeClass('column_large').addClass('column_full');

				$('#btn_more').html(' &laquo; More ');
				$.cookie("ADMIN.CMS.TEMPLATES.COLLAPSED", 'TRUE');
			}
			else {
				//
				$('div.column_right').css('display', '');
				$('div.column_left').removeClass('column_full').addClass('column_large');

				$('#btn_more').html(' Hide &raquo; ');
				$.cookie("ADMIN.CMS.TEMPLATES.COLLAPSED", 'FALSE');
			}
		});

		if ($.cookie("ADMIN.CMS.TEMPLATES.COLLAPSED") == 'TRUE') {
			//
			$('#btn_more').click();
		}


	    $('#btn_preview, #btn_save').click(function(){
			//
			{foreach $TEMPLATE.ELEMENTS as $id => $content}
			$('#html_{$id}').val( editor_html['{$id}'].getSession().getValue() );
			{/foreach}

			$('#css').val( editor_css.getSession().getValue() );
			$('#js').val( editor_js.getSession().getValue() );

			if (true) {
				//
				$.fancybox.showActivity();

				if ($(this).is('#btn_preview')) {
					$('#preview_form input#action').val('preview');
				}
				if ($(this).is('#btn_save')) {
					$('#preview_form input#action').val('save');
				}

				$('#preview_form').submit();
			}
		});

		{foreach $TEMPLATE.ELEMENTS as $id => $content}
		$('#tab_left_{$id}').click(function(){
			//
			$('.editor_container').addClass('hide');
			$('#editor_container_{$id}').removeClass('hide');

			$('.tab_left').removeClass('active');
			$('#tab_left_{$id}').addClass('active');

			$.cookie("ADMIN.TABS.HTMLEDITOR", $(this).attr('id'));

			//editor_html['{$id}'].getSession().setValue( $('#html_{$id}').val() );

			editor_html['{$id}'].focus();
		});
		{/foreach}

		$('#btn_html').click(function(){
			//
			$('#'+$.cookie("ADMIN.TABS.HTMLEDITOR")).click();
		});

		$('#btn_css').click(function(){
			//
			editor_css.focus();
		});

		$('#btn_js').click(function(){
			//
			editor_js.focus();
		});


	    $('#tab_preview, #tab_html, #tab_css, #tab_js').removeClass('hide');

	    {foreach $TEMPLATE.ELEMENTS as $id => $content}
	    editor_html['{$id}'] = ace.edit("editor_html_{$id}");
	    editor_html['{$id}'].setTheme("ace/theme/eclipse");

	    editor_html['{$id}'].getSession().setValue( $('#html_{$id}').val() );
	    {/foreach}

	    editor_css = ace.edit("editor_css");
	    editor_css.setTheme("ace/theme/eclipse");

	    editor_js = ace.edit("editor_js");
	    editor_js.setTheme("ace/theme/eclipse");

	    var HTMLMode = require("ace/mode/html").Mode;
	    {foreach $TEMPLATE.ELEMENTS as $id => $content}
		editor_html['{$id}'].getSession().setMode(new HTMLMode());
		{/foreach}

		var CSSMode = require("ace/mode/css").Mode;
		editor_css.getSession().setMode(new CSSMode());

		var JSMode = require("ace/mode/javascript").Mode;
		editor_js.getSession().setMode(new JSMode());

	    $('#tab_html, #tab_css, #tab_js').addClass('hide');

	    $('#btn_preview').click();


	    /*
	    $.z_insert = function(id, content) {
	    	//
	    	//content = $.htmlClean( content, {format:true, removeTags:[]} ); //.replace(/\<br \/\>/g, '<br />' + "\n");

	    	existing = editor_html[id].getSession().getValue();
	    	if (existing.match(/<\/body>/) && id == 'CONTENT') {
	    		content = existing.replace(/<body([\s\S]*?)>([\s\S]*)<\/body>/i, '<body$1>'+content+'</body>')
	    	}

	    	//if (existing.match(/<\/body>/)) alert('aa');
	    	//if (x = existing.match(/<body([\s\S]*?)>([\s\S]*)<\/body>/i)) alert(x);

	    	editor_html[id].getSession().setValue( content );
	    }
	    */



		//
		$('#editor_preview').load(function(){
			//
			$.fancybox.hideActivity();

			$(this).animate({height: $(this).contents().height()});

			$('#editor_preview').contents().find('.z-block, .z-slot, .z-editable').each(function(){
				//
				$(this).width( $(this).parent().width() );
	    		$(this).height( $(this).parent().height() );

			});

			$('#editor_preview').contents().find('.z-editable .z-cell[id!=""]').each(function(){
				//
				//$(this).fadeTo('fast', 0.5);
			}).mouseover(function(){
				//
				//$(this).fadeTo('fast', 1.0);

			}).mouseout(function(){
				//
				//$(this).fadeTo('fast', 0.5);

			}).click(function(){
		    	//

		    	$.fancybox({
		    		padding: 0,
		    		width:950,
		    		height:560,
		    		autoDimensions:false,
		    		context: $(this),
					titleShow: false,
					overlayColor: '#333333',
					overlayOpacity: .5,
					showNavArrows: false,
					disableNavButtons: false,
					href: $('#ROOT').val() + '/admin/cms/templates/content',
					onComplete: function() {
						//
						$('input[title!=""], textarea[title!=""]').hint();

						var self = this.context; //.parents().find('.z-cell');
						var slot = self.attr('slot');

						//$('#wysiwyg').val( self.html() );
						$('#wysiwyg').val( $('#html_'+slot).val() );

						// Setup WYSIWYG editor
					    $('#wysiwyg').wysiwyg({
					    	css : window.parent.$('#ROOT').val() + "/admin/css/wombat.wysiwyg.css"
					    });

					    $.z_toolbars();

					    $('.close').click(function(){
					    	//
					    	$.fancybox.close();
					    });

					    $('#insert-article').click(function(){
					    	//
					    	//$.z_insert( slot.attr('slot'), $('#wysiwyg').val() );

					    	var content = $('#wysiwyg').val();
					    	//content = $.htmlClean( content, {format:true, removeTags:[]} ); //.replace(/\<br \/\>/g, '<br />' + "\n");

					    	$('#html_'+slot).val( content );
					    	editor_html[slot].getSession().setValue( $('#html_'+slot).val() );

							$('#btn_preview').click();

					    	$.fancybox.close();
					    });

					    savedRange = null;

					    $('#btn_image, #btn_link, #btn_other').click(function(){
					    	//
					    	savedRange = $('#wysiwygIFrame').getInternalRange();
					    });

					    $('#btn_article').click(function(){
					    	//
					    	$('#wysiwygIFrame').focus();
					    	$('#wysiwygIFrame').returnRange( savedRange );
					    	savedRange = null;
					    });

					    // IMAGE TAB

						$('#select-image').click(function(){
							//
							if ( $('#image_filemanager_container').css('display') == 'none' ) {
								//
								$('#select-image').val('Cancel');
								$('#image_filemanager_container').slideDown(400, 'swing', function(){
									//
									$('#image_filemanager').attr('src', $('#ROOT').val() + '/admin/filemanager/index.htm?filefunc=z_select_image');
								});
							}
							else {
								//
								$('#select-image').val('Select Image');
								$('#image_filemanager_container').slideUp(400, 'swing');
							}
						});

						// called above, when user selects an image from the document browser
						$.z_select_image = function(file) {
							//
							$('#image-url').val( file ).blur();
							$('#image-container').html('<img src="' + $('#UPLOAD').val() + file + '" />').find('img').load(function(){
								//
								$('#image-width').val( $(this).width() ).keyup().blur();
								$('#image-height').val( $(this).height() ).keyup().blur();
							});

							$('#image_filemanager_container').slideUp(400, 'swing');

						}

						$('#image-width').keyup(function(){
							$('#image-container').find('img').css('width', $(this).val() ? $(this).val() : 'auto');
						});
						$('#image-height').keyup(function(){
							$('#image-container').find('img').css('height', $(this).val() ? $(this).val() : 'auto');
						});

					    $('#insert-image').click(function(){
					    	//
					    	var content = $('#image-container').html();

					    	if ( $('#image-link').val() && $('#image-link').val() != 'http://') {
					    		content = '<a href="'+$('#image-link').val()+'">' + "\n\t" + $('#image-container').html() + "\n" + '</a>';
					    	}

					    	$('#btn_article').click();
					    	$('#wysiwyg').wysiwyg('insertHtml', content);
					    });


					    // LINK TAB

						$('#select-link').click(function(){
							//
							if ( $('#link_filemanager_container').css('display') == 'none' ) {
								//
								$('#select-link').val('Cancel');
								$('#link_filemanager_container').slideDown(400, 'swing', function(){
									//
									$('#link_filemanager').attr('src', $('#ROOT').val() + '/admin/filemanager/index.htm?filefunc=z_select_file');
								});
							}
							else {
								//
								$('#select-link').val('Select Document');
								$('#link_filemanager_container').slideUp(400, 'swing');
							}
						});

						// called above, when user selects an image from the document browser
						$.z_select_file = function(file) {
							//
							$('#link-url').val( $('#UPLOAD').val() + file ).blur();
							$('#link_filemanager_container').slideUp(400, 'swing');
						}

						$('#link-list').change(function(){
							//
							$('#link-url').val( $('#link-list').val() ).blur();
						});

						$('#insert-link').click(function(){
					    	//
					    	var target = $('#link-new').is(':checked') ? 'target="_blank"' : '';
					    	content = "\n" + '<a href="'+ $('#link-url').val() +'" '+ target +'>' + "\n\t" + $('#link-text').val() + "\n" + '</a>' + "\n";

					    	$('#btn_article').click();
					    	$('#wysiwyg').wysiwyg('insertHtml', content);
					    });

					    // COMPONENT TAB

					    $('#component-select').change(function(){
					    	//
					    	if ($(this).val() == 'EDITABLE') {
					    		//
					    		$('#component-editable-attr').slideDown();
					    		$('.component-hint').fadeOut();
					    	}
					    	else {
					    		//
					    		$('#component-editable-attr').slideUp();

					    		$('.component-hint').fadeOut();
					    		$('.component-hint[rel="' + $(this).val() + '"]').fadeIn();
					    	}
					    });

					    $('#insert-component').click(function(){
					    	//

					    	if ($('#component-select').val()) {
								//
								if ($('#component-select').val() == 'EDITABLE') {
									//
									if ($('#editable-ident').val()) {
										//
										content = '{EDITABLE '+$('#editable-ident').val()+'}';
									}
									else {
										//
										alert('Please type a unique block name');
									}
								}
								else {
									//
									content = '{BLOCK '+$('#component-select').val()+'}';
								}
					    	}
					    	else {
					    		//
					    		alert('Please select a component to be inserted in your page');
					    	}

					    	if (content) {
					    		//
					    		var style = '';
					    		if ($('#min-width').val() && $('#min-width').val() != $('#min-width').attr('title')) {
					    			style += 'min-width:' + $('#min-width').val() + 'px;';
					    		}
					    		if ($('#min-height').val() && $('#min-height').val() != $('#min-height').attr('title')) {
					    			style += 'min-height:' + $('#min-height').val() + 'px;';
					    		}
					    		if ($('#max-width').val() && $('#max-width').val() != $('#max-width').attr('title')) {
					    			style += 'max-width:' + $('#max-width').val() + 'px;';
					    		}
					    		if ($('#max-height').val() && $('#max-height').val() != $('#max-height').attr('title')) {
					    			style += 'max-height:' + $('#max-height').val() + 'px;';
					    		}

					    		content = '<div class="z-wysiwyg-block" style="'+style+'">'+content+'</div>';

					    		$('#btn_article').click();
					    		$('#wysiwyg').wysiwyg('insertHtml', content);
					    	}

					    });

					}
		    	});

		    	return false;
		    });

		});

	});

	</script>

	<style type="text/css" media="screen">
	{foreach $TEMPLATE.ELEMENTS as $id => $content}
	#editor_html_{$id},
	{/foreach}
	#editor_css, #editor_js, #editor_preview {
		margin: 0;
		height:600px;
		position:relative;
	}
	#editor_preview {
		width:100%;
	}

	.column_left .content {
		padding: 0;
	}
	.tab_left {
		border:1px solid #9F9F9F;
		border-right:none;
		border-top-left-radius: 10px 10px;
		border-bottom-left-radius: 10px 10px;
		margin:10px 0 10px 10px;
		padding:10px;
		cursor:pointer;
		background-color: #FFFFFF;
	}
	.tab_left.active {
		background-color: #E3E3E3;
		font-weight:bold;
	}
	.tab_left:hover {
		background-color: #FFF5D3;
	}
	</style>