/*
	Global values
*/

var chartWidth = '850px';
var chartHeight = '240px';


/*
	Find element's Y axis position
*/

function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (1)
		{
			curtop+=obj.offsetTop;
			if (!obj.offsetParent)
			{
				break;
			}
			obj=obj.offsetParent;
		}
	}
	else if (obj.y)
	{
		curtop+=obj.y;
	}

	return curtop;
}

/*
	Find element's X axis position
*/

function findPosX(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (1)
		{
			curtop+=obj.offsetLeft;
			if (!obj.offsetParent)
			{
				break;
			}
			obj=obj.offsetParent;
		}
	}
	else if (obj.x)
	{
		curtop+=obj.x;
	}

	return curtop;
}

/*
	Setup chart from given table and type
*/

function setChart(tableId, type, wrapper)
{
	//clear existing chart before create new one
	$(wrapper).html('');

	var chartData = [false,false,false,false,false,false,false,false,false];
    $(tableId).find('tr:gt(0)').each(function(i){
    	$(this).removeClass('hover');
    	if ( $(this).find('input[type=checkbox]').is(':checked') )
    	{
    		$(this).addClass('hover');
    		chartData[ i ] = true;
/*
    		if ($(tableId).hasClass('double'))
    		{
	    		if (i % 2)
	    			chartData[ i-1 ] = true;
	    		else
	    			chartData[ i+1 ] = true;
	    	}
*/
    	}
    });

	$(tableId).visualize({
		type: type,
		//width: chartWidth,
		width: $(tableId).width() * 7/10,
		enableGroup: chartData,
		height: chartHeight,
		colors: ['#B11623', '#292C37', '#7EC421', '#9FBAD4', '#2A044A', '#A0C55F', '#555152', '#DCE9BE']
		//colors: ['#7EC421', '#9FBAD4'] // blue
		//colors: ['#2A044A', '#A0C55F'] // green
		//colors: ['#555152', '#DCE9BE'] // red
	}).appendTo(wrapper);

	//if IE then need to add refresh event
	if (navigator.appName == "Microsoft Internet Explorer")
	{
		$('.visualize').trigger('visualizeRefresh');
	}
}

/*
	Setup notification badges for shortcut
*/
function setNotifications()
{
	// Setup notification badges for shortcut
	$('#shortcut_notifications span').each(function() {
		if($(this).attr('rel') != '')
		{
			target = $(this).attr('rel');

			if($('#' +target).length > 0)
			{
				var Ypos = findPosY(document.getElementById(target));
				var Xpos = findPosX(document.getElementById(target));

				$(this).css('top', Ypos-24 +'px');
				$(this).css('left', Xpos+83 +'px');
			}
		}
	});
	$('#shortcut_notifications').css('display', 'block');
}

