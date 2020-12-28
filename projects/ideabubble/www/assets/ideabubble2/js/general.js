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

	/*------------------------------------*\
	 #Sliders
	\*------------------------------------*/
	var swiper = null;
	if (document.getElementById('home-banner-swiper') && document.getElementById('home-banner-swiper').getAttribute('data-slides') > 1)
	{
		var $banner = $('#home-banner-swiper');

		swiper = new Swiper('#home-banner-swiper', {
			autoplay: $banner.data('autoplay'),
			speed: $banner.data('speed'),
			loop: true,
			pagination: '#home-banner-swiper .swiper-pagination',
			paginationClickable: true,
			nextButton: '#home-banner-swiper .swiper-button-next',
			prevButton: '#home-banner-swiper .swiper-button-prev'
		});
	}

	var carousel_slider = new Swiper('#courses-carousel', {
		slidesPerView: calculate_slides_per_view(),
		paginationClickable: true,
		nextButton: '#courses-carousel-next',
		prevButton: '#courses-carousel-prev',
		spaceBetween: 31
	});

	$(window).resize(function()
	{
		carousel_slider.params.slidesPerView = calculate_slides_per_view();
	});

	function calculate_slides_per_view()
	{
		var width = window.innerWidth;
		var count = 2;
		if      (width <  768) count = 1;
		//else if (width <  990) count = 2;
		//else if (width < 1240) count = 3;

		return count;
	}

	/*------------------------------------*\
	 #Text typing animation
	\*------------------------------------*/
	$('.typed-text').each(function()
    {
		var strings = [];
		$(this).find('p').each(function(){strings.push(this.innerHTML);});
		$(this).find('p').remove();
		$(this).typed({
			strings: strings,
			typeSpeed: 50,
			backDelay: 1500,
			loop: true
		});
	});



	/*------------------------------------*\
	 #Form submissions
	\*------------------------------------*/
	$("#form-contact-us").on('submit', function(ev)
	{
        if ( ! $("#form-contact-us").validationEngine('validate'))
		{
			ev.preventDefault();
			setTimeout(removeBubbles, 5000);
			return false;
        }
    });

    $('[action="frontend/formprocessor/"], [action="/frontend/formprocessor/"]').on('submit', function() {
        // If the form is inside a modal box (which is fixed position), don't try scrolling to the fields.
        // // Scrolling would only impact what's behind the form
        var scroll = ($(this).parents('.ib-modal').length == 0);

        if (!$(this).validationEngine('validate', {scroll: scroll})) {
            $(window).trigger('resize');
            return false;
        }
    });

    $(document).on('click', '.formError', function() {
        $(this).remove();
    });





    /*------------------------------------*\
     #Modals
    \*------------------------------------*/
    // Open when trigger is clicked
    $('body').on('click', '[data-toggle="ib-modal"]', function() {
        $($(this).data('target'))
            .show()
            .trigger(':ib-modal-open');
    });

    // Dismiss when the blackout is clicked
    $(document).on('click', '.ib-modal', function(ev) {
        var $clicked_element = $(ev.target);
        // The modal box itself is inside the blackout. If it or a child element is clicked, do nothing.
        if ( ! $clicked_element.hasClass('ib-modal-dialog') && ! $clicked_element.parents('.ib-modal-dialog').length) {
            $(this)
                .hide()
                .trigger(':ib-modal-close');
        }
    });

    // Dismiss via close button
    $(document).on('click', '.ib-modal-close', function() {
        $(this).parents('.ib-modal')
            .hide()
            .trigger(':ib-modal-close');
    });

    // Dismiss when "Esc" is pressed
    $(document).keyup(function(ev) {
        if (ev.keyCode == 27) {
            $('.ib-modal')
                .hide()
                .trigger(':ib-modal-close');
        }
    });

    // When a modal opens...
    $(document).on(':ib-modal-open', '.ib-modal', function() {
        // Flag the body (so that it knows to disable scrolling, etc.)
        $('body').addClass('ib-modal-open');
    });

    // When a model closes...
    $(document).on(':ib-modal-close', '.ib-modal', function() {
        // If no modals are left open, remove the flag from the body
        if ($('.ib-modal:visible').length == 0) {
            $('body').removeClass('ib-modal-open');
        }
    });


    // Keep these two fields in sync
    $('#campaign-name').on('change', function() {
        $('#campaign_form_name').val(this.value);
    });

    $('#campaign_form_name').on('change', function() {
        $('#campaign-name').val(this.value);
    });

    $('.simplebox-background_image').each(function() {
        var image = $(this).find('img').attr('src');
        if (image) {
            $(this).parents('.simplebox').css('background-image', 'url('+image+')');
        }
    });




    /*------------------------------------*\
     #Table column toggle
    \*------------------------------------*/
    $('[data-table_toggle]').on('click', 'button', function()
    {
        var $selected = $(this);
        var $toggle   = $selected.parents('[data-table_toggle]');
        var $table    = $($toggle.data('table_toggle'));
        var $options  = $toggle.find('button');

        // Hide all toggleable columns
        var toggleable_column_count = $toggle.find('.simplebox-column').length;
        var column_number           = $selected.parents('.simplebox-column').index();
        var position_from_end       = toggleable_column_count - column_number;
        var $th                     = $table.find('th:not(:only-child):nth-last-child('+position_from_end+')');
        var $heading_cells          = $th.parents('tr').find('td, th');

        $table.find('th:not(:only-child)').addClass('hidden--mobile').removeClass('active-column-heading');
        for (var i = 1; i <= toggleable_column_count; i++) {
            $table.find('td:nth-last-child('+i+')').addClass('hidden--mobile');
        }

        // Show the selected column
        $table.find('td:nth-last-child('+position_from_end+')').removeClass('hidden--mobile');

        // Show the selected heading and expand it to fill the table
        $th
            .removeClass('hidden--mobile')
            .attr('colspan', $heading_cells.length)
            .data('colspan', $heading_cells.length)
            .attr('data-colspan', $heading_cells.length);

        // Highlight the selected option
        var color  = $th.css('background-color');
        var color2 = $th.find('span').css('color') || $th.find('h1, h2, h3, h4, h5, h6, p').css('color') || $th.css('color');

        $options.removeClass('active').css('background-color', '').css('color', '');
        $selected.addClass('active').css('background-color', color).css('color', color2);

        $th.addClass('active-column-heading');
    });

    // If the window is resized to tablet and higher, no columns will be hidden, so remove the heading colspan
    $(window).resize(function() {
        if (window.outerWidth >= 768) {
            $('th[data-colspan]').removeAttr('colspan');
        } else {
            $('th[data-colspan]').each(function(index, element) {
                $(element).attr('colspan', $(element).data('colspan'));
            });

        }
    });

    // Ensure an option is selected by default
    if ($('[data-table_toggle] button.active').length == 0) {
        $('[data-table_toggle] .simplebox-column:first button:last').trigger('click');
        $(window).trigger('resize');
    }




});/*document end*/

// Hide the pop-up bubbles from the jQuery Validation
function removeBubbles() {
	$('.formError').each(function (i, e) {
		document.body.removeChild(e);
	});
}

