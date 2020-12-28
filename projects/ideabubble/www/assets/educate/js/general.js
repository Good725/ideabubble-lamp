$(document).ready(function () {
	$(window).scroll(function () {
	    body_height = $(".body-content").position();
	    scroll_position = $(window).scrollTop();
	    if (body_height.top < scroll_position)
	        $("body").addClass("fixed_strip");
	    else
	        $("body").removeClass("fixed_strip");

	});
	$(".submenu > a").click(function(){
		$(".dropwrap").removeClass("open");
		 $(this).siblings(".dropwrap").slideToggle("slow", "linear")

	});
	$(".pull").click(function(){
		$(".navigation").slideToggle("slow", "linear")
	});
	$(window).resize(function(){
    	$(".navigation").removeAttr("style")
	});

	/*$('.home-page-slider').slick({
		autoplay: true,
		dots: true,
		speed: 500,
		arrows: false,
		fade: false,

		cssEase: 'linear'
	});*/

	/*------------------------------------*\
	 #Sliders
	\*------------------------------------*/
	var swiper = null;
	if (document.getElementById('home-banner-swiper') && document.getElementById('home-banner-swiper').getAttribute('data-slides') > 1)
	{
		var $banner = $('#home-banner-swiper');

		swiper = new Swiper('#home-banner-swiper', {
			autoplay   :$banner.data('autoplay'),
			direction  : $banner.data('direction'),
			effect     : $banner.data('effect'),
			speed      : $banner.data('speed'),
			loop       : true,
			pagination : '#home-banner-swiper .swiper-pagination',
			paginationClickable : true,
			nextButton : '#home-banner-swiper .swiper-button-next',
			prevButton : '#home-banner-swiper .swiper-button-prev'
		});
	}



	/*------------------------------------*\
	 # contact-us form validation
	\*------------------------------------*/
	$("#form-contact-us").on('submit', function(ev)
	{
		var valid = ($(this).validationEngine('validate'));
		if ( ! valid)
		{
			ev.preventDefault();
			return false;
		}
	});


	var window_width = $( window ).width();
	$(window).resize(function() {
		$(".js-height .fix-container").css( 'min-height', "");
		set_div_height();
	});
	set_div_height();

	/* add active clss to header menu 1st element */
	$('.navigation ul li a:first').addClass('active');
});/*document end*/

function set_div_height(){
	var window_width = $( window ).width();
	if(window_width >1600){
			$('.js-height .roate-text').each(function(index) {
				var w = $(this).width();
				var h = w + 10;
				$("#portrait_text_width_"+index).css( 'min-height', h);
			});
		}
}
function go_to_section(destination_id)
{
	$("html, body").animate({ scrollTop: $('#'+destination_id).offset().top }, 1000);
}
	