$(function(){

	// Preload images
	$.preloadCssImages();



    // Find all the input elements with title attributes and add hint to it
    $('input[title!=""],textarea[title!=""]').hint();



    /*
    // Setup WYSIWYG editor
    $('#wysiwyg').wysiwyg({
    	css : "css/wombat.wysiwyg.css"
    });
    */


    /*
    // Setup slider menu (left panel submenus)
    $('#main_menu').accordion({
			collapsible: true,
			autoHeight: false
	});
	*/


	// Setup show and hide left panel
	$('#hide_menu').click(function(){
		$('#left_menu').hide();
		$('#show_menu').show();
		$('body').addClass('nobg');
		$('#content').css('marginLeft', 30);
		//$('#wysiwyg').css('width', '97%');
		setNotifications();

		$.cookie("ADMIN.MENU.COLLAPSED", 'TRUE', { expires: null, path: '/' });
	});

	$('#show_menu').click(function(){
		$('#left_menu').show();
		$(this).hide();
		$('body').removeClass('nobg');
		$('#content').css('marginLeft', 240);
		//$('#wysiwyg').css('width', '97%');
		setNotifications();

		$.cookie("ADMIN.MENU.COLLAPSED", 'FALSE', { expires: null, path: '/' });
	});

	if ($.cookie("ADMIN.MENU.COLLAPSED") == 'TRUE') {
		//
		$('#hide_menu').click();
	}


	// Setup click to hide to all alert boxes
	$('.alert_success,.alert_info,.alert_warning,.alert_error').click(function(){
		$(this).fadeOut('fast');
	});


/*
	// Setup modal window for all photos
	$('.media_photos li a[rel=slide]').fancybox({
		padding: 0,
		titlePosition: 'outside',
		overlayColor: '#333333',
		overlayOpacity: .2
	});
*/



	// Setup charts example

/*
	// Chart bar type
	$('#chart_bar').click(function(){
		setChart('table#chart', 'bar', '#chart_wrapper');

		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');
	});


	// Chart area type
	$('#chart_area').click(function(){
		setChart('table#chart', 'area', '#chart_wrapper');

		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');
	});


	// Chart pie type
	$('#chart_pie').click(function(){
		setChart('table#chart', 'pie', '#chart_wrapper');

		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');
	});


	// Chart line type
	$('#chart_line').click(function(){
		setChart('table#chart', 'line', '#chart_wrapper');

		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');
	});
*/



	/*
	//make table editable, refresh charts on blur
	$(function(){
		$('table#chart td')
			.click(function(){
				if( !$(this).is('.input')  && $(this).attr('class') != 'no_input hover'){
					$(this).addClass('input')
						.html('<input type="text" value="'+ $(this).text() +'" size="4" />')
						.find('input').focus()
						.blur(function(){
							//remove td class, remove input
							$(this).parent().removeClass('input').html($(this).val() || 0);
							//update charts
							$('.visualize').trigger('visualizeRefresh');
						});
				}
			})
			.hover(function(){ $(this).addClass('hover'); },function(){ $(this).removeClass('hover'); });
	});
	*/

	// click table row to select row
	/*
	$(function(){
		$('table#chart tr:gt(0)')
			.click(function(){
				$(this).parent().find('input[type=checkbox]').attr('checked', false);
				$(this).find('input[type=checkbox]').attr('checked', true);

				setChart('table#chart', $('table#chart').attr('rel'), '#chart_wrapper');
			})
			.hover(
				function(){ $(this).addClass('hover'); },
				function(){ if( !$(this).find('input[type=checkbox]').is(':checked') ) $(this).removeClass('hover'); });
	});
	*/

	$(function(){
		$('table.data:not(.ignore) tr:gt(0)')
			.click(function(){
				$(this).find('input[type=checkbox]').attr('checked', ! $(this).find('input[type=checkbox]').attr('checked'));

				if ($(this).is('table#chart tr')) {
					setChart('table#chart', $('table#chart').attr('rel'), '#chart_wrapper');
				}
			})
			.hover(
				function(){ $(this).addClass('hover'); },
				function(){ if( !$(this).find('input[type=checkbox]').is(':checked') ) $(this).removeClass('hover'); });
	});



	// Setup left panel calendar

	$("#calendar").datepicker({
		nextText: '&raquo;',
		prevText: '&laquo;',
		dateFormat: "yy-mm-dd",
		onSelect: function(date) {

			$.fancybox.showActivity();
			window.location = $("#calendar").attr('action') + date;

		}
	});


/*
	// Setup datepicker input
	$("#datepicker").datepicker({
		nextText: '&raquo;',
		prevText: '&laquo;',
		showAnim: 'slideDown'
	});
*/


    /*
    $('#content div.inner').append( $('#debug-container').html() );
    $('#filter-open').click( function(){ $('#filter-container').fadeToggle('fast'); } );

    //$('#filter-submit').click( function(){ $('#form_data').submit(); } );
    //$('#filter-reset').click( function(){ $('#filter-container input[type=text]').val(''); $('#form_data').submit(); } );


	var current_url = window.location.protocol + '//' + window.location.hostname + window.location.pathname;
	$('#filter-submit').click( function(){ window.location = current_url + '?' + $('#filter-container input[type=text], #filter-container select').serialize(); } );
    $('#filter-reset').click( function(){ window.location = current_url; } );
    */


	// Setup minimize and maximize window
	$('.onecolumn .header span').click(function(){
		if($(this).parent().parent().children('.content').css('display') == 'block')
		{
			$(this).css('cursor', 's-resize');
		}
		else
		{
			$(this).css('cursor', 'n-resize');
		}

		$(this).parent().parent().children('.content').slideToggle('fast');
	});

	$('.twocolumn .header span').click(function(){
		if($(this).parent().parent().children('.content').css('display') == 'block')
		{
			$(this).css('cursor', 's-resize');
		}
		else
		{
			$(this).css('cursor', 'n-resize');
		}

		$(this).parent().parent().children('.content').slideToggle('fast');
	});

	$('.threecolumn .header span').click(function(){
		if($(this).parent().parent().children('.content').css('display') == 'block')
		{
			$(this).css('cursor', 's-resize');
		}
		else
		{
			$(this).css('cursor', 'n-resize');
		}

		$(this).parent().children('.content').slideToggle('fast');
	});



	// Check or uncheck all checkboxes
	$('#check_all').click(function(){
		if($(this).is(':checked'))
		{
			$('form#bulk input:checkbox').each(function(){
				//
				$(this).attr('checked', true);
				$(this).parents('tr:first').addClass('hover');
			});
		}
		else
		{
			$('form#bulk input:checkbox').each(function(){
				//
				$(this).attr('checked', false);
				$(this).parents('tr:first').removeClass('hover');
			});
		}
	});



	// Setup notification badges for shortcut
	setNotifications();



	/*
	// Setup modal window link
	$('#shortcut li a').fancybox({
		padding: 0,
		titleShow: false,
		overlayColor: '#333333',
		overlayOpacity: .5
	});
	*/


	// Add tooltip to shortcut
	$('#shortcut li a[title!=""]').tipsy({gravity: 's'});
	$('table.data th a[title!=""]').tipsy({gravity: 's'});


/*
	// Setup edit popups
	$('table.data a.modal').fancybox({
		padding: 0,
		autoScale: true,
		titleShow: false,
		overlayColor: '#333333',
		overlayOpacity: .5,
		disableNavButtons: false,
		scrolling:'yes',
		onComplete: function(){
			$(".datepicker").datepicker({
				nextText: '&raquo;',
				prevText: '&laquo;',
				showAnim: 'slideDown',
				dateFormat:"yy-mm-dd",
				firstDay:1
			});
		}
	});
*/


	// Setup tab contents

/*
	// tab 1
	$('#tab1').click(function(){
		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');

		//show tab1 content
		$('.tab_content').addClass('hide');
		$('#tab1_content').removeClass('hide');
	});


	// tab 2
	$('#tab2').click(function(){
		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');

		//show tab2 content
		$('.tab_content').addClass('hide');
		$('#tab2_content').removeClass('hide');
	});


	// tab 3
	$('#tab3').click(function(){
		//switch menu
		$(this).parent().parent().find('td input').removeClass('active');
		$(this).addClass('active');

		//show tab3 content
		$('.tab_content').addClass('hide');
		$('#tab3_content').removeClass('hide');
	});



	// Add tooltip to edit and delete button
	$('.help').tipsy({gravity: 's'});


	// Setup sortable to threecolumn div
	$("#threecolumn").sortable({
		opacity: 0.6,
		connectWith: '.threecolumn_each',
		items: 'div.threecolumn_each'
	});
*/

});

