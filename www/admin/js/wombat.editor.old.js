
	function addslashes(str)
	{
	    return (str+'').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
	}

	function html_encode(value)
	{
	  return $('<div/>').text(value).html();
	}

	function html_decode(value)
	{
	  return $('<div/>').html(value).text();
	}

	function rgb_to_hex(rgb)
	{
	    if (rgb.match(/^#[0-9A-Fa-f]{6}$/)) {
	        return rgb;
	    }
	    var rgbvals = /rgb\((.+),(.+),(.+)\)/i.exec(rgb);
	    if (!rgbvals) {
	        return rgb;
	    }
	    var rval = parseInt(rgbvals[1]);
	    var gval = parseInt(rgbvals[2]);
	    var bval = parseInt(rgbvals[3]);
	    var pad = function(value) {
	        return (value.length < 2 ? '0' : '') + value;
	    };
	    return '#' + pad(rval.toString(16).toUpperCase()) + pad(gval.toString(16).toUpperCase()) + pad(bval.toString(16).toUpperCase());
	}

/*
	jQuery.fn.outerHTML = function(s) {
		return (s)
		? this.before(s).remove()
		: jQuery("<p>").append(this.eq(0).clone()).html();
	}
*/

//var $ = window.parent.$, jQuery = window.parent.jQuery;

alert('0');

$(function(){

    alert('1');

    // force precise width / height
    $('.z-editable').each(function(){
    	//
    	$(this).width( $(this).width() );
    	$(this).height( $(this).height() );

    	$(this).append(''+
				'<input type="button" value="&uArr; &uArr;" class="z-split-up">'+
				'<input type="button" value="&dArr; &dArr;" class="z-split-down">'+
				'<input type="button" value="&lArr;" class="z-split-left">'+
				'<input type="button" value="&rArr;" class="z-split-right">'+
				'<div class="z-group"><input type="button" class="z-remove" value="Remove"> &nbsp; <input type="button" class="z-insert" value="Content"></div>'+
    	'');
    });

    // clickable placeholders
	$('.z-editable').click(function(){
        //
		$('.z-editable').removeClass("active");
		$(this).addClass("active");

	});


	function parse_up(element, dy)
	{
		//alert('in: '+$(element).attr('class'));
		$(element).css('height', ($(element).height() - dy) + 'px')

		$(element).filter('.z-container-v').children()       .each(function(){ parse_up(this, dy); });
		$(element).filter('.z-container-h').children().last().each(function(){ parse_up(this, dy); });
	}
	function parse_down(element, dy)
	{
		//alert('in: '+$(element).attr('class'));
		$(element).css('height', ($(element).height() + dy) + 'px')

		$(element).filter('.z-container-v').children()        .each(function(){ parse_down(this, dy); });
		$(element).filter('.z-container-h').children().first().each(function(){ parse_down(this, dy); });
	}

	$('.z-editable .z-split-up').click(function(){

		w = $(this).parent().width();
		h = $(this).parent().height();

		$(this).parent()
			.wrap('<div class="z-container-h" style="height:'+h+'px; width:'+w+'px; float:left;" />')
			.height(Math.floor(h/2)-5)
			.clone( true ).height(Math.ceil(h/2)-5)
			//.find('.z-cell').attr('type', '').html('').parent()
			.insertBefore( $(this).parent() )
			.after('<div class="z-splitter-h" restrict="v" style="width:'+w+'px; float:left;"></div>')
			.next()
			.easydrag().ondrop(function(e, element, dx, dy){

				$(element).prev('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_up(this, dy) });

				$(element).next('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_down(this, dy) });

			});

		//
	});

	$('.z-editable .z-split-down').click(function(){

		w = $(this).parent().width();
		h = $(this).parent().height();

		$(this).parent()
			.wrap('<div class="z-container-h" style="height:'+h+'px; width:'+w+'px; float:left;" />')
			.height(Math.floor(h/2)-5)
			.clone( true ).height(Math.ceil(h/2)-5)
			//.find('.z-cell').attr('type', '').html('').parent()
			.insertAfter( $(this).parent() )
			.before('<div class="z-splitter-h" restrict="v" style="width:'+w+'px; float:left;"></div>')
			.prev()
			.easydrag().ondrop(function(e, element, dx, dy){

				$(element).prev('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_up(this, dy) });

				$(element).next('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_down(this, dy) });

			});

		//
	});

	function parse_left(element, dx)
	{
		//alert('in: '+$(element).attr('class'));
		$(element).css('width', ($(element).width() - dx) + 'px')

		$(element).filter('.z-container-h').children()        .each(function(){ parse_left(this, dx); });
		$(element).filter('.z-container-v').children().last().each(function(){ parse_left(this, dx); });
	}
	function parse_right(element, dx)
	{
		//alert('in: '+$(element).attr('class'));
		$(element).css('width', ($(element).width() + dx) + 'px')

		$(element).filter('.z-container-h').children()        .each(function(){ parse_right(this, dx); });
		$(element).filter('.z-container-v').children().first().each(function(){ parse_right(this, dx); });
	}

	$('.z-editable .z-split-left').click(function(){

		w = $(this).parent().width();
		h = $(this).parent().height();

		$(this).parent()
			.wrap('<div class="z-container-v" style="height:'+h+'px; width:'+w+'px; float:left;" />')
			.width(Math.floor(w/2)-5)
			.clone( true ).width(Math.ceil(w/2)-5)
			//.find('.z-cell').attr('type', '').html('').parent()
			.insertBefore( $(this).parent() )
			.after('<div class="z-splitter-v" restrict="h" style="height:'+h+'px; float:left;"></div>')
			.next()
			.easydrag().ondrop(function(e, element, dx, dy){

				$(element).prev('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_left(this, dx) });

				$(element).next('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_right(this, dx) });

			});

		//
	});

	$('.z-editable .z-split-right').click(function(){

		w = $(this).parent().width();
		h = $(this).parent().height();


		$(this).parent()
			.wrap('<div class="z-container-v" style="height:'+h+'px; width:'+w+'px; float:left;" />')
			.width(Math.floor(w/2)-5)
			.clone( true ).width(Math.ceil(w/2)-5)
			//.find('.z-cell').attr('type', '').html('').parent()
			.insertAfter( $(this).parent() )
			.before('<div class="z-splitter-v" restrict="h" style="height:'+h+'px; float:left;"></div>')
			.prev()
			.easydrag().ondrop(function(e, element, dx, dy){

				$(element).prev('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_left(this, dx) });

				$(element).next('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_right(this, dx) });

			});

		//
	});


	$('.z-editable .z-remove').click(function(){

		if (!$(this).parent().parent().parent().hasClass('z-container-v') && !$(this).parent().parent().parent().hasClass('z-container-h'))
			return;

		w = $(this).parent().parent().parent().width();
		h = $(this).parent().parent().parent().height();

		if ( $(this).parent().parent().nextAll('.z-editable').length )
		{
			$(this).parent().parent().nextAll('.z-editable').width(w).height(h).addClass('active');
			$(this).parent().parent().parent().replaceWith( $(this).parent().parent().nextAll('.z-editable') );
		}
		else if ( $(this).parent().parent().prevAll('.z-editable').length )
		{
			$(this).parent().parent().prevAll('.z-editable').width(w).height(h).addClass('active');
			$(this).parent().parent().parent().replaceWith( $(this).parent().parent().prevAll('.z-editable') );
		}

	});

	/*
	$('.z-editable .z-insert').fancybox({
		padding: 0,
		titleShow: false,
		overlayColor: '#333333',
		overlayOpacity: .5,
		href: $('#REQUEST').val() + '/content',
		disableNavButtons: false,
		onComplete: function(){

			$('input[title!=""],textarea[title!=""]').hint();

			//$('#wysiwyg').val( $('.z-editable.active .layout-content').html() );

			$('#article-title').val( $('.z-editable.active .layout-content h1[title]').html() );
			$('#wysiwyg').val( $('.z-editable.active .layout-content').html().replace($('.z-editable.active .layout-content h1[title]').outerHTML(), '') );

			// Setup WYSIWYG editor
		    $('#wysiwyg').wysiwyg({
		    	css : $('#ROOT').val() + "/admin/css/wombat.wysiwyg.css"
		    });

		    $('div.toolbar[rel=tabs]').each(function(){
				//
				//$('div.toolbar a[rel=tab]').click(function(){
				$(this).find('a[rel=tab]').click(function(){
					//switch tab menu
					$(this).parent().parent().find('a[rel=tab]').removeClass('active');
					$(this).addClass('active');

					//hide all, show selected tab
					$('#'+$(this).parent().attr('id')).find('a[rel=tab]').each(function(){
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


			if ($('.z-editable.active .layout-content').attr('type') == 'picture')
			{
				// try to decode image
				$('#picture-url').attr('a_prefix', $('#conf-wwwuploads').val());

				if ($('.z-editable.active .layout-content').find('img').length)
				{
					img = $('.z-editable.active .layout-content').find('img');

					$('#picture-url').val( $(img).attr('src').replace($('#picture-url').attr('a_prefix'), '') ).blur();
					$('#picture-width').val( $(img).css('width') ).blur();
					$('#picture-width').val( $(img).css('height') ).blur();

					if ( $(img).parent().attr('href') )
						$('#picture-link').val( $(img).parent().attr('href') ).blur();

					reset_image_preview();
					$('#xtab2').click();

				}
			}
			else if ($('.z-editable.active .layout-content').attr('type') == 'other')
			{
				$('#component-title').val( $('.z-editable.active .layout-content h1[title]').html() );
				$('#component-select').val( $('.z-editable.active .layout-content').html().replace($('.z-editable.active .layout-content h1[title]').outerHTML(), '') );

				$('#xtab3').click();
			}

		}
	});
	*/


	$('.z-splitter-h').each(function(){

		if( $(this).prev('.z-container-v, .z-container-h, .z-editable').length )
			$(this).width( $(this).prev('.z-container-v, .z-container-h, .z-editable').width() );
		else
			$(this).width( $(this).next('.z-container-v, .z-container-h, .z-editable').width() );

	}).easydrag().ondrop(function(e, element, dx, dy){

				$(element).prev('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_up(this, dy) });

				$(element).next('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_down(this, dy) });

			});

	$('.z-splitter-v').each(function(){

		if( $(this).prev('.z-container-v, .z-container-h, .z-editable').length )
			$(this).height( $(this).prev('.z-container-v, .z-container-h, .z-editable').height() );
		else
			$(this).height( $(this).next('.z-container-v, .z-container-h, .z-editable').height() );

	}).easydrag().ondrop(function(e, element, dx, dy){

				$(element).prev('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_left(this, dx) });

				$(element).next('.z-container-v, .z-container-h, .z-editable')
					.each(function(){ parse_right(this, dx) });

			});


});


