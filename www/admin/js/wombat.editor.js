
$(document).ready(function(){
	//
	$('.z-editable').each(function(){
    	//
    	$(this).find('.z-cell').first().height( $(this).parent().height() );

    	$(this).append(''+
				'<input type="button" value="Content" class="z-insert" ' +
				'style="position:absolute; margin-left:'+($(this).parent().width()/2 - 80/2)+'px; margin-top:-'+($(this).parent().height()/2 + 30/2)+'px;" />'+
    	'');
    });

    $('.z-block, .z-slot').each(function(){
    	//
    	$(this).width( $(this).parent().width() );
    	$(this).height( $(this).parent().height() );
    });


    window.parent.$.z_content();
    window.parent.$.fancybox.hideActivity();


    // PREVIEW EDITOR

	/*
    // set opacity to all elements, except editable ones
    if ($('body').attr('opacity') == 'on') {
    	//
    	$('body *:not(#z-context, .z-cell)').css('opacity', '0.5');
    }
    */

    // context menu
    $('body').append('<ul id="z-context" class="context-menu"></ul>');

    // set selector to the entire body, without special editable areas
    selector = 'body *:not(#z-context, .z-slot, .z-block, .z-cell)';

    // if we only have designated editable areas, move the editor to these
    if ( $('.z-editable').length ) {
    	selector =  '.z-editable .z-cell *:not(#z-context, .z-slot, .z-block, .z-cell)';
    }


    // assign random ID's to all elements with no id
    $(selector).each(function(){
    	if ( !$(this).attr('id')) {
    		var autoid = this.nodeName.toLowerCase() + '-' + Math.floor(Math.random()*9999);
    		while ($('#'+autoid).length) {
    			autoid = this.nodeName.toLowerCase() + '-' + Math.floor(Math.random()*9999);
    		}

    		$(this).attr('id', autoid);
    	}
    });

	// setup context menu for all elements
    $(selector).each(function(){
    	//
    	$(this).contextMenu(
    		// get menu
    		function(el){
    			//
    			if (el != undefined) {
    				//
    				$('#z-context').html('');
			    	$('#z-context').append('<li class="tag"><a href="#'+$(el).attr('id')+'">'+ el.nodeName.toUpperCase() +' #'+ $(el).attr('id') +'</a></li>');

			    	// all elements up to the current editable area, or body
			    	$(el).parentsUntil('body, .z-cell').each(function(){
						//
			    		$('#z-context').append('<li class="tag"><a href="#'+$(this).attr('id')+'">'+ this.nodeName.toUpperCase() +' #'+ $(this).attr('id') +'</a></li>');
			    	});
    			}
    			return {menu: 'z-context', inSpeed: 75, outSpeed: 40};
    		},
    		// callback when context element clicked
	        function(action, el, pos) {
	        	//
	        	// call mouse-out to remove highlighting, and have all properties intact
	        	$('#'+action).mouseout();

	        	var attributes = $('#'+action).css();

				// store id in two locations, so we can later change the id itself
				window.parent.$('#inspector #css-id').val( $('#'+action).attr('id') );
				window.parent.$('#inspector #css-id').attr( 'original', $('#'+action).attr('id') );

				window.parent.$('#inspector #css-class').val( $('#'+action).attr('class') );
	        	for (i in attributes) {
	        		if (attributes[i] == defaults[i])
	        			window.parent.$('#inspector #css #css-' + i).focus().val('').blur();
	        		else
	      				window.parent.$('#inspector #css #css-' + i).focus().val( attributes[i] ).blur();
	        	}
	        	window.parent.$('#inspector #css-id').focus().blur();

	        	// make sure the css inspector tab is open
	        	window.parent.$('#btn_icss').click();
	    	},
	    	// callback when mouse over
	    	function(action, el) {
	    		//
	    		$('#' + action).mouseover();
	    	},
	    	// callback when mouse out
	    	function(action, el) {
	    		//
	    		$('#' + action).mouseout();
	    	}
	    );
    });

    // set mouseover behavior for all elements - highlight, store current border
    $(selector).mouseover(function(e){

    	$(this).attr('zeditor', $(this).css('border'));
    	$(this).css('border', '1px dotted red');

    	e.stopPropagation();
    	$(this).parents().mouseout();
    });

    // set mouseout behavior - remove highlight
    $(selector).mouseout(function(e){

    	$(this).css('border', $(this).attr('zeditor'));

    	e.stopPropagation();
    });


    // extend for css() to retrieve all attributes
    jQuery.fn.css2 = jQuery.fn.css;
	jQuery.fn.css = function() {
	    if (arguments.length) return jQuery.fn.css2.apply(this, arguments);

	    var attr = ['font-family','font-size','font-weight','font-style','color',
	        'text-transform','text-decoration','letter-spacing','word-spacing',
	        'line-height','text-align','vertical-align','direction','background-color',
	        'background-image','background-repeat','background-position',
	        'background-attachment','opacity','width','height','top','right','bottom',
	        'left','margin-top','margin-right','margin-bottom','margin-left',
	        'padding-top','padding-right','padding-bottom','padding-left',
	        'border-top-width','border-right-width','border-bottom-width',
	        'border-left-width','border-top-color','border-right-color',
	        'border-bottom-color','border-left-color','border-top-style',
	        'border-right-style','border-bottom-style','border-left-style','position',
	        'display','visibility','z-index','overflow-x','overflow-y','white-space',
	        'clip','float','clear','cursor','list-style-image','list-style-position',
	        'list-style-type','marker-offset'];

        /*
	    var attr = ['font-family','font-size','font-weight','font-style','color',
	        'text-transform','text-decoration','letter-spacing','word-spacing',
	        'line-height','text-align','vertical-align','background-color',
	        'background-image','background-repeat','background-position',
	        'opacity','width','height','top','right','bottom',
	        'left','margin-top','margin-right','margin-bottom','margin-left',
	        'padding-top','padding-right','padding-bottom','padding-left',
	        'border-top','border-right','border-bottom','border-left','position',
	        'display','visibility','z-index','overflow-x','overflow-y',
	        'float','clear','cursor','list-style'];
        */

	    var len = attr.length, obj = {};
	    for (var i = 0; i < len; i++)
	        obj[attr[i]] = jQuery.fn.css2.call(this, attr[i]);
	    return obj;
	}

	// collect default values for each css property
	var defaults = $('body').css();
   	window.parent.$('#inspector #css').html('');
   	for (i in defaults) {
 		var d = defaults[i];
 		$('body').css(i, '');
 		defaults[i] = '' + $('body').css(i);
 		$('body').css(i, d);

 		if (defaults[i].match(/^\d+px$/))
 			defaults[i] = '0px';

 		/*
 		if (i.match(/^border/))
 			defaults[i] = 'none';
 		if (i.match(/^list-style/))
 			defaults[i] = 'disc';
        */

 		window.parent.$('#inspector #css').append('<li><label for="css-'+i+'">'+i+'</label><input id="css-'+i+'" type="text" value="" name="'+i+'" title="'+defaults[i]+'"></li>');
   	}

   	window.parent.$('#inspector #css input[title!=""]').hint();

   	// link css inspector to relay the modified property back to the editor
   	window.parent.$('#inspector #css-id').blur(function(){
   		//
   		$('#' + window.parent.$('#inspector #css-id').attr('original')).attr( 'id', $(this).val() );
   		window.parent.$('#inspector #css-id').attr('original', $(this).val());
   	});

   	window.parent.$('#inspector #css-class').keyup(function(){
   		//
   		$('#' + window.parent.$('#inspector #css-id').val()).attr( 'class', $(this).val() );
   	});

   	window.parent.$('#inspector #css input[type=text]').keyup(function(){
   		//
   		$('#' + window.parent.$('#inspector #css-id').val()).css( $(this).attr('name'), $(this).val() );

   		var slot = $.z_get_slot( window.parent.$('#inspector #css-id').val() );
   		var content = $.z_get_content( window.parent.$('#inspector #css-id').val() );

   		//alert( slot );
   		//alert( content );

   		window.parent.$.z_insert( slot, content );
   	});


	// based on child ID, retrieve the editable slot
	$.z_get_slot = function( child )
	{
		var slot = $('#' + child).closest('body, .z-editable .z-cell');
		if (slot.is('body')) {
			return 'CONTENT';
		}
		if (slot.is('.z-editable .z-cell')) {
			return slot.attr('slot');
		}

		return '';
	}

   	$.z_get_content = function( child )
   	{
   		var slot = $('#' + child).closest('body, .z-editable .z-cell');
   		var clone = slot.clone();

   		clone.find('#z-context').replaceWith('');
   		clone.find('.z-slot,.z-editable,.z-block').each(function(){
   			//
   			var html = $(this).find('.z-cell').html();
   			$(this).replaceWith( html );
   		});
   		clone.find('*').each(function() {
   			//
   			if ( $(this).attr('style') == '' )
   				$(this).removeAttr('style');

   			if ( $(this).attr('id').match(new RegExp('^' + this.nodeName.toLowerCase() + '-\\d+$')) )
   				$(this).removeAttr('id');

   			$(this).removeAttr('zeditor');
   		});

   		return clone.html();
   	}

});