$(document).ready(function() {

	setChart('table#chart', $('table#chart').attr('rel'), '#chart_wrapper');
	$(window).resize(function(){
		//$('.visualize').trigger('visualizeRefresh');
		setChart('table#chart', $('table#chart').attr('rel'), '#chart_wrapper');
	});

/*
	//Add ability to click link if href is not empty
	$('#main_menu').find('li a').click(function(){
		if($(this).attr('href').length > 0 && $(this).attr('rel').length == 0)
		{
			location.href = $(this).attr('href');
		}
	});
*/

/*
	//Add message flag/unflag
	$('img.flag').click(function(){
		$(this).parents('tr:first').find('input[type="checkbox"]:first ').attr('checked', true);

		if ($(this).hasClass('gray'))
			$(this).parents('form:first').find('.bulk select').val('flag');
		else
			$(this).parents('form:first').find('.bulk select').val('unflag');

		$('#action').val('bulk');
		$(this).parents('form:first').submit();
	});
*/
});


$(document).ready(function() {

	$.z_toolbars = function(selector) {
		//
		if (!selector)
			selector = 'div.toolbar[rel=tabs]';

		$(selector).each(function(){
			//
			if ($(this).is('.tabbed')) {
				return;
			}
			$(this).addClass('tabbed');

			//$('div.toolbar a[rel=tab]').click(function(){
			$(this).find('a[rel=tab]').click(function(){
				//switch tab menu
				$(this).parent().parent().find('a[rel=tab]').removeClass('active');
				$(this).addClass('active');

				//hide all, show selected tab
				//$('.tab').addClass('hide');
				$(this).parent().find('a[rel=tab]').each(function(){
					//
					$('#' + $(this).attr('tab')).addClass('hide');
				});

				$('#' + $(this).attr('tab')).removeClass('hide');

				$.cookie("ADMIN.TABS."+$(this).parent().attr('id'), $(this).attr('id'));
			});

			if ($('#' + $.cookie("ADMIN.TABS."+$(this).attr('id'))).length)
				$('#' + $.cookie("ADMIN.TABS."+$(this).attr('id'))).click();
			else
				$(this).find('a[rel=tab]').filter(':first').click();
		});
	}

	$.z_toolbars();

	$('.wysiwyg').wysiwyg({
   		css : $('#ROOT').val() + "/admin/css/wombat.wysiwyg.css"
   		/*
   		controls: {
      		html : { visible : true }
      	}
      	*/
   	});

   	$(".datepicker").datepicker({
		nextText: '&raquo;',
		prevText: '&laquo;',
		showAnim: 'slideDown',
		dateFormat:"yy-mm-dd",
		firstDay:1
	});

	$('.colorpicker').colorpicker({
        size: 20,
        label: '<label>Color:</label> &nbsp; ',
        hide: true
    });

    /*
    $('input[type=button][rel=modal]').click(function() {
    	$.fancybox({
			padding: 0,
			titleShow: false,
			overlayColor: '#333333',
			overlayOpacity: .5,
			showNavArrows: false,
			disableNavButtons: false,
			href: $(this).attr('href'),
			onComplete: function(){
				//
				$("#popup .datepicker").datepicker({
					nextText: '&raquo;',
					prevText: '&laquo;',
					showAnim: 'slideDown',
					dateFormat:"yy-mm-dd",
					firstDay:1
				});
			}
		});
	});
	*/

	$('input[type=button][rel=popup], a[rel=popup]').click(function(e) {
		//
		e.preventDefault();

		var popup = window.open( $(this).attr('href'), "popup-" + $(this).attr('id'), "menubar=0, resizable=1, width="+ $(this).attr('popup-width') +", height="+ $(this).attr('popup-height') +"" );
		popup.moveTo((screen.width - $(this).attr('popup-width')) / 2, 100);

		//return false;

	});

   	//$('a[rel=modal]').unbind();
	//$('a[rel=modal], input[rel=modal]').fancybox({
	$('a[rel=modal], input[rel=modal]').click(function() {

		$.fancybox({
			//
			padding: 0,
			titleShow: false,
			overlayColor: '#333333',
			overlayOpacity: .5,
			showNavArrows: false,
			disableNavButtons: false,
			href: $(this).attr('href'),
			onComplete: function(){

				if ($('#popup').height() >  $('#fancybox-inner').height() - 30 - 33 ) {
					$('#popup').height( $('#fancybox-inner').height() - 30 - 33 );
				}

				$('.modal_content .wysiwyg').wysiwyg({
			   		css : $('#ROOT').val() + "/admin/css/wombat.wysiwyg.css"
			   	});

			   	$(".modal_content .datepicker").datepicker({
					nextText: '&raquo;',
					prevText: '&laquo;',
					showAnim: 'slideDown',
					dateFormat:"yy-mm-dd",
					firstDay:1
				});

				$('.modal_content .colorpicker').colorpicker({
			        size: 20,
			        label: '<label>Color:</label> &nbsp; ',
			        hide: true
			    });

				$('.modal_content .wysiwyg iframe').each(function(){
					$(this).css( { minHeight: parseInt($(this).css('min-height')) - 28 + 'px', height: $(this).height() - 28 + 'px' } );
				});

				//$.fancybox.resize();
			}
		});
		return false;
	});

	$("a[rel=post],input[rel=post]").click(function(){
		//
		$.fancybox.showActivity();

		var postdata = '';
		if ($(this).attr('data'))
			postdata = $(this).attr('data');
		if ($(this).attr('serialize'))
			postdata = $( $(this).attr('serialize') ).serialize();

		$.ajax({
			type: 'POST',
			url: $(this).attr('href'),
			data: postdata,
			success: function(a){
				if (a.responseText) {
					//
					$.fancybox(a.responseText, {
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

		return false;
	});


	$('form.ajax').submit(function(){

		$.fancybox.showActivity();

		$(this).ajaxSubmit({
			context: $(this),
			success:function(responseText){

				if ( $(this).attr('target') ) {
					//
					$.fancybox.hideActivity();
					//$('#' + $(this).attr('target')).contents().find('html').html( responseText );

                    /*
					var iframe = document.getElementById( $(this).attr('target') ).contentWindow.document;
			        iframe.designMode = "On";
			        iframe.open();
			        iframe.write( responseText );
			        iframe.close();
			        iframe.designMode = "Off";
			        */

					return;
				}

				if (responseText && responseText != '<head></head><body></body>') {
					$.fancybox(responseText, {
						padding: 10
					});
					return;
				}

				$.fancybox.close();
				$.fancybox.showActivity();

				//window.location.reload();
				window.location = window.location;
			},
			error:function(a){
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
		return false;
	});


	/*
	$("a.ajax.redirect").click(function(){
		//
		$.fancybox.showActivity();

		$.ajax({
			type: 'POST',
			url: $(this).attr('href'),
			data: $(this).attr('data'),
			success: function(r){

				alert($(this).attr('url'));
				//window.location = $(this).attr('url');
			},
			error: function(a,b,c){
				$.fancybox.hideActivity();
				if (a.status == 403) {
					//
					$.fancybox(a.responseText, {
						padding: 0
					});
				}
				else
					alert('ERROR: '+a.responseText);
			}
		});

		return false;
	});
	*/

});