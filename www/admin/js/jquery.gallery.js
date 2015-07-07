
$(document).ready(function() {

	$.fn.gallery = function(options){
		//
		//options = $.extend({}, $.fn.gallery.defaults, options);

		return this.each(function(){

			//Show Banner
			$(this).find(".main_image .desc").show(); //Show Banner
			$(this).find(".main_image .block").animate({ opacity: 0.85 }, 1 ); //Set Opacity

			$(this).find(".main_image").width( $(this).width() - $(this).find(".image_thumb").width() - 22 );

			//Click and Hover events for thumbnail list
			$(this).find(".image_thumb ul li:first").addClass('active');
			$(this).find(".image_thumb ul li").click(function(){
				//Set Variables
				var imgAlt = $(this).find('img').attr("alt"); //Get Alt Tag of Image
				var imgTitle = $(this).find('a').attr("href"); //Get Main Image URL
				var imgDesc = $(this).find('.block').html(); 	//Get HTML of block
				var imgDescHeight = $(this).closest(".main_image").find('.block').height();	//Calculate height of block

				if ($(this).is(".active")) {  //If it's already active, then...
					return false; // Don't click through
				} else {
					//Animate the Teaser
					$(this).closest(".image_thumb").parent().find(".main_image").find(".block").animate({ opacity: 0, marginBottom: -imgDescHeight }, 250 , function() {
						$(this).closest(".main_image").find(".block").html(imgDesc).animate({ opacity: 0.85, marginBottom: "0" }, 250 );
						$(this).closest(".main_image").find("img.preview").attr({ src: imgTitle , alt: imgAlt});

						if (options.onchange) {
							options.onchange();
						}
					});
				}

				$(this).closest(".image_thumb").parent().find(".image_thumb ul li").removeClass('active'); //Remove class of 'active' on all lists
				$(this).addClass('active');  //add class of 'active' on this list only
				return false;

			}) .hover(function(){
				$(this).addClass('hover');
				}, function() {
				$(this).removeClass('hover');
			});

			//Toggle Teaser
			$(this).find("a.collapse").click(function(){
				$(this).closest(".main_image .block").slideToggle();
				$(this).closest("a.collapse").toggleClass("show");
			});

		});
	};

	 /*
	 $.fn.gallery.defaults = {
        fade: false,
        fallback: '',
        gravity: 'n',
        html: false,
        title: 'title'
    };
    */

});//Close Function