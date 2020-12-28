/* Namespace for general CMS data */
var cms_ns = {};
cms_ns.modified = false;
cms_ns.modified_inputs = [];

/*
 * Notifying the user of unsaved changes
 */
window.onload = function()
{
	// When the user tries leaving the page, check for unsaved changes and notify them, if any.
	window.addEventListener('beforeunload', function(ev)
	{

		// If the link to leave the page has the class "skip-save-warning", proceed without giving the warning
		if (ev && ev.srcElement && $(ev.srcElement.activeElement).hasClass('skip-save-warning'))
		{
			return undefined;
		}

		// If no changes have been made, do nothing
		if ( ! cms_ns.modified)
		{
			return undefined;
		}

		// Set a custom message. This will not work in Firefox, which only uses its default message.
		var message = 'You have made changes to the following form fields. Are you sure you wish to leave this page without saving?';

		// Add list of changed fields to the message (max 5)
		for (var i = 0; i < cms_ns.modified_inputs.length && i < 5; i++)
		{
			message += "\n"+cms_ns.modified_inputs[i].name;
		}

		// Show the message
		(ev || window.event).returnValue = message; //Gecko + IE
		return message; //Gecko + Webkit, Safari, Chrome etc.
	});
};
// When a form field is changed, record that a change has been made
$(document).on('change', 'form :input', function(ev)
{
	// Check that the field has not been specifically excluded
	// Check that the change was made by a human.
	// Check that the field has a name. (Unnamed input is not usually sent to the server.)
	if ( ! $(this).hasClass('save-warning-exclude') && (ev.originalEvent || $(this).find('\+ .cke').length > 0) && this.name != '')
	{
		cms_ns.modified = true ;
		cms_ns.modified_inputs.push(this);
	}
});

// If the user clicks a save button or a delete confirmation, clear record of unsaved changes
$(document).on('click mouseup', '[class*="save"], [type="submit"], .btn[id*="save"], .btn[name="save"], .btn:contains("Save"), .btn:contains("Complete"), .modal .btn:contains("Delete"), .btn-primary, .btn-success, .btn-info', function()
{
	cms_ns.modified = false;
	cms_ns.modified_inputs = [];
});

// If the user submits a form, clear record of unsaved changes
$('form').submit(function()
{
	cms_ns.modified = false;
	cms_ns.modified_inputs = [];
});

// Remove a specific field from the list of modified fields
cms_ns.clear_modified_input = function(input_name)
{
	if (cms_ns.modified_inputs[input_name])
	{
		delete cms_ns.modified_inputs[input_name];
		// If this was the only modified field, set modified to false
		if (cms_ns.modified_inputs.length > 0)
		{
			cms_ns.modified = false;
		}
	}
};


if( !window.disableScreenDiv ){
	window.disableScreenDiv = document.createElement( "div" );
	window.disableScreenDiv.hide = true;
	window.disableScreenDiv.style.display = "block";
	window.disableScreenDiv.style.position = "fixed";
	window.disableScreenDiv.style.top = "0px";
	window.disableScreenDiv.style.left = "0px";
	window.disableScreenDiv.style.right = "0px";
	window.disableScreenDiv.style.bottom = "0px";
	window.disableScreenDiv.style.textAlign = "center";
	window.disableScreenDiv.style.zIndex = 9999;
	window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;background-color:#ffffff;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
			'<div class="ajax_loader_icon_inner" style="position:absolute;top:50%;left:50%;margin:-16px;z-index:2;width: 32px;height: 32px;margin: 0 auto;background-image: url(\'/engine/shared/img/ajax-loader.gif\')"></div>';
	window.disableScreenDiv.autoHide = true;
	document.body.appendChild(window.disableScreenDiv);

	$(document).ready(function(){
		$(document).ajaxStart(function(){
			if( window.disableScreenDiv  && window.disableScreenDiv.hide ) {
				window.disableScreenDiv.style.visibility = "visible";
			}
		});
		$(document).ajaxStop(function(){
			if( window.disableScreenDiv  && window.disableScreenDiv.autoHide ){
				window.disableScreenDiv.style.visibility = "hidden";
			}
		});
		$(document).ajaxSend(function(e, x, o){
			// console.log("ajax-start:" + o.url);
		});
		$(document).ajaxComplete(function(e, x, o){
			// console.log("ajax-stop:" + o.url);
		});
	});
}

$(document).ready(function()
{
	// Visibility toggles
	$('body')
		.on('click', '.popup_close', function(){
			$(this).closest('.sectionOverlay').hide();
		})
		.on('click', '.toggleBtn', function () {
			$(this).toggleClass('open');
			$(this).siblings('.toggleContent').slideToggle();
			return false;
		});

	// Dismiss when clicked away from (popup)
	$(document).on('click', '.sectionOverlay', function(ev)
	{
		var $clicked_element = $(ev.target);
		if ( ! $clicked_element.hasClass('sectioninner') && ! $clicked_element.parents('.sectioninner').length)
		{
			$(this).hide();
		}
	});

	// Dismiss when clicked away from (toggle content)
	$(document).on('click', function(ev)
	{
		if ( ! $(ev.target).closest('.toggleContent, .toggleBtn').length) {
			$('.toggleContent:visible').slideToggle();
			$('.toggleBtn').removeClass('open');
		}
	});

	// Dismiss when "Esc" is pressed
	$(document).keyup(function(ev)
	{
		if (ev.keyCode == 27)
		{
			// Popups
			$('.sectionOverlay').hide();

			// Toggle content
			$('.toggleContent:visible').slideToggle();
			$('.toggleBtn').removeClass('open');
		}
	});

    // Stop dropdown from dismissing when clicking inside the dropdown
    $(document).on('click', '.dropdown[data-autodismiss="false"] .dropdown-menu', function() {
        $(this).parents('.dropdown').addClass('open').find('[data-toggle]').attr('aria-expanded', 'true');
    });

	$(document).on('click', '.button.wishlist_add', function(){
		var button = this;
		var contact_id = $(this).data('contact_id');
		var schedule_id = $(this).data('schedule_id');

		$.post(
				'/admin/contacts3/wishlist_add',
				{
					contact_id: contact_id,
					schedule_id: schedule_id
				},
				function (response) {
					$(button).addClass("hidden");
					$(".button.wishlist_remove").each(function(){
						if ($(this).data("schedule_id") == schedule_id) {
							$(this).removeClass("hidden");
						}
					})
				}
		);
	});

	$(document).on('click', '.button.wishlist_remove', function(){
		var button = this;
		var contact_id = $(this).data('contact_id');
		var schedule_id = $(this).data('schedule_id');

		$.post(
				'/admin/contacts3/wishlist_remove',
				{
					contact_id: contact_id,
					schedule_id: schedule_id
				},
				function (response) {
					$(button).addClass("hidden");
					$(".button.wishlist_add").each(function(){
						if ($(this).data("schedule_id") == schedule_id) {
							$(this).removeClass("hidden");
						}
					})
				}
		);
	});

	/** Login modals **/

	// Collapse the log-in-to-continue overlay, when clicked away from
	$('#login-overlay').on('click', function(ev)
	{
		var $clicked = $(ev.target);
		if ( ! $clicked.hasClass('guest-user-wrapper, sectionOverlay') && ! $clicked.parents('.guest-user-wrapper, .sectionOverlay').length)
		{
			$(this).collapse('hide');
		}
	});


	// Open login modal
	$(document).on('click', '[data-toggle="login_modal"]', function(ev)
	{
		ev.preventDefault();
		$('.sectionOverlay').hide();
		$('[data-toggle="tab"][href="'+$(this).data('target')+'"]').tab('show');
		$('#login_popup_open').css('display', 'block');
		$('html, body').css('overflowY', 'hidden');
	});

	// Dismiss when "close" button is clicked
	$('.sectionOverlay').find('.basic_close, .cancel').click(function()
	{
		$(this).parents('.sectionOverlay').css('display', 'none');
		$('html, body').css('overflowY', '');
	});

	// Dismiss when clicked away from
	$(document).on('click', '.sectionOverlay', function(ev)
	{
		var $clicked_element = $(ev.target);
		if ( ! $clicked_element.hasClass('sectioninner') && ! $clicked_element.parents('.sectioninner').length)
		{
			$(this).css('display', 'none');
			$('html, body').css('overflowY', '');
		}
	});

	// Dismiss when "Esc" is pressed
	$(document).keyup(function(ev)
	{
		if (ev.keyCode == 27)
		{
			var $overlay = $('.sectionOverlay');
			if ($overlay.is(':visible'))
			{
				// Dismiss regular overlays
				$overlay.css('display','none');
				$('html, body').css('overflowY', '');
			}
			else
			{
				// If there are no regular overlays, dismiss the log-in-to-continue overlay
				$('#login-overlay').collapse('hide');
			}
		}
	});




	/*------------------------------------*\
	 #Sliders
	\*------------------------------------*/
	var $banner = $('#home-banner-swiper');
	if($banner.length && $banner.data('slides') > 1)
	{
		new Swiper('#home-banner-swiper',{
			autoplay: $banner.data('autoplay'),
			direction: $banner.data('direction'),
			effect: $banner.data('effect'),
			speed: $banner.data('speed'),
			loop: true,
			pagination: '#home-banner-swiper .swiper-pagination',
			paginationClickable: true,
			nextButton: '#home-banner-swiper .swiper-button-next',
			prevButton: '#home-banner-swiper .swiper-button-prev'
		});

		new Swiper('#news-slider', {
			autoplay: 5000,
			loop: true,
			pagination: '#news-slider .swiper-pagination',
			paginationClickable: true,
			nextButton: '#news-slider .swiper-button-next',
			prevButton: '#news-slider .swiper-button-prev'
		});

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

	}

	function calculate_slides_per_view()
	{
		var width = window.innerWidth;
		var count = 4;
		if      (width <  768) count = 1;
		else if (width <  990) count = 2;
		else if (width < 1240) count = 3;

		return count;
	}




	/*------------------------------------*\
	 #Slide-in menus
	\*------------------------------------*/
	// Open when trigger is clicked
	$(document).on('click', '[data-toggle="slidein"][data-target]', function()
	{
		var $target = $(this.dataset.target);

		if ($target.hasClass('slidein--active'))
		{
			$target.removeClass('slidein--active');
			$('body').removeClass('body--slidein');
		}
		else
		{
			$target.addClass('slidein--active');
			$('body').addClass('body--slidein');
		}
	});

	// Dismiss when close button is clicked
	$(document).on('click', '[data-dismiss="slidein"]', function()
	{
		$(this).parents('.slidein').removeClass('slidein--active');
		$('body').removeClass('body--slidein');
	});

	// Dismiss when clicked away from
	$(document).on('click', '.slidein', function(ev)
	{
		var $clicked_element = $(ev.target);
		if ($clicked_element.hasClass('slidein'))
		{
			$clicked_element.removeClass('slidein--active');
			$('body').removeClass('body--slidein');
		}
	});

	// Dismiss when "Esc" is pressed
	$(document).keyup(function(ev)
	{
		if (ev.keyCode == 27)
		{
			$('.slidein--active').removeClass('slidein--active');
			$('body').removeClass('body--slidein');
		}
	});



    /*------------------------------------*\
     #Validation
    \*------------------------------------*/
    $('.validate-on-submit').on('submit',function() {
        if ( ! $(this).validationEngine('validate')) {
            return false;
        }
    });

    $(document).on('click', '.alert .close-btn', function() {
        $(this).parents('.alert').remove();
    });

    $(document).ready(function() {
        setTimeout(function() {
            $('.alert.popup_box').addClass('fadeOutRight');
            setTimeout(function(){ $('.alert.popup_box .close').trigger('click'); }, 1000);
        }, 12000);
    });




	/*------------------------------------*\
	 #Menus
	\*------------------------------------*/
	$('.header-menu-expand').on('click', function(ev)
	{
		ev.preventDefault();
		var $menu  = $(this).find('\+ .header-menu');
		var visible = $menu.is(':visible');

		// If any other menus are expanded, hide them
		$('.header-menu').hide();
		$('.header-menu-expand').removeClass('expanded');

		// Show the menu if it was hidden before the expand button was clicked
		if ( ! visible)
		{
			$menu.show();
			$(this).addClass('expanded');
		}
	});

	// Dismiss when clicked away from
	$(document).on('click', function(ev) {
		if ( ! $(ev.target).closest('.header-menu, .header-menu-expand').length) {
			$('.header-menu').hide();
			$('.header-menu-expand').removeClass('expanded');
		}
	});

	$('.level2 .submenu-expand').on('click', function()
	{
		var $li = $(this).parents('li');
		$li.hasClass('expanded') ? $li.removeClass('expanded') : $li.addClass('expanded');
	});




	/*------------------------------------*\
	 #Calendar
	\*------------------------------------*/
	$(document).ready(function() {
		if ($("#sidebar-calendar").length > 0) {
			$("#sidebar-calendar").eventCalendar({
				eventsjson: '/frontend/courses/get_calendar_event_feed',
				jsonDateFormat: 'human',
				cacheJson: false,
				dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'],
				startWeekOnMonday: false
			});
		}
	});




	/*------------------------------------*\
	 #Share buttons
	\*------------------------------------*/
	$('.share_button--facebook, .share_button--twitter').on('click', function(ev)
	{
		ev.preventDefault();
		var width  = 500;
		var height = 300;
		var left   = screen.width/2-250;
		var url    = this.href;

		window.open(url, 'newwindow', 'width='+width+', height='+height+', left='+left);
	});




	/*------------------------------------*\
	 #Banner search
	\*------------------------------------*/

	// fill search form from search history
	$('.search_history').on('click',function (e) {
        if( ! $(e.target).hasClass("remove_search_history") )
        {
            var $this = $(this);
            $('#banner-search-location').val($this.data('location_name'));
            $('#banner-search-location-id').val($this.data('location_id'));

            $('#banner-search-subject').val($this.data('subject_name'));
            $('#banner-search-subject-id').val($this.data('subject_id'));
            $('#banner-search-year-id').val($this.data('year-id'));
            $('#banner-search-category-id').val($this.data('category-id'));

			// trigger form submit
            $('#banner-search-form').submit();
        }

    });

	// hide current search history and remove it from cookies
    $('.remove_search_history').on('click',function () {
    	var parent_span =  $(this).parent('.search_history');
        var location_id = parent_span.data('location_id');
        var course_id = parent_span.data('course_id');
    	parent_span.hide();
        var new_cookie_array = JSON.parse(Cookies.get('last_search_parameters'));
        new_cookie_array = $.grep(new_cookie_array, function(value) {
            return (value.location != location_id || value.course != course_id);
        });
        Cookies.set('last_search_parameters', JSON.stringify(new_cookie_array));
    });


	// Display the dropout menu, when its input box is focused
	$('[data-drilldown]').focus(function()
	{
		$('.search-drilldown.active').removeClass('active');
		$(this.getAttribute('data-drilldown')).addClass('active');
	});

	// Function to check if a list is empty.
	// A list can be empty due to a selection not being made or due to no results being found.
	// A different message is displayed for each.
	$.fn.notify_if_empty = function()
	{
		if (this.children(':visible').length == 0) {

			// Check if this column is waiting on a filter (filter column exists and does not have a selection.)
			var $filtered_by = $(this.data('filtered_by'));
			var awaiting_filter = ($filtered_by.length && ! $filtered_by.find('.active').length);

			if (awaiting_filter)
			{
				this.find('~ .search-drilldown-no_results').addClass('hidden');
				this.find('~ .search-drilldown-awaiting_selection').removeClass('hidden');
			}
			else
			{
				this.find('~ .search-drilldown-awaiting_selection').addClass('hidden');
				this.find('~ .search-drilldown-no_results').removeClass('hidden');
			}
		}
		else
		{
			this.find('~ .search-drilldown-awaiting_selection').addClass('hidden');
			this.find('~ .search-drilldown-no_results').addClass('hidden');
		}

		return this;
	};

	// Filter results, as the user types
	$('[data-type_search]').on('keyup', function()
	{
		var term = this.value;
		var lc_term = term.toLowerCase();
		var re = new RegExp("^" + term, "gi") ;
		var $list = $(this.getAttribute('data-type_search'));

		$list.find('li').each(function()
		{
			var item = this.getElementsByTagName('a')[0];

			// remove tags (remove bolding from previous search terms)
			item.innerHTML = item.innerHTML.replace(/(<([^>]+)>)/ig, '');

			if (item.innerHTML.toLowerCase().indexOf(lc_term) == -1)
			{
				// Hide irrelevant items
				this.style.display = 'none';
			}
			else
			{
				// Show relevant items and bold the keyword
				this.style.display = '';
				item.innerHTML = item.innerHTML.replace(re, function(str) {return '<b>'+str+'</b>'});
			}
		});

		$list.notify_if_empty();
	});

	// When a location is selected, put it in the search bar and dismiss the menu
	$('#location-drilldown-location-list').on('click', 'a', function(ev)
	{
		ev.preventDefault();
		$("#location-drilldown-location-list li a").removeClass("selected");
		$(this).addClass("selected");
		var location_ids = [] , location_html = [];
		// Put the selected value, minus HTML tags in the search bar
		if( this.getAttribute('data-id') === 'all' )
		{
			$('#location-drilldown-location-list li a.location').each( function(){
				location_ids.push(this.getAttribute('data-id'));
				location_html.push(this.innerHTML.replace(/(<([^>]+)>)/ig, '') );
			});
			$('#banner-search-location-id')[0].value = location_ids;
			$('#banner-search-location')[0].value = location_html;
		}
		else{
			location_ids.push(this.getAttribute('data-id'));
			$('#banner-search-location-id')[0].value = this.getAttribute('data-id');
			$('#banner-search-location')[0].value = this.innerHTML.replace(/(<([^>]+)>)/ig, '');
		}

		$.post(
			'/frontend/courses/ajax_get_years',
			{
				location_ids: location_ids
			},
			function (response) {
				var $ul = $("#subject-drilldown-year-list");
				$ul.html("");
				for (var i in response) {
					var year = response[i];
					$ul.append('<li><a class="" data-id="' + year.id + '">' + year.year + '</a>');
				}
			}
		)
		// Dismiss the current dropout and move on to the next one
		$('#banner-search-subject').focus();
	});

    // When a year is selected, show the relevant course types (categories)
    $('#subject-drilldown-year-list').on('click', 'a', function(ev)
    {
        ev.preventDefault();
        var $selected = $(this);
        var year_id;

        if ($selected.hasClass('active'))
        {
            // Dismiss, if clicked when already active
            $selected.removeClass('active');
            year_id = '';
        }
        else
        {
            $('#subject-drilldown-year-list').find('.active').removeClass('active');
            $selected.addClass('active');
            year_id = this.getAttribute('data-id');
        }

        // add year_id to hidden input
        $('#banner-search-year-id').val(year_id);

		var location_ids = $("#banner-search-location-id").val().split(',');
        $.post(
			'/frontend/courses/ajax_get_categories_from_year/'+year_id,
			{location_ids: location_ids}
		).done(function(data)
        {
            var category_ids = year_id ? JSON.parse(data) : [];
            var $list = $('#subject-drilldown-category-list');

            var category_id;

            // Remove previous selection / results
            $('#subject-drilldown-subject-list').html('');
            $list.find('.active').removeClass('active');

            // Hide class types that don't match the search results
            $list.find('a').each(function()
            {
                category_id = parseInt($(this).attr('data-id'));
                this.parentElement.style.display = (category_ids.indexOf(category_id) != -1) ? '' : 'none';
            });

            // For smaller screens, position the class type list, relative to the selected subject
            if (window.innerWidth < 768)
            {
                var selected_position = $selected.parents('li').position();
                $list.parents('.search-drilldown-column').css('top', selected_position.top + $selected.parents('li').height());

                if (subject_id)
                    $list.parents('.search-drilldown-column').show();
                else
                    $list.parents('.search-drilldown-column').hide(); // Dismiss, if clicked when already active
            }

            $list.removeClass('hidden').notify_if_empty();
        });
    });

    // When a class type (category) is selected, show the relevant courses
    $('#subject-drilldown-category-list').on('click', 'a', function(ev)
    {
        ev.preventDefault();
        var $selected = $(this);
        var year_id = $('#subject-drilldown-year-list').find('.active').attr('data-id');
        var category_id;

        if ($selected.hasClass('active'))
        {
            // Dismiss, if clicked when already active
            $selected.removeClass('active');
            category_id = '';
        }
        else
        {
            $('#subject-drilldown-category-list').find('.active').removeClass('active');
            $selected.addClass('active');
            category_id = $(this).attr('data-id');
        }

        // add category_id to hidden input
        $('#banner-search-category-id').val(category_id);

		var location_ids = $("#banner-search-location-id").val().split(',');

        $.ajax({
            url: '/frontend/courses/ajax_get_subjects',
            data: { year_id: year_id, category_id: category_id, location_ids: location_ids }
        }).done(function(data)
        {
            data = category_id ? JSON.parse(data) : [];
            var $list = $('#subject-drilldown-subject-list');
            var html = '';


            for (var i = 0; i < data.length; i++)
            {
                html += '<li><a href="#" data-id="'+data[i].id+'">'+data[i].name+'</a></li>';
            }

            // For smaller screens, position the class type list, relative to the selected subject
            if (window.innerWidth < 768)
            {
                var selected_position = $selected.parents('li').position();
                $list.parents('.search-drilldown-column').css('top',
                    selected_position.top +
                    $selected.parents('li').height() +
                    $('.search-drilldown-column--category').position().top
                );

                if (category_id)
                    $list.parents('.search-drilldown-column').show();
                else
                    $list.parents('.search-drilldown-column').hide(); // Dismiss, if clicked when already active
            }

            $list.html(html);
            $list.notify_if_empty();
        });
    });

    // When a subject is selected, put it in the search bar and dismiss the menu
    $('#subject-drilldown-subject-list').on('click', 'a', function(ev)
    {
        ev.preventDefault();

        $('#subject-drilldown-subject-list').find('.active').removeClass('active');
        $(this).addClass('active');

        //$('#banner-search-subject-id')[0].value = this.getAttribute('data-id');
        $('#banner-search-subject-id').val(this.getAttribute('data-id'));
        $('#banner-search-subject')[0].value = this.innerHTML;

        $('#subject-drilldown').removeClass('active');
    });

	// Dismiss via the close icon
	$('.search-drilldown-close').on('click', function()
	{
		$(this).parents('.search-drilldown').removeClass('active');
	});

	// Dismiss when clicked away from
	$(document).on('click', function(ev) {
		if ( ! $(ev.target).closest('.search-drilldown, [data-drilldown]').length) {
			$('.search-drilldown').removeClass('active');
		}
	});




	/*------------------------------------*\
	 #Search results
	\*------------------------------------*/
	var $course_results = $('#course-results');

	/* Toggle visibility of sidebar filters */
	$('.sidebar-section-collapse').on('click', function(ev)
	{
		ev.preventDefault();
		$(this).parents('.sidebar-section').find('.sidebar-section-content').slideToggle(0);
	});

	/* Toggle between list and grid mode */
	$course_results.on('change', '[name="course_list_display"]', function()
	{
		if (this.value == 'grid')
		{
			$('.course-list').addClass('course-list--grid').removeClass('course-list--list');
		}
		else
		{
			$('.course-list').addClass('course-list--list').removeClass('course-list--grid');
		}
	});

	// Only grid mode exists on small screens
	$(window).resize(function()
	{
		if (window.innerWidth < 1024)
		{
			$('#course-list-display_grid').prop('checked', true).trigger('change');
		}
	});

	/* Display fee when a schedule is selected */
	$course_results.on('change', '.course-widget-schedule', function()
	{
		var $price        = $(this).parents('.course-widget').find('.course-widget-price');
		var $price_amount = $price.find('.course-widget-price-amount');
		var fee           = $(this).find(':selected').attr('data-fee');

		if (fee == 0)
		{
			$price.hide();
			$price_amount.html('');
		}
		else
		{
			$price_amount.html(fee);
			$price.show();
		}
	});

	/*------------------*/
	/* Search filters */
	/*-----------------*/

    var $filters = $('#available_results-filters');

    // If data came from search result
    if ($filters.find('.search-filter-checkbox:checked')) {
        update_new_search_results();
    }

    // Clear all search criteria
    $('#search-filters-clear').on('click', function()
    {
        $('.search-filter-checkbox').prop('checked', false);
        $('.search-filter-dropdown').removeClass('filter-active');
        $('.search-filter-amount').html('');
        $(this).removeClass('visible');

        update_new_search_results();
    });

    // Add criteria on filters click
    var search_filter_timer = null;
    $filters.on('change', '.search-filter-checkbox', function()
    {
        var $dropdown = $(this).parents('.search-filter-dropdown');
        var $amount   = $dropdown.find('.search-filter-amount');
        var $clear    = $('#search-filters-clear');
        var amount    = $dropdown.find('.search-filter-checkbox:checked').length;

        if (amount == 0) {
            $dropdown.removeClass('filter-active');
            $amount.html('');
        } else {
            $amount.html(amount);
            $dropdown.addClass('filter-active');
        }

        // Show the "clear filters" option, if at least one filter has been selected
        if ($filters.find('.search-filter-checkbox:checked').length) {
            $clear.addClass('visible');
        } else {
            $clear.removeClass('visible');
        }

        // Blackout appears when the search results are loading.
        // Give the user half a second to check more boxes before then.
        clearTimeout(search_filter_timer);
        search_filter_timer = setTimeout(update_new_search_results, 500);
    });

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    // When a checkbox is changed, add/remove its entry in the search criteria
    $('.sidebar-filter-options').find('[type="checkbox"]').on('change', function(ev, arg)
    {
        arg = (typeof arg !== 'undefined') ? arg : null;

        // Add or remove the item from "search criteria" section
        if (this.checked)
        {
            var $li = $('<li class="search-criteria-li">'+$('#course_filter_criteria_template').html()+'</li>');
            $li
                .data('id', this.getAttribute('data-id'))
                .attr('data-id', this.getAttribute('data-id'));
            $li.find('.search-criteria-category').html(this.getAttribute('data-category'));
            $li.find('.search-criteria-value').html(this.getAttribute('data-value'));

            $li.insertBefore('#search-criteria-reset-li')
        }
        else
        {
            $('.search-criteria-li[data-id="'+this.getAttribute('data-id')+'"]').remove();
        }

        // Use "no_update" to only update the search criteria section and not perform
        // the AJAX call to update the results. (Only used on the initial page load.)
        if (arg != 'no_update')
        {
            update_search_results();
        }
    });

	var $search_criteria = $('#course_filter_criteria');

	// When a checkbox is changed, add/remove its entry in the search criteria
	$('.sidebar-filter-options').find('[type="checkbox"]').on('change', function(ev, arg)
	{
		arg = (typeof arg !== 'undefined') ? arg : null;

		// Add or remove the item from "search criteria" section
		if (this.checked)
		{
			var $li = $('<li class="search-criteria-li">'+$('#course_filter_criteria_template').html()+'</li>');
			$li
				.data('id', this.getAttribute('data-id'))
				.attr('data-id', this.getAttribute('data-id'));
			$li.find('.search-criteria-category').html(this.getAttribute('data-category'));
			$li.find('.search-criteria-value').html(this.getAttribute('data-value'));

			$li.insertBefore('#search-criteria-reset-li')
		}
		else
		{
			$('.search-criteria-li[data-id="'+this.getAttribute('data-id')+'"]').remove();
		}

		// Use "no_update" to only update the search criteria section and not perform
		// the AJAX call to update the results. (Only used on the initial page load.)
		if (arg != 'no_update')
		{
			update_search_results();
		}
	}).trigger('change', ['no_update']);

	// When a criteria item remove icon is clicked, remove the item and un-check its checkbox
	$search_criteria.on('click', '.search-criteria-remove', function()
	{
		var $li = $(this).parents('li');
		// Uncheck the corresponding checkbox and trigger its change action
		var id = $li.data('id');
		$('input[data-id="'+id+'"]').prop('checked', false).trigger('change');

		// Course results don't have a checkbox in the sidebar, so this is handled separately
		if ($li.find('.filter-course_ids').length)
		{
			$li.remove();
			update_search_results();
		}
	});

	// Keyword search
	$('#course-filter-keyword').on('change', update_search_results);

	// Navigate through pagination results
	$course_results.on('click', '.pagination a:not(.disabled)', function(ev)
	{
		ev.preventDefault();
		$course_results.find('.pagination .current').removeClass('current');
		$(this).addClass('current');
		update_search_results();
		$course_results[0].scrollIntoView();
	});

	function update_search_results()
	{
		var $sidebar     = $('#sidebar');
		var sort         = $('[name="course_list_sort"]:checked').val();
		var display      = $('[name="course_list_display"]:checked').val();
		var keywords     = $('#course-filter-keyword').val();
		var page         = $('#search_results-pagination').find('.current').attr('data-page');
		var location_ids = [];
		var year_ids     = [];
		var category_ids = [];
		var level_ids    = [];
		var course_ids   = [];

		page = page ? page : 1;

		$sidebar.find('[name="location_ids[]"]:checked').each(function(){location_ids.push(this.value);});
		$sidebar.find('[name="year_ids[]"]:checked'    ).each(function(){    year_ids.push(this.value);});
		$sidebar.find('[name="category_ids[]"]:checked').each(function(){category_ids.push(this.value);});
		$sidebar.find('[name="level_ids[]"]:checked'   ).each(function(){   level_ids.push(this.value);});
		$('.filter-course_ids'                         ).each(function(){  course_ids.push(this.value);});

		$.ajax(
		{
			url     : '/frontend/courses/ajax_filter_results',
			data    : {
				'location_ids' : location_ids,
				'year_ids'     : year_ids,
				'category_ids' : category_ids,
				'level_ids'    : level_ids,
				'course_ids'   : course_ids,
				'keywords'     : keywords,
				'sort'         : sort,
				'display'      : display,
				'page'         : page,
				'reminder'     : 0,
				'timeslots'    : 1
			},
			type     : 'post',
			dataType : 'json'
		}).done(function(result)
			{
				$('#course-results').html(result);
			});
	}




	/*------------------------------------*\
	 #Course details
	\*------------------------------------*/
	$("#schedule_selector").on("change", function()
	{
		var id = $(this).val();
		var event_id = (this.selectedIndex != -1) ? $(this.options[this.selectedIndex]).data('event_id') : '';
		var $price_wrapper = $('.price_wrapper');

		$("#enquire-course, #book-course").prop("disabled", true);
		if (id.length > 0)
		{
			$.post('/frontend/courses/get_schedule_price_by_id', {sid: id, event_id: event_id}, function (data)
			{
				$price_wrapper.find('.price').html(data.price);
				$price_wrapper.css('visibility', 'visible');
				$("#enquire-course, #book-course").prop("disabled", false);
			});
		}
		else
		{
			$price_wrapper.css('visibility', 'hidden');
			$('#trainer_name').hide().html('');
			$price_wrapper.find('.price').html('');
		}
	}).trigger('change');




	/*------------------------------------*\
	 #Booking/enquiry form
	\*------------------------------------*/
	$('#booking_form-student_date_of_birth').datetimepicker({ format: 'd/m/Y', timepicker: false });

	$('.booking-use_guardian-toggle').on('change', function()
	{
		var $target = $($(this).data('target'));
		var $target_sync_with = $($target.data('sync_with'));
		if (this.checked)
		{
			// Show the fields and disable synchronisation with the guardian section
			$target.removeClass('hidden');
			$target.data('sync', 'off').attr('data-sync', 'off');
			$target_sync_with.data('sync', 'off').attr('data-sync', 'off');
		}
		else
		{
			// Hide the fields, enable synchronisation with the guardian section and perform a sync
			$target.addClass('hidden');
			$target.data('sync', 'on').attr('data-sync', 'on');
			$target_sync_with.data('sync', 'on').attr('data-sync', 'on');
			$target_sync_with.find('[data-field]').each(function(){$(this).trigger('change');});
		}
	});

	// Synchronise fields in two linked sections
	$('[data-sync="on"]').find('[data-field]').on('change', function()
	{
		// The two sections to sync
		var $section = $(this).parents('[data-sync="on"]');
		var $sync    = $($section.data('sync_with')+'[data-sync="on"]');

		// When a field is changed, ensure the field with the same data-field in the other section is also changed
		$sync.find('[data-field="'+this.getAttribute('data-field')+'"]').val(this.value);
		$sync.find('[data-field="'+this.getAttribute('data-field')+'"][type="checkbox"]').prop('checked', this.checked);
	});

	$("#booking_form-guardian_relationship_to_student").on("change", function(){
		if(this.value=='other'){
			$('#booking_form-guardian_relationship_to_student_other').prop('disabled', false);
			$(".form-group.guardian_relationship_to_student_other").css("visibility", "visible");
		} else {
			$('#booking_form-guardian_relationship_to_student_other').prop('disabled', true);
			$(".form-group.guardian_relationship_to_student_other").css("visibility", "hidden");
		}

	});


	$(document).on('change',".paginationCopy > .pagination-new select",function (value) {

		var index=value.target.selectedIndex+1;

		$("#number_of_courses select").eq(0).find("option:nth-child("+index+")").prop('selected', true);

	});

    $("#number_of_courses select").eq(0).on('change',update_new_search_results);




	/*------------------------------------*\
	 #Booking-seating selector
	\*------------------------------------*/
	// Toggle which cart item's seating selector is visible
	$('#seating-selector-select_schedule').on('change', function()
	{
		$('.seating-selector').addClass('hidden');
		$('.seating-selector[data-booking="'+this.value+'"]').removeClass('hidden');
	});

	$('.seating-selector-prev').on('click', function()
	{
		var item_number = $(this).parents('.seating-selector').prev().data('booking');
		$('#seating-selector-select_schedule').val(item_number).trigger('change');
	});

	$('.seating-selector-next').on('click', function()
	{
		var item_number = $(this).parents('.seating-selector').next().data('booking');
		$('#seating-selector-select_schedule').val(item_number).trigger('change');
	});


	// Update pricing when a zone selector is changed
	$('.seating-selector-option-radio').on('change', function()
	{
		var zone_fee  = 0; // The figure
		var $zone_fee = $('#checkout-breakdown-zone_fee'); // The DOM element
		var individual_zone_fee;

		// Calculate the figure
		$('.seating-selector-option-radio:checked').each(function() {
			individual_zone_fee = $(this).data('price');
			zone_fee += (individual_zone_fee && ! isNaN(individual_zone_fee)) ? parseFloat(individual_zone_fee) : 0;
		});

		// Update the HTML
		$zone_fee.html(zone_fee.toFixed(2));
		(zone_fee == 0)
			? $zone_fee.parents('li').addClass('hidden')
			: $zone_fee.parents('li').removeClass('hidden');

		calculate_checkout_total();
	});

	// When the zone is clicked, select its corresponding radio button
	$('.seating-selector-zone-button').on('click', function()
	{
		var $schedule_seating = $(this).parents('.seating-selector');
		$schedule_seating.find('.seating-selector-option-radio[value="'+this.dataset.row_id+'"]')
			.prop('checked', true).trigger('change');
	});

    $("#payment-methods > li").on("click", function(){
        var cc_sms = $(this).data("payment_method");
        $("[name=payment_method]").val(cc_sms);

        $("#checkout-breakdown .booking_fee.cc").addClass("hidden");
        $("#checkout-breakdown .booking_fee.sms").addClass("hidden");

        $("#checkout-breakdown .booking_fee." + cc_sms).removeClass("hidden");

        calculate_checkout_total();
    });

    $("#mobile_verification_send").on("click", function(){
        var btn = this;
        btn.disabled = true;
        var data = {};
        data.amount = $("[name=amount]").val();
        data.mobile = $("[name=charge_mobile_code]").val() + $("[name=charge_mobile_number]").val();
        data.operator = $("[name=charge_mobile_operator]").val();
        $.post(
            "/frontend/bookings/allpoints_create_new_transaction",
            data,
            function (response) {
                if (response.error) {
                    var $clone = $('#checkout-error_message-template').clone();
                    $clone.removeClass('hidden').find('.checkout-error_message-text').html(response.error);
                    $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                    btn.disabled = false;
                } else {
                    $("[name=allpoints_tx_id]").val(response.id);
                    $("#mobile_verification_sent").removeClass("hidden");
                }
            }
        );
    });
});


function calculate_checkout_total()
{
	if (document.getElementById('booking-checkout-form'))
	{
		var subtotal    = parseFloat($('#checkout-breakdown-subtotal'   ).html().replace(/,/g, ''));
		var zone_fee    = parseFloat($('#checkout-breakdown-zone_fee'   ).html().replace(/,/g, ''));
		var discount    = parseFloat($('#checkout-breakdown-discount'   ).html().replace(/,/g, ''));
        var amend_fee   = $("#checkout-amendable_tier")[0].checked ? parseFloat($("li.amend-fee").data('amount')) : 0;
		var booking_fee = 0;
		$('.checkout-breakdown-booking_fee').each (function(){
			if (!$(this).parents("li").hasClass('hidden')) {
				booking_fee += parseFloat($(this).html().replace(/,/g, ''));
			}
		});


		var total = subtotal + zone_fee - discount + booking_fee + amend_fee;

		$('#checkout-breakdown-total').html(total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
		$('#checkout-breakdown-total-field').val(total);

		if (total > 30) {
			$("[aria-controls=payment-tabs-mobile_carrier]").addClass("hidden");
			$("#payment-tabs-mobile_carrier").addClass("hidden");
		} else {
			$("[aria-controls=payment-tabs-mobile_carrier]").removeClass("hidden");
			$("#payment-tabs-mobile_carrier").removeClass("hidden");
		}

		// If the cart has been emptied, redirect the user
		var $items_in_cart = $('#checkout-sidebar-items').find('> div:not(#checkout-item-template) .checkout-item');
		if ($items_in_cart.length == 0)
		{
			// Redirect the user, via a form submission, so post data can be passed
			var form  = document.createElement('form');
			var input = document.createElement('input');

			form.method = 'post';
			form.action = '/available-results.html';
			input.type  = 'hidden';
			input.name  = 'cart_emptied';
			input.value = 1;

			form.appendChild(input);
			document.body.appendChild(form);
			form.submit();
		}
	}
}


$(function(){
    $('#db-student').change(function(){  if(this.checked){  $('#student-info').show() }else {  $('#student-info').hide();}  });
    $('#db-gu-student').on('change', function(){  if(this.checked){  $('#student-info2').show() }else {  $('#student-info2').hide();}  });
}); 

$(window).scroll(function () {
    var body_height = $(".dashboard-wrapper").position();
    var scroll_position = $(window).scrollTop();
	if (body_height) {
		if (body_height.top < scroll_position)
			$("body").addClass("fixed_strip");
		else
			$("body").removeClass("fixed_strip");
	}
});

 $('.history-dropdown').click(function () {
	$(this).toggleClass('current-tab');
	$(this).next(".transactions-history").slideToggle()
		//$(this).siblings('.transactions-history').slideToggle();
	return false;
});
$('.action-btn > a').click(function () {
	$(this).toggleClass('open');
	//$('.action-btn ul').slideUp();
	$(this).siblings('.action-btn ul').slideToggle(500);
	return false;
});

function load_family_members_block(contact_id) {
	if($('#family_block').length){
		$.ajax({
			url: '/frontend/contacts3/ajax_get_family_members/',
		}).done(function(data) {
            $('#family_block').html(data);

			if(contact_id){
				$('#family_block [data-contact_id="'+ contact_id +'"]').addClass('active');
			}
		});
	}
}

function update_new_search_results(callback)
{

	page=$("#pagination-new .bootpag .selected-active-page a").html();
	if(!page)
	{
		page = 1;
	}

	sortBy=$("#number_of_courses select option:selected").attr("value");
    if(!sortBy)
    {
        sortBy=1;
    }

    var $filters = $('#available_results-filters');
    var keywords     = $('#search_keyword').val();
    if($.trim(keywords) == ''){
        keywords = null;
    }

    // var page         = $('#search_results-pagination').find('.current').attr('data-page');
    var location_ids = [];
    var subject_ids  = [];
    var category_ids = [];
    var course_ids   = [];
    var year_ids     = [];
    var level_ids    = [];
    var topic_ids    = [];
	var cycle = [];

    // page = page ? page : 1;

    $filters.find('[data-type="location"]:checked').each(function(index,value){location_ids.push($(value).data('id'));});
    $filters.find('[data-type="subject"]:checked').each(function(index,value){subject_ids.push($(value).data('id'));});
    $filters.find('[data-type="category"]:checked').each(function(index,value){category_ids.push($(value).data('id'));});
    $filters.find('[data-type="course"]:checked').each(function(index,value){course_ids.push($(value).data('id'));});
    $filters.find('[data-type="year"]:checked').each(function(index,value){year_ids.push($(value).data('id'));});
    $filters.find('[data-type="level"]:checked').each(function(index,value){level_ids.push($(value).data('id'));});
    $filters.find('[data-type="topic"]:checked').each(function(index,value){topic_ids.push($(value).data('id'));});
	$filters.find('[data-type="cycle"]:checked').each(function(index,value){cycle.push($(value).data('id'));});

    var fullDate = new Date();
    var twoDigitMonth = fullDate.getMonth() + 1;
	if (twoDigitMonth < 10) twoDigitMonth = "0" + twoDigitMonth;
    var twoDigitDate = fullDate.getDate();
	if (twoDigitDate < 10) twoDigitDate = "0" + twoDigitDate;
    var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;

    var data    = {
        'location_ids' : location_ids,
        'subject_ids'  : subject_ids,
        'category_ids' : category_ids,
        'course_ids'   : course_ids,
        'year_ids'     : year_ids,
        'level_ids'    : level_ids,
        'topic_ids'    : topic_ids,
        'keywords'     : keywords,
		'cycle'     : cycle,
        'given_date'     : currentDate,
		'page'			:page,
        'sortBy': sortBy
    };

    update_packages_results(data, callback);


}

function update_packages_results(data, callback)
{
    $.ajax(
        {
            url     : '/frontend/courses/ajax_filtered_package_course_results',
            data    : data,
            type     : 'post',
            dataType : 'json'
        }).done(function(result)
    {


       if(result.packageResult.length==0&&result.courseResult.length==0)
	   {
		   $('#content_for_packages').html("<h2>No Results Found</h2>");
		   $('#content_for_courses').html("");
	   }else {
		   $('#content_for_packages').html(result.packageResult);
		   $('#content_for_courses').html(result.courseResult);
		   eval(result.jsEvalString);
		   check_if_schedule_event_is_booked();
	   }

		if (typeof callback == 'function') {
			callback();
		}
    });
}

function draw_pagination(total_items, items_per_page,current_page)
{
	var first_num =(current_page-1)*items_per_page+1;
	var second_num =(current_page)*items_per_page;
	if(second_num>total_items)
	{
		second_num=total_items;
	}
$("#number_of_courses>ul>li").html(first_num+"-"+second_num+" of "+total_items + " results found")
	var pages = parseInt(total_items/items_per_page);
	var mod = total_items%items_per_page;

	if(mod!=0){
		pages+=1;
	}

	var pagination=$('#pagination-new').bootpag({
		total: pages,          // total pages
		page: current_page,            // default page
		maxVisible: 5,     // visible pagination
		leaps: false,         // next/prev leaps through maxVisible
		wrapClass:"",
		activeClass:"selected-active-page"
	});

	pagination.unbind("page");


	pagination.on("page", function(event, num){
		update_new_search_results();
	});

    if($("#header_paging_controle .pagination-and-search-results").length==2)
		$("#header_paging_controle .pagination-and-search-results").eq(1).remove();

	$("#header_paging_controle").find('.paginationCopy').remove();
	$("#header_paging_controle").find('.left-section').append($("#pagination-new").clone(true).addClass("paginationCopy"));

	var index=$("#number_of_courses select").eq(0).find("option:selected").index()+1;
	$(".paginationCopy > .pagination-new select").find("option:nth-child("+index+")").prop('selected', true);
}

function update_courses_results(data)
{
	$('.search-calendar-slider').css('opacity', .5);

    $.ajax(
	{
		url      : '/frontend/courses/ajax_filtered_course_results',
		data     : data,
		type     : 'post',
		dataType : 'json'
	}).done(function(result)
		{
			var $content = $('#content_for_courses');
			var $result  = $('<div></div>').html(result);

			if ( ! $content.find('.search-calendar-wrapper') && ! $result.find('.search-calendar-wrapper'))
			{
				// If there is no calendar, add the entire HTML
				$content.html(result);
			}
			else
			{
				// If there is a calendar, just update the calendar portion
				var $slider       = $content.find('.search-calendar-slider');
				var $old_calendar = $slider.find('.search-calendar');
				var $new_calendar = $result.find('.search-calendar').addClass('search-calendar--'+data.direction);

				// Put the new calendar after the old one
				$slider.append($new_calendar);
				$old_calendar.remove();
				$('.search-calendar-slider').css('opacity', '');
			}

			check_if_schedule_event_is_booked();
		});
}

function check_if_schedule_event_is_booked()
{
        var arr_of_event_ids = [];
        var arr_of_schedule_ids = [];

        var cart_inputs = $('.purchase-packages').find('input.cart_hidden_inputs');
        if(cart_inputs.length>0){
            cart_inputs.each(function () {
                if ($(this).data('event-id')!='') {
                    arr_of_event_ids.push($(this).data('event-id'));
                }else{
                    arr_of_schedule_ids.push($(this).data('schedule-id'));
				}
            });
        }
        if(arr_of_event_ids.length>0){
            $('.custom-calendar').find('.booking-date-button').each(function () {
                var $this = $(this);

                if($this.data('event-id')!= undefined && $.inArray( $this.data('event-id'), arr_of_event_ids ) != -1){
                    $this.addClass('already_booked');
                }
            });
        }
		if(arr_of_schedule_ids.length>0){
			$('.custom-calendar').find('.booking-date-button').not('.not-allowed').each(function () {
				var $this = $(this);

				if($.inArray( $this.data('schedule-id'), arr_of_schedule_ids ) != -1){
					$this.addClass('already_booked');
				}
			});
		}

}


/*------------------------------------*\
 #Booking process
\*------------------------------------*/
function change_book_all_classes_to_remove_booking(event_id,$date_and_package) {
	var $this = $date_and_package.find("ul[data-event-id='" + event_id + "']");
	$this.addClass('booked');
	$this.find('li').last().html('<a class="remove-booking" data-event-id="'+event_id+'" href="#">Remove Booking</a>');
}

function change_book_all_classes_to_remove_booking_with_schedule_id(schedule_id,$date_and_package) {
    var $this = $date_and_package.find("ul[data-schedule-id='" + schedule_id + "']");
    $this.addClass('booked');
    $this.find('li').last().html('<a class="remove-booking" data-event-id="'+-1+'" href="#">Remove Booking</a>');
}

function show_course_offers_wrap(course_element, callback) {

	if ($(course_element).hasClass('active')) {
		$('.package-offers-wrap').slideUp();
		$(".date-and-package .booking-date-button").removeClass('active');
	}

	else {

		var $this = $(course_element);
		$(".date-and-package .booking-date-button").removeClass('active');
		$this.addClass('active');
		var data = {
			'course_id': $this.data('course-id'),
			'schedule_id': $this.data('schedule-id'),
			'date': $this.data('date')
		};
		$.ajax({
			url: '/frontend/courses/ajax_show_course_offers_wrap',
			data: data,
			context: course_element,
			type: 'post',
			dataType: 'json'
		}).done(function (result) {
            var $package_data = $this.closest('.date-and-package').find('.package-offers-wrap').hide();

            $package_data.html(result);
            $this.parents('tr').after($package_data.parents('tr'));
            $package_data.slideDown();

            var event_ids = get_event_ids_in_cart();
            var schedule_ids = get_schedule_ids_in_cart();

            if (event_ids.length > 0) {
                for (var event_id in event_ids) {
                    change_book_all_classes_to_remove_booking(event_ids[event_id], $package_data);
                }
            }
            if (schedule_ids.length > 0) {
                for (var schedule_id in schedule_ids) {
                    change_book_all_classes_to_remove_booking_with_schedule_id(schedule_ids[schedule_id], $package_data);
                }
            }

            if (callback) {
                callback();
            }
        });
	}
}

document.addEventListener("DOMContentLoaded", function() {
	var $cart_form = $('#cart-submit-form');


	if ($cart_form.length) {
		var add_to_cart_contact_id  = $cart_form.data('contact_id');
		var add_to_cart_timeslot_id = $cart_form.data('timeslot_id');
		var add_to_cart_schedule_id = $cart_form.data('schedule_id');
		var td;

		$("#student_id").val(add_to_cart_contact_id);
		update_new_search_results(function(){
			if (add_to_cart_schedule_id) {
				if (add_to_cart_timeslot_id) {
					td = $('.booking-date-button[data-schedule-id="'+add_to_cart_schedule_id+'"][data-event-id="'+add_to_cart_timeslot_id+'"]')[0];
				}
				else {
					td = $('.booking-date-button[data-schedule-id="'+add_to_cart_schedule_id+'"]')[0];
				}

				show_course_offers_wrap(td, function(){
					$(td).closest('.date-and-package').find('.package-offers-wrap .button.cart').click();
				});
			}
		});

		if($('#booking-cart-empty').is(':visible')){
			$("#continue-button").css({ opacity: 0.5 });
		}
	}
});

$(document).ready(function(){
	$('#cart-submit-form').submit(function(e){
		if($('#booking-cart-empty').is(':visible'))
			e.preventDefault();
		$("#continue-button").css({ opacity: 0.5 });
	})
});

function checkIfCartEmpty() {
	if ($('#booking-cart-empty').is(':visible')) {
		$("#continue-button").hover(function () {
			$(this).css('cursor','pointer').attr('title', 'Please select a course in order to continue.');
			$("#continue-button").css({ opacity: 0.5 });
		}, function () {
			$(this).css('cursor','auto');
		})
	}
	else if ($('#booking-cart-empty').is(':hidden')){

		$('.continue-text').css('display', 'none');
		$("#continue-button").css({ opacity: 1.0 });
	}
}

/* for globally actions menus */
function addMessage(message, type)
{
    type = type || success;
	try {
		var message_area = $('#msg_area');

		message_area.empty();
		message_area.add_alert(message, type+' popup_box');
		message_area.fadeTo(2000, 500);

		message_area.fadeTo(2000, 500).slideUp(500, function () {
			message_area.slideUp(500);
		});
	} catch (exc) {
		console.log(exc);
	}
}

// Add an alert to a message area.
// e.g. $('#selector').add_alert('Save successful', 'success');
(function($)
{
    $.fn.add_alert = function(message, type)
    {
        var $alert = $(
            '<div class="alert'+((type) ? ' alert-'+type : '')+'">' +
            '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
            '</div>');
        $(this).append($alert);
    };
})(jQuery);

$(document).ready(function(){

	// swipe left
	$(document).on('click','.arrow-left',function () {
		var $this = $(this);
		var package_id = $this.closest('.select-package').attr('id');

		var first_date = $this.closest('.custom-calendar').find('.search-calendar-course_row td[data-date]:first').data('date');

		var fullDate = new Date();
		var twoDigitMonth = fullDate.getMonth() >= 9 ? (fullDate.getMonth()+1) : '0' + fullDate.getMonth() + 1;
		var twoDigitDate = fullDate.getDate() + ""; if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
		var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;

		if(first_date != currentDate){
			fullDate = new Date(first_date);
			fullDate.setDate(fullDate.getDate() - 7);
			twoDigitMonth = ((fullDate.getMonth()) >= 9)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
			twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
			currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;

			if($this.hasClass('search_courses_left')){

				var $accordion = $('#accordion');
				// var $sidebar     = $('#sidebar');
				// var sort         = $('[name="course_list_sort"]:checked').val();
				// var display      = $('[name="course_list_display"]:checked').val();
				var keywords     = $('#search_keyword').val();
				if($.trim(keywords) == ''){
					keywords = null;
				}

				// var page         = $('#search_results-pagination').find('.current').attr('data-page');
				var location_ids = [];
				var subject_ids  = [];
				var category_ids = [];
				var course_ids   = [];
				var year_ids     = [];
				var level_ids    = [];
				var topic_ids    = [];

				// page = page ? page : 1;

				$accordion.find('[data-type="location"]:checked').each(function(index,value){location_ids.push($(value).data('id'));});
				$accordion.find('[data-type="subject"]:checked').each(function(index,value){subject_ids.push($(value).data('id'));});
				$accordion.find('[data-type="category"]:checked').each(function(index,value){category_ids.push($(value).data('id'));});
				var coursesTr=$("#content_for_courses .custom-calendar tbody tr").not(":nth-child(1)");
				coursesTr.each(function (ind,val) {
					// this causes some unwanted loss of classes while clicking next/prev week icons
					//course_ids.push( $(this).find("td:nth-child(1)").data("course-id"));
				});
				$accordion.find('[data-type="year"]:checked').each(function(index,value){year_ids.push($(value).data('id'));});
				$accordion.find('[data-type="level"]:checked').each(function(index,value){level_ids.push($(value).data('id'));});
				$accordion.find('[data-type="topic"]:checked').each(function(index,value){topic_ids.push($(value).data('id'));});

				var data = {
					'location_ids' : location_ids,
					'subject_ids'  : subject_ids,
					'category_ids' : category_ids,
					'course_ids'   : course_ids,
					'year_ids'     : year_ids,
					'level_ids'    : level_ids,
					'topic_ids'    : topic_ids,
					'keywords'     : keywords,
					'given_date'   : currentDate,
					'direction'    : 'prev'
				};
				update_courses_results(data);
				check_if_schedule_event_is_booked();
			}else if($this.hasClass('for-time-slots')){
				var schedule_id = $this.closest('.swiper-slide').find('.custom-calendar  tr:nth-child(2) td:nth-child(7)').data('schedule-id');
				var data    = {
					'schedule_id'     : schedule_id,
					'given_date'     : currentDate
				};
				$.ajax(
					{
						url     : '/frontend/courses/ajax_get_time_slots_results',
						data    : data,
						type     : 'post',
						dataType : 'json'
					}).done(function(result)
                        {
                            $this.closest('.alternative-dates-wrap').html(result);
                            check_if_schedule_event_is_booked();
                        });

			}else{
				data    = {
					'id'     : package_id,
					'given_date'     : currentDate
				};
				$.ajax(
					{
						url     : '/frontend/courses/ajax_get_package_course_results',
						data    : data,
						type     : 'post',
						dataType : 'json'
					}).done(function(result)
                        {
                            $this.closest('.date-and-package').html(result);
                            check_if_schedule_event_is_booked();
                        });
			}

		}

	});

	// swipe right
	$(document).on('click','.arrow-right',function () {
		var $this = $(this);
		var package_id = $this.closest('.select-package').attr('id');

		var last_date = $this.closest('.custom-calendar').find('.search-calendar-course_row td:last-child').data('date');
		var fullDate = new Date(last_date);
		fullDate.setDate(fullDate.getDate() + 1);
		//Thu May 19 2011 17:25:38 GMT+1000 {}
		//convert month to 2 digits
		var twoDigitMonth = ((fullDate.getMonth()) >= 9)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
		var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
		var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;
		//      format  2017-04-27

		if($this.hasClass('search_courses_right')) {
			var $accordion = $('#accordion');
			var keywords = $('#search_keyword').val();
			if ($.trim(keywords) == '') {
				keywords = null;
			}
			var location_ids = [];
			var subject_ids = [];
			var category_ids = [];
			var course_ids = [];
			var year_ids = [];
			var level_ids = [];
			var topic_ids = [];

			$accordion.find('[data-type="location"]:checked').each(function (index, value) {
				location_ids.push($(value).data('id'));
			});
			$accordion.find('[data-type="subject"]:checked').each(function (index, value) {
				subject_ids.push($(value).data('id'));
			});
			$accordion.find('[data-type="category"]:checked').each(function (index, value) {
				category_ids.push($(value).data('id'));
			});

			var coursesTr=$("#content_for_courses .custom-calendar tbody tr").not(":nth-child(1)");
			coursesTr.each(function (ind,val) {
				//course_ids.push( $(this).find("td:nth-child(1)").data("course-id"));
			});


			$accordion.find('[data-type="year"]:checked').each(function (index, value) {
				year_ids.push($(value).data('id'));
			});
			$accordion.find('[data-type="level"]:checked').each(function (index, value) {
				level_ids.push($(value).data('id'));
			});
			$accordion.find('[data-type="topic"]:checked').each(function (index, value) {
				topic_ids.push($(value).data('id'));
			});

			var data = {
				'location_ids' : location_ids,
				'subject_ids'  : subject_ids,
				'category_ids' : category_ids,
				'course_ids'   : course_ids,
				'year_ids'     : year_ids,
				'level_ids'    : level_ids,
				'topic_ids'    : topic_ids,
				'keywords'     : keywords,
				'given_date'   : currentDate,
				'direction'    : 'next'
			};
			update_courses_results(data);
			check_if_schedule_event_is_booked();

		}else if($this.hasClass('for-time-slots')){
			var schedule_id = $this.closest('.swiper-slide').find('.custom-calendar  tr:nth-child(2) td:nth-child(7)').data('schedule-id');
			var data    = {
				'schedule_id'     : schedule_id,
				'given_date'     : currentDate
			};
			$.ajax(
			{
				url     : '/frontend/courses/ajax_get_time_slots_results',
				data    : data,
				type     : 'post',
				dataType : 'json'
			}).done(function(result)
				{
					$this.closest('.alternative-dates-wrap').html(result);
					check_if_schedule_event_is_booked();
				});

		}else{
			var data    = {
				'id'     : package_id,
				'given_date'     : currentDate
			};
			$.ajax(
			{
				url     : '/frontend/courses/ajax_get_package_course_results',
				data    : data,
				type     : 'post',
				dataType : 'json'
			}).done(function(result)
				{
					$this.closest('.date-and-package').html(result);
					check_if_schedule_event_is_booked();
				});
		}
	});

	$(document).on('click','.button-toggle',function() {
		var $this = $(this);
		$this.toggleClass("active");
		$this.parents('.package-wrap').toggleClass('open');
		var id = $this.data('id');

		var fullDate = new Date();
		var twoDigitMonth = ((fullDate.getMonth()) >= 9)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
		var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
		var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;

		var data    = {
			'id'     : $this.data('id'),
			'given_date'     : currentDate
		};

		$.ajax(
		{
			url     : '/frontend/courses/ajax_get_package_course_results',
			data    : data,
			type     : 'post',
			dataType : 'json'
		}).done(function(result)
			{
				$('#'+id).html(result);
				check_if_schedule_event_is_booked();
			});

	});

	/*popup open on click of courses left side */
	$(document).on('click', '.search-calendar-course-image', function()
	{
		var $origin = $(this); // The clicked element

		// Get data for the clicked course
		var course_id   = this.dataset.course_id;
		var schedule_id = this.dataset.schedule_id;

		$.ajax({
			url: '/frontend/courses/ajax_get_schedule/',
			data: {schedule_id: schedule_id, course_id: course_id}
		}).done(
			function(result)
			{
				var schedule = result.schedule;
				var course   = result.course;
				var topics   = result.topics;

				if (course && course.id)
				{
					// Fill elements in the modal, with data from the course
					var $modal = $('#booking_popup');
					$modal.find('.popup-title').text(course.title);
					$('#booking_popup-schedule_id').html(schedule_id > 0 ? schedule_id : course_id);
					$('#booking_popup-description').html(course.summary);
					$('#booking_popup-image').attr('src', $origin.find('img').attr('src'));

					var $topics = $('#booking_popup-topics');
					var topics_html = '<ul>';
					var i;
					for (i = 0; i < topics.length; i++)
					{
						topics_html += '<li>'+topics[i].name+'</li>';
						topics_html += ((i + 1) % 9 == 0) ? '</ul><ul>' : '';
					}
					topics_html += '</ul>';
					topics_html.replace('<ul></ul>', '');

					$('#booking_popup-topics-list').html(topics_html);
					course.topics.length ? $topics.show() : $topics.hide();

					// Show the modal, after the details have been filled
					$modal.show();
				}
			});
	});

	$('#booking_popup').on('click', '.basic_close', function(){
		$('#booking_popup').css('display','none');
	});

	$(document).on('click','.alternativen-dates-btn',function() {
		$(this).toggleClass("active");
		$(this).siblings(".alternative-dates-wrap").slideToggle("1000");
		check_if_schedule_event_is_booked();
	});

	// Open package-offers-wrap
	$(document).on('click',".date-and-package tr:not(:first-child) .booking-date-button", function () {
		show_course_offers_wrap(this);
	});


	$(document).on('click',".package-offers-wrap .close-package", function () {
		$(this).parents('.package-offers-wrap').slideUp();
		$(this).parents('.date-and-package').find(".booking-date-button").removeClass('active');
	});

	$(document).on('click', ".alternative-dates-wrap .custom-calendar td.alt-date-book",function ()
	{
		$(".alternative-dates-wrap .custom-calendar td.alt-date-book").removeClass('active');
		var $this = $(this);
		$this.addClass('active');

		var d = $this.data("date");
		var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

		var fullDate = new Date(d);
		var month =  monthNames[fullDate.getMonth()];
		var twoDigitDate = fullDate.getDate()+"";
		if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;

		var full_day_name = weekday[fullDate.getDay()];
		var date_formatted = full_day_name + ' ' + twoDigitDate + ' ' + month;
		var is_student = $('#cart-submit-form').data('is_student');

		$('#alternative-date-book').remove();
		$this.closest('.swiper-slide')
			.append('<div id="alternative-date-book" class="subdropdwon-alternative-date">' +
						'<div class="classes-details-wrap">' +
						'<ul class="details-wrap" ' +
								'data-booking-type="'+$this.data("booking-type")+
								'data-course-title="'+$this.data("course-title")+'" ' +
								'data-date="'+d+'" ' +
								'data-date-formatted="'+date_formatted+'" ' +
								'data-event-id="'+$this.data("event-id")+'" ' +
								'data-fee-per="'+$this.data("fee-per")+'" ' +
								'data-logged-in-user-permission="'+$this.data("logged-in-user-permission")+'" ' +
								'data-schedule-id="'+$this.data("schedule-id")+'" ' +
							'"><li>' +
						'<span class="month">'+month+'</span><span class="date">'+twoDigitDate+'th</span></li>	' +
						'<li><span class="time">'+$this.data("start-time")+'</span><span class="location">'+$this.data("location")+'</span></li>' +
						'<li><span class="mints">'+subtract_times($this.data("start-time"),$this.data("end-time"))+' mins</span>	' +
						'<span class="icon"><span class="fa fa-book" aria-hidden="true"></span></span>	' +
						'<span class="room-no">'+$this.data("room")+'</span>' +
						'</li><li><span class="time">'+$this.data("end-time")+'</span>' +
						'<span class="name">'+$this.data("trainer")+'</span></li><li><div class="price-wrap">' +
						'<span class="price">€'+$this.data("fee")+'</span>' +
						// Todo add prev price and seats
//                '<span class="prv-price">€ 250</span>' +
						'<span class="payfor">'+$this.data("pay-type")+'</span>' +
//                '<span class="left-place">Only 10 places left</span></div></li>' +
						(is_student
							? '<button type="button" class="button button--book wishlist add">Add to wishlist</button><a class="wishlist remove hidden">Remove from wishlist</a>'
							: '<li><button type="button" class="button button--book">'+$this.data("logged-in-user-permission")+'</button></li>'
						)+
						'</ul></div></div>');

		if($this.hasClass('already_booked')){
			var details_wrap = $('#alternative-date-book').find('ul')[0];
			$(details_wrap).addClass('booked')
				.find('li .cart').replaceWith('<a class="remove-booking" data-event-id="'+$this.data("event-id")+'" href="#">Remove Booking</a>');
		}
	});

	// CART
	check_cart();


	$(document).on('click', '.details-wrap .wishlist.add', function(){
		var button = this;
		var $details = $(button).closest('.details-wrap');
		var schedule_id = $details.data('schedule-id');
		var timeslot_id = $details.data('booking-type') =='One Timeslot' ? $details.data('event-id') : null;

		$.post(
			'/admin/contacts3/wishlist_add',
			{
				schedule_id: schedule_id,
				timeslot_id: timeslot_id
			},
			function (response) {
				$(button).closest('.details-wrap').find('.wishlist.add').addClass("hidden");
				$(button).closest('.details-wrap').find('.wishlist.remove').removeClass("hidden");
			}
		);
	});

	$(document).on('click', '.details-wrap .wishlist.remove', function(){
		var button = this;
		var schedule_id = $(button).closest('.details-wrap').data('schedule-id');

		$.post(
			'/admin/contacts3/wishlist_remove',
			{
				schedule_id: schedule_id
			},
			function (response) {
				$(button).closest('.details-wrap').find('.wishlist.add').removeClass("hidden");
				$(button).closest('.details-wrap').find('.wishlist.remove').addClass("hidden");
			}
		);
	});

	// add booking to cart  BOOK ALL BUTTON
	$(document).on('click', ".details-wrap button.cart", function ()
	{
		var button          = this;
		var details_wrap    = $(this).closest('.details-wrap');
		var course_year_id  = details_wrap.data('year-id');
		var $student        = $('#student_id').find(':selected');
		var student_year_id = $student.data('year-id');
		var event_id        = details_wrap.data('event-id');
		var year_mismatch   = (student_year_id && course_year_id && student_year_id != course_year_id);

		// These variables say if the user has chosen to "continue anyway", despite certain warnings
		var ignore_year_mismatch = (details_wrap.attr('data-ignore_year_mismatch') == 1);
		var ignore_time_conflict = (details_wrap.attr('data-ignore_time_conflict') == 1);

		// If the years don't match, show the modal and prematurely end the function
		if ( ! ignore_year_mismatch && year_mismatch)
		{
			$('#booking-year_mismatch-item_year').text(details_wrap.data('year'));
			$('#booking-year_mismatch-student_name').html($student.html());
			$('#booking-year_mismatch-student_year').text($student.data('year'));
			$('#booking-year_mismatch-continue').data('event-id', event_id).attr('data-event-id', event_id);
			$('#booking-year_mismatch').show();
			return false;
		}
		else
		{
			details_wrap.data('ignore_year_mismatch', 0).attr('data-ignore_year_mismatch', 0);
		}

		if ( ! ignore_time_conflict)
		{
			// Check if the time of the event collides with the time of another item in the cart
			$.ajax({
				url    :'/frontend/bookings/ajax_check_for_booking_overlap',
				method : 'POST',
				data   : {'event_id': event_id, contact_id: $student.val()}
			})
				.done(function(result)
				{
					if ( ! result.success)
					{
						$('#bookings-conflict-continue').data('event-id', event_id).attr('data-event-id', event_id);
						$('#bookings-conflict-message').html(result.message);
						$('#bookings-conflict-modal').show();
					}
					else
					{
						// Reset the "continue anyway" preference and add the item to the cart
						details_wrap.data('ignore_time_conflict', 0).attr('data-ignore_time_conflict', 0);
						add_to_cart(button);
					}
				});
		}
		else
		{
			// User has said that they are okay with the time conflict, so add the item to the cart.
			add_to_cart(button);
		}
	});

	function add_to_cart(button)
	{
		var details_wrap      = $(button).closest('.details-wrap');
		var course_title      = details_wrap.data('course-title');
		var booking_type      = details_wrap.data('booking-type');
		var schedule_id       = details_wrap.data('schedule-id');
		var user_permission   = details_wrap.data('logged-in-user-permission');
		var date_formatted    = details_wrap.data('date-formatted');
		var fee_per           = details_wrap.data('fee-per');
		var when_to_pay       = $.trim(details_wrap.find('span.payfor').text());
		var amendable         = details_wrap.data('amendable');
		var schedule_event_id = details_wrap.data('event-id');

		// take just amount
		var fee = $.trim(details_wrap.find('span.price').text());
		var fee_res = fee.split(" ");
		fee = fee_res[1];

		// whole schedule
		if (booking_type == "Whole Schedule")
		{
			var data    = {
				'schedule_id' : schedule_id
			};
			$.ajax({
				url  : '/frontend/courses/ajax_get_whole_schedule_events_count',
				data : data,
				type : 'post'
			}).done(function (response)
				{
					var now       = new Date();
					var events    = [];
					var event_ids = [];
					for (var i = 0 ; i < response.events.length ; ++i)
					{
						var event_date = new Date(response.events[i].datetime_start);
						if (event_date > now)
						{
							events.push(response.events[i]);
							event_ids.push(response.events[i].id);
						}
					}

					var is_payg = (when_to_pay.toLowerCase() == 'pay as you go' || when_to_pay.toLowerCase() == 'payg');
					var calculated_fee;
					if (is_payg) {
						calculated_fee = 0;
					}
					else if (fee_per == "Timeslot"){
						calculated_fee = event_ids.length * parseFloat(fee);
					}
					else {
						calculated_fee = fee;
					}

					var date_formatted_range = (events.length > 0) ? events[0].date_formatted : '';
					if (events.length > 1) {
						var last_event = events[events.length - 1];
						date_formatted_range += ' &ndash; ' + last_event.date_formatted + ' ' + last_event.datetime_start.split('-')[0];
					}

					add_item_to_cart({
						when_to_pay:     when_to_pay,
						course_title:    course_title,
						date_formatted:  date_formatted_range,
						fee:             calculated_fee,
						per_class_fee:   fee,
						event_ids:       event_ids,
						schedule_id:     schedule_id,
						booking_type:    booking_type,
						user_permission: user_permission,
						count:           event_ids.length,
						fee_per:         fee_per,
						amendable:       amendable,
						inputs_only:     true
					});

					$('.details-wrap[data-schedule-id='+schedule_id+']').addClass('booked').each(function () {
						$(this).find('li .cart').replaceWith('<a class="remove-booking" data-schedule-id="'+schedule_id+'" href="#">Remove Booking</a>');
					});
					$('.custom-calendar').find("[data-schedule-id~='" + schedule_id + "']").not('.not-allowed').addClass('already_booked');
					check_cart();
				});

		}else{
			add_item_to_cart({
				when_to_pay:     when_to_pay,
				course_title:    course_title,
				date_formatted:  date_formatted,
				fee:             fee,
				per_class_fee:   fee,
				event_ids:       schedule_event_id,
				schedule_id:     schedule_id,
				booking_type:    booking_type,
				user_permission: user_permission,
				count:           null,
				fee_per:         fee_per,
				amendable:       amendable,
				inputs_only:     true
			});

			$('.details-wrap[data-event-id='+schedule_event_id+']').addClass('booked').each(function () {
				$(this).find('li .cart').replaceWith('<a class="remove-booking" data-event-id="'+schedule_event_id+'" href="#">Remove Booking</a>');
			});

			check_if_schedule_event_is_booked();
			check_cart();
		}
	}

	// If the user chooses to continue with a booking, despite its time conflicting with another booking
	$(document).on('click', '#bookings-conflict-continue', function()
	{
		var event_id = $(this).attr('data-event-id');
		var $event   = $('.details-wrap[data-event-id="'+event_id+'"]');

		// Flag that the mismatch warning is to be ignored and click the button again.
		$event.data('ignore_time_conflict', 1).attr('data-ignore_time_conflict', 1);
		$event.find('.cart').trigger('click');

	});

	// If the user chooses to continue with a booking despite it not matching the student's year
	$(document).on('click', '#booking-year_mismatch-continue', function()
	{
		var event_id = $(this).attr('data-event-id');
		var $event   = $('.details-wrap[data-event-id="'+event_id+'"]');

		// Flag that the mismatch warning is to be ignored and click the button again.
		$event.data('ignore_year_mismatch', 1).attr('data-ignore_year_mismatch', 1);
		$event.find('.cart').trigger('click');
	});

	// click on remove-booking button on booking popup
	$(document).on('click', ".remove-booking", function(e)
	{
		e.preventDefault();
		var details_wrap = $(this).closest('.details-wrap');
		var schedule_id = details_wrap.data('schedule-id');
		var fee_txt = details_wrap.find('.price').text();
		var fee = fee_txt.split(" ")[1];
		if(details_wrap.data('booking-type') == "Whole Schedule"){

			$('.details-wrap').each(function () {
				var $this = $(this);
				if ($this.data('schedule-id')==schedule_id) {
					$this.removeClass('booked')
						.find('li .remove-booking').replaceWith('<button type="button" class="button button--book cart">'+$this.data("logged-in-user-permission")+'</button>');
				}
			});
			remove_item_from_cart_with_schedule(schedule_id);
		}else{
			var event_id = $(this).data('event-id');
			$('.details-wrap').each(function () {
				var $this = $(this);
				if ($this.data('event-id')==event_id) {
					$this.removeClass('booked')
						.find('li .remove-booking').replaceWith('<button type="button" class="button button--book cart">'+$this.data("logged-in-user-permission")+'</button>');
				}
			});
			subtract_if_there_is_class_in_cart_with_same_schedule( schedule_id, event_id, fee);
		}
	});

	// click on remove_from_cart on cart
	$('#checkout-sidebar-items').on('click', '.checkout-item-remove', function(e)
	{
		e.preventDefault();

		var $li       = $(this).parents('.checkout-item');
		var event_ids = $li.data('event-id');
		event_ids     = Array.isArray(event_ids) ? event_ids : JSON.parse(event_ids);

		if (!Array.isArray(event_ids)) {
			event_ids = [event_ids];
		}
		// Remove from the sidebar
		remove_item_from_cart(event_ids);

		// Un-highlight items in the search calendar
		var $event_details;
		var event_id;
		$('.details-wrap').each(function()
		{
			$event_details = $(this);
			event_id       = $event_details.data('event-id');

			if (event_ids.indexOf(event_id) > -1)
			{
				$event_details.removeClass('booked')
					.find('li .remove-booking').replaceWith('<button type="button" class="button button--book cart">'+$li.data("logged-in-user-permission")+'</button>');
			}
		});
	});
});

function add_item_to_cart(args)
{
	var when_to_pay      = (typeof args.when_to_pay       != 'undefined') ? args.when_to_pay       : '';
	var course_title     = (typeof args.course_title      != 'undefined') ? args.course_title      : '';
	var date_formatted   = (typeof args.date_formatted    != 'undefined') ? args.date_formatted    : '';
	var fee              = (typeof args.fee               != 'undefined') ? args.fee               : '';
	var per_class_fee    = (typeof args.per_class_fee     != 'undefined') ? args.per_class_fee     : '';
	var per_day_fee      = (typeof args.per_day_fee       != 'undefined') ? args.per_day_fee       : '';
	var event_ids        = (typeof args.event_ids         != 'undefined') ? args.event_ids         : '';
	var schedule_id      = (typeof args.schedule_id       != 'undefined') ? args.schedule_id       : '';
	var booking_type     = (typeof args.booking_type      != 'undefined') ? args.booking_type      : '';
	var user_permission  = (typeof args.user_permission   != 'undefined') ? args.user_permission   : '';
	var fee_per          = (typeof args.fee_per           != 'undefined') ? args.fee_per           : '';
	var amendable        = (typeof args.amendable         != 'undefined') ? args.amendable         : '';
	var count            = (typeof args['count']          != 'undefined' && $.isNumeric(args['count'])) ? args['count'] : 1;

	var count_text          = (count == 1) ? '1 class' : count+ ' classes';
	var purchase_packages   = $('.purchase-packages');
	var $existing_cart_item = [];

	event_ids = (Array.isArray(event_ids)) ? event_ids : [event_ids];
	// If there is an item in the cart menu, from the same schedule, on the same day,
	// that item will be updated, rather than an another item added
	if (event_ids.length) {
		$existing_cart_item = $('.checkout-item[data-schedule-id="'+schedule_id+'"][data-event-id="'+(event_ids.length > 1 ? '[' + event_ids + ']' : event_ids[0])+'"]');
	}

	for (var i = 0; i < event_ids.length; i++)
	{
		var event_id = event_ids[i];
		purchase_packages.append(
			'<div class="booking-item-inputs">' +
				'  <input ' +
				'  class="cart_hidden_inputs" ' +
				'  type="hidden" ' +
				'  data-schedule-id="'+schedule_id+'" ' +
				'  data-booking-type="'+booking_type+'" ' +
				'  data-event-id="'+event_id+'" ' +
				'  data-when-to-pay="'+when_to_pay+'" ' +
				'  data-fee="'+fee+'"' +
				'  data-count="'+count+'"' +
				'  data-date="'+htmlentities(date_formatted)+'"' +
				'  />' +

				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][schedule_id]"     value="' + schedule_id     + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][course_title]"    value="' + course_title    + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][date_formatted]"  value="' + date_formatted  + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][booking_type]"    value="' + booking_type    + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][when_to_pay]"     value="' + when_to_pay     + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][fee]"             value="' + fee             + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][count]"           value="' + count           + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][user_permission]" value="' + user_permission + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][amendable]"       value="' + amendable       + '" />' +
				'</div>'
		);
	}

	if (!args.inputs_only) {
		var is_payg = (when_to_pay.toLowerCase() == 'pay as you go' || when_to_pay.toLowerCase() == 'payg');

		if ($existing_cart_item.length) {
			// Update existing cart menu item
			var new_count = count;
			var new_fee = (fee_per == 'Timeslot') ? (fee * new_count) : fee;
			var new_fee_text = (is_payg && per_class_fee != '')
				? parseFloat(per_class_fee).toFixed(2) + ' <small>per&nbsp;class</small>'
				: new_fee.toFixed(2);
			if (per_day_fee) {
				new_fee_text = parseFloat(per_day_fee).toFixed(2) + ' daily'
			}
			count_text = (new_count == 1) ? '1 class' : new_count + ' classes';

			var existing_event_ids = $existing_cart_item.data('event-id');
			existing_event_ids = (Array.isArray(existing_event_ids)) ? existing_event_ids : [existing_event_ids];

			var new_event_ids = existing_event_ids.concat(event_ids);

			$existing_cart_item.data({'event-id': new_event_ids}).attr('data-event-id', JSON.stringify(new_event_ids));
			$existing_cart_item.data('count', new_count).attr('data-count', new_count);
			$existing_cart_item.find('.checkout-item-count').text(count_text);
			$existing_cart_item.find('.checkout-item-fee').text(new_fee_text);
		}
		else {
			// Add a new cart menu item
			var $clone = $('#checkout-item-template').find('li').clone();
			var event_data = (event_ids.length == 1) ? event_ids[0] : JSON.stringify(event_ids);

			$clone
				.data({
					'schedule-id': schedule_id,
					'booking-type': booking_type,
					'event-id': event_data,
					'count': count,
					'date': date_formatted,
					'logged-in-user-permission': user_permission
				})
				.attr({
					'data-schedule-id': schedule_id,
					'data-booking-type': booking_type,
					'data-event-id': event_data,
					'data-count': count,
					'data-date': date_formatted,
					'data-logged-in-user-permission': user_permission
				});

			var fee_text = (is_payg && per_class_fee != '')
				? parseFloat(per_class_fee).toFixed(2) + ' <small>per&nbsp;class</small>'
				: parseFloat(fee).toFixed(2);
			if (per_day_fee) {
				fee_text = parseFloat(per_day_fee).toFixed(2) + ' daily'
			}

			$clone.find('.checkout-item-title').text(course_title);
			$clone.find('.checkout-item-date').html(date_formatted);
			$clone.find('.checkout-item-count').html(count + ((count == 1) ? ' session' : ' sessions'));
			$clone.find('.checkout-item-fee').text(fee_text);
			$clone.find('.checkout-item-input').remove();

			if (when_to_pay == 'Pre-Pay') {
				$('#prepay_container-mobile').find('ul').append($clone.clone());
                $('#prepay_container').find('ul').append($clone);
				$('.prepay_container').removeClass("hidden");
			} else if (is_payg) {
				$('#pay_as_you_go_container-mobile').find('ul').append($clone.clone());
                $('#pay_as_you_go_container').find('ul').append($clone);
				$('.pay_as_you_go_container').removeClass("hidden");
			}

            if (!args.no_messsage) {
                var $icon = $('#cart-alert-icon-template');
                $icon.find('.cart-icon-amount').attr('data-amount', '+1');

                addMessage(course_title+' added to cart. '+$icon.html(), 'add');
            }
		}
	}

    refresh_cart_messages();

	$("#booking-cart-empty").addClass("hidden");
}

function refresh_cart_messages()
{
    var schedule_ids = [];
    $('.checkout-item').each(function() {
        schedule_ids.push(this.getAttribute('data-schedule-id'));
    });

    $.ajax({
        url  : '/frontend/courses/ajax_get_cart_messages',
        data : {schedule_ids: schedule_ids}
    }).done(function(messages) {
            messages = JSON.parse(messages);
            var $notices = $('#booking-cart-notices');
            if (messages.length) {
                var html = '';
                for (var i = 0; i < messages.length; i++) {
                    html += '<div class="booking-cart-notice">'+messages[i]+'</div>'
                }
                $notices.html(html).removeClass('hidden');
            }
            else {
                $notices.addClass('hidden');
            }
        });
}

function add_if_there_is_class_in_cart_with_same_schedule(purchase_packages, schedule_id, fee, fee_per) {
	var element = purchase_packages.find("input[data-schedule-id='" + schedule_id +"']");

	if (element.length > 0) {
		var quantity = element.find('span.classes_quantity').text();
		var amount = element.find('span.cart_amount').text();
		var new_quantity = parseInt(quantity) + 1;

		if (fee_per == 'Timeslot') {
			var new_amount = parseFloat(amount) + parseFloat(fee);
			element.find('span.cart_amount').text(new_amount);
		}
		element.find('span.classes_quantity').text(new_quantity);

		return true;
	}else{
		return false;
	}
}

function remove_item_from_cart(schedule_event_id)
{
	var $items = $('#checkout-sidebar-items');

    var $item_to_remove = $items.find("[data-event-id='" + schedule_event_id + "'], [data-event-id='" + JSON.stringify(schedule_event_id) + "']");
    var schedule_id = $item_to_remove.data('schedule-id');
    var course_title = $item_to_remove.find('.checkout-item-title').text();
    var $icon = $('#cart-alert-icon-template');

    $icon.find('.cart-icon-amount').attr('data-amount', '-1');
    addMessage(course_title+' removed from cart '+$icon.html(), 'remove');
    $item_to_remove.remove();

	// If the cart item has a single event ID
    $items.find("[data-event-id='" + schedule_event_id + "']").remove();
	$items.find("[data-event-id='" + JSON.stringify(schedule_event_id) + "']").remove();

	// If the cart item has multiple event IDs, as a JSON array
	$items.find('[data-event-id]').each(function()
	{
		var event_ids = $(this).data('event-id');

		if (Array.isArray(event_ids))
		{
			// Remove the event from the array
			var remove_ids = Array.isArray(schedule_event_id) ? schedule_event_id : [schedule_event_id];
			event_ids = $(event_ids).not(remove_ids).get();

			if (event_ids.length == 0) {
				// If this was the last event in the array, remove the cart item
				$(this).remove();
			}
			else {
				// Store the updated events array in the item
				$(this).data({'event-id': event_ids}).attr('data-event-id', JSON.stringify(event_ids));
			}
		}
	});

	var schedule_with_events=$('.purchase-packages').find("input[data-event-id][data-event-id!='']");

	schedule_with_events.each (function(){
		var ids_remove = schedule_event_id;
		if (!Array.isArray(schedule_event_id)) {
			ids_remove = [schedule_event_id];
		}

		if (ids_remove.indexOf($(this).data("event-id")) != -1) {
			$(this).remove();
		}
	});
	$('.purchase-packages').find("[data-event-id='" + schedule_event_id + "']").parents('.booking-item-inputs').remove();
	$('.custom-calendar').find("[data-event-id='" + schedule_event_id + "']").removeClass('already_booked');

    if (Array.isArray(schedule_event_id)) {
        $('.custom-calendar').find("[data-schedule-id~='" + schedule_id + "']").removeClass('already_booked');
    }

    check_cart(true);
}

function subtract_if_there_is_class_in_cart_with_same_schedule(schedule_id, schedule_event_id, fee) {
	var $purchase_packages = $('#checkout-sidebar-items');
	var $element = $purchase_packages.find("li[data-schedule-id='" + schedule_id +"']");

	if ($element.length > 0)
	{
		var count      = $element.data('count');
		var new_count  = parseInt(count) - 1;
		var count_text = (new_count == 1) ? '1 class' : new_count + ' classes';
		var amount     = $element.find('.checkout-item-fee').text();
		var event_ids  = $element.data('event-id');
		event_ids = Array.isArray(event_ids) ? event_ids : [event_ids];
		var index = event_ids.indexOf(schedule_event_id);
		if (index > -1) event_ids.splice(index, 1);

		$element.data('count', new_count).attr('data-count', new_count);
		$element.data({'event-id': event_ids}).attr('data-event-id', JSON.stringify(event_ids));
		$element.find('.checkout-item-count').text(count_text);

		if ($.isNumeric(amount)) {
			var new_amount = parseFloat(amount) - parseFloat(fee);
			$element.find('.checkout-item-fee').text(new_amount.toFixed(2));
		}

		$('.cart_hidden_inputs[data-event-id="'+schedule_event_id+'"]').remove();


		if (new_count == 0) {
			$element.remove();
			remove_item_from_cart(schedule_event_id);
		}else{
			$('.custom-calendar').find("[data-event-id='" + schedule_event_id + "']").removeClass('already_booked');
			$purchase_packages.find("input[data-event-id='" + schedule_event_id + "']").remove();
		}
	}else{
		remove_item_from_cart(schedule_event_id);
	}
	check_cart(true);
}


function remove_item_from_cart_with_schedule(schedule_id)
{
	$('#checkout-sidebar-items').find("[data-schedule-id='" + schedule_id + "']").remove();
	$('.purchase-packages'     ).find("[data-schedule-id='" + schedule_id + "']").parents('.booking-item-inputs').remove();
	$('.custom-calendar'       ).find("[data-schedule-id~='" + schedule_id + "']").removeClass('already_booked');
	check_cart(true);
}


function subtract_times(start,end) {
	var s = start.split(':');
	var e = end.split(':');
	var min = e[1]-s[1];
	var hour = e[0]-s[0];
	return 60*hour + min;
}

$(document).on("change", "#student_id", function(){
	check_cart(true);
});

var $discountItemTemplate = $(".discountItemPlaceholder.template");
$discountItemTemplate.removeClass(".template");
$discountItemTemplate.remove();

function check_cart(override) {
	var hasValues=false;
	var schedules_only=$('.purchase-packages').find("input[data-event-id='']");
	var schedule_events={};
	schedules_only.each(function () {
		var ent = $(this).attr('data-schedule-id');
		if(!schedule_events[ent])
		{

			schedule_events[ent]={};
		}

		schedule_events[ent]={'isScheduleOnly':true};

		hasValues=true;


	});

	var schedule_with_events=$('.purchase-packages').find("input[data-event-id][data-event-id!='']");


	schedule_with_events.each(function () {
		var ent = $(this).attr('data-schedule-id');
		if(!schedule_events[ent])
		{

			schedule_events[ent]={};
		}

		schedule_events[ent][$(this).attr('data-event-id')] =
		{
			attending : 1,
			note      : ' ',
			fee       : $(this).attr('data-fee'),
			prepay    : ($(this).attr('data-when-to-pay')=='Pre-Pay')
		};
		hasValues=true;

	});

	$("#cart_total_container").find(".discountItemPlaceholder").remove();
    var student_id = $("#student_id").val();
	var url = '/frontend/bookings/get_order_table_html';
	if(hasValues || override){
		url = '/frontend/bookings/add_to_cart';
	}

	$.ajax({
		type    : "POST",
		url     : url,
		data    : {booking:schedule_events, override: override, student_id: student_id},
		success : function (res) {

			if (res == null) {
				return;
			}

			var total=0;

			var discounts = {};

			$("#continue-button").css({ opacity: 1 });
			$(".booking-item-inputs").remove();
			$(".prepay_container .checkout-item, .pay_as_you_go_container .checkout-item").remove();

            $(".discountItemPlaceholder").remove();

			var offers_to_display = {};

			res.forEach(function(obj){
				if (obj.discounts) {
					if (obj.discount == 0) {
						for (var i = 0 ; i < obj.discounts.length ; ++i) {
							if (obj.discounts[i].remaining_conditions && obj.discounts[i].remaining_conditions.length > 0) {
								var impossible = false;
								for (var j = 0 ; j < obj.discounts[i].remaining_conditions.length ; ++j) {
									if (obj.discounts[i].remaining_conditions[j].type == 'impossible') {
										impossible = true;
										break;
									}

								}
								if (!impossible) {
									offers_to_display[obj.discounts[i].id] = obj.discounts[i];
								}
							}
						}
					}
				}

                if ($("#search-offers").data('display-offers') == '1') {
                    $("#search-offers ol").html("");
                    $("#search-offers").css("display", "none");
                    var offers_html = "";
                    for (var o in offers_to_display) {
                        offers_html += '<li>' + '<b>' + offers_to_display[o].title + '</b><br /><p>' + offers_to_display[o].summary + '</p>' + '</li>';
                    }
                    if (offers_html != "") {
                        $("#search-offers ol").html(offers_html);
                        $("#search-offers").css("display", "");
                    }
                }


				if (obj.type != 'subtotal') {
					var first_timeslot = null;
					for (var i in obj.timeslot_details) {
						first_timeslot = obj.timeslot_details[i];
						break;
					}
					add_item_to_cart({
						when_to_pay: obj.prepay ? 'Pre-Pay' : 'Pay as you go',
						course_title: obj.details.title,
						date_formatted: obj.details.start_date,
						fee: obj.fee,
						per_class_fee:  first_timeslot.fee_amount,
						per_day_fee:  obj.details.fee_per == 'Day' ? obj.details.fee_amount : null,
						event_ids: obj.periods_attending,
						schedule_id: obj.id,
						booking_type: obj.fee_per,
						user_permission: $(".details-wrap[data-schedule-id=" + obj.id + "]").data("logged-in-user-permission"),
						count: obj.periods_attending.length,
						fee_per: obj.fee_per,
						amendable: obj.details.amendable,
						no_message: true,
						inputs_only: false
					});
				}

				if(obj.type=='schedule') {
					if (obj.prepay) {
						total += obj.total;
					}

					if(obj.discount!=0){
						var discount_index = 0;
						obj.discounts.forEach(function (disc) {
							if(disc.amount!=0 && obj.prepay == true) {
                                if (discounts[disc.id]) {

                                } else {
                                    discounts[disc.id] = {
                                        amount: 0,
                                        template: null
                                    };
                                }

                                discounts[disc.id].amount += disc.amount;
                                if (discounts[disc.id].template == null) {
                                    discounts[disc.id].template = $discountItemTemplate.clone();
                                    discounts[disc.id].template.css("display", "");
                                    discounts[disc.id].template.removeClass("template");

                                    discounts[disc.id].template.find(".title").html(disc.title);
                                    discounts[disc.id].template.data("schedule-id", obj.id);
                                    discounts[disc.id].template.data("discount-id", disc.id);
                                    discounts[disc.id].template.data("discount", discounts[disc.id].amount);
                                    /* why would we need these inputs?
                                     discountTemplate.append(
                                     '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][id]" value="' + disc.id + '" />' +
                                     '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][ignore]" value="0" />' +
                                     '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][code]" value="" />' +
                                     '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][amount]" value="' + disc.amount + '" />'
                                     );*/

                                    if ($("#cart_total_container").length > 0) {
                                        $("#cart_total_container").prepend(discounts[disc.id].template);
                                    } else {
                                        $(".prepay_container > ul").append(discounts[disc.id].template);
                                    }
                                }

                                discounts[disc.id].template.find(".amount").html("-" + discounts[disc.id].amount);
								++discount_index;
							}
						})
					}
				}
				if(obj.type=='subtotal') {
					if(obj.discount!=0){
						var discount_index = 0;
						obj.discounts.forEach(function (disc) {
							if(disc.amount!=0) {
								if (discounts[disc.id]) {

								} else {
									discounts[disc.id] = {
										amount: 0,
										template: null
									};
								}

								discounts[disc.id].amount += disc.amount;
								total -= disc.amount;
								if (discounts[disc.id].template == null) {
									discounts[disc.id].template = $discountItemTemplate.clone();
									discounts[disc.id].template.css("display", "");
									discounts[disc.id].template.removeClass("template");

									discounts[disc.id].template.find(".title").html(disc.title);
									discounts[disc.id].template.data("schedule-id", obj.id);
									discounts[disc.id].template.data("discount-id", disc.id);
									discounts[disc.id].template.data("discount", discounts[disc.id].amount);
									/* why would we need these inputs?
									 discountTemplate.append(
									 '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][id]" value="' + disc.id + '" />' +
									 '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][ignore]" value="0" />' +
									 '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][code]" value="" />' +
									 '<input type="hidden" name="discounts[' + obj.id + '][' + discount_index + '][amount]" value="' + disc.amount + '" />'
									 );*/

									if ($("#cart_total_container").length > 0) {
										$("#cart_total_container").prepend(discounts[disc.id].template);
									} else {
										$(".prepay_container > ul").append(discounts[disc.id].template);
									}
								}

								discounts[disc.id].template.find(".amount").html("-" + discounts[disc.id].amount);
								++discount_index;
							}
						})
					}
				}
			});

			var subtotal = total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

			// Update totals on search results page
			$(".cart_total_amount").text('€'+subtotal);
			//UPdate totals on the checkout
			//$('#checkout-breakdown-subtotal').text(subtotal);
			calculate_checkout_total();

            refresh_cart_messages()
		}
	});

	var prepay_container = $('#prepay_container');
	var pay_as_you_go_container = $('#pay_as_you_go_container');

	var cart_prepay_bookings = prepay_container.find('.checkout-item');
	var cart_pay_as_you_go_bookings = pay_as_you_go_container.find('.checkout-item');

	if(cart_prepay_bookings.length > 0 || cart_pay_as_you_go_bookings.length > 0){
		$('#booking-cart-empty').addClass('hidden');
		if (cart_prepay_bookings.length > 0) {
			prepay_container.removeClass('hidden');
		}else{
			prepay_container.addClass('hidden');
		}
		if (cart_pay_as_you_go_bookings.length > 0) {
			pay_as_you_go_container.removeClass('hidden');
		}else{
			pay_as_you_go_container.addClass('hidden');
		}
		var cart_total_amount = $(".cart_amount");

		//  var sum = 0;
//         $('.cart_amount').each(function(){
//             sum += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
//         });
		//   $(".cart_total_amount").text('€'+sum);
	}else{
		prepay_container.addClass('hidden');
		pay_as_you_go_container.addClass('hidden');
		$(".cart_total_amount").text('€'+0);
		$('#booking-cart-empty').removeClass('hidden');
	}
}

function get_event_ids_in_cart() {
	var array_of_event_ids = [];
//     $('.purchase-packages').find('li').each(function () {
	$('.purchase-packages').find('input.cart_hidden_inputs').each(function () {
		var $this = $(this);
		if($this.data('event-id') && $this.data('event-id')!=''){
			array_of_event_ids.push($this.data('event-id'));
		}
	});
	return array_of_event_ids;
}

function get_schedule_ids_in_cart() {
	var array_of_schedule_ids = [];
//     $('.purchase-packages').find('li').each(function () {
	$('.purchase-packages').find('input.cart_hidden_inputs').each(function () {
		var $this = $(this);
		if($this.data('event-id') == '' && $this.data('schedule-id')){
			array_of_schedule_ids.push($this.data('schedule-id'));
		}
	});
	return array_of_schedule_ids;
}

function htmlentities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

$(document).ready(position_cart_menu);
$(window).scroll(position_cart_menu);
$(window).resize(position_cart_menu);
$('#sidebar-search').on('shown.bs.collapse', function()
{
	// If this has been manually opened, flag it as the user's preference
	$(this).addClass('sidebar-search-open_by_choice');
	position_cart_menu();
});

function position_cart_menu()
{
	var $sidebar = $('#right-section').find('.checkout-right-sect');

	if ($sidebar.length > 0)
	{
		var $search_filters     = $('#sidebar-search');
		var content_section     = document.querySelector('#header_paging_controle, #booking-checkout-form');
		var sidebar_coordinates = $sidebar[0].getBoundingClientRect();
		var window_height       = $(window).height();

		$sidebar.removeClass('fixed-bottom').removeClass('fixed-top').css('bottom', '');

		// Sidebar is styled differently on smaller screens
		if ($(window).width() > 990)
		{
			var item_head_coordinates = document.getElementsByClassName('item-summary-head')[0].getBoundingClientRect();
			var footer_coordinates    = document.getElementsByClassName("footer")[0].getBoundingClientRect();
			var content_coordinates   = content_section.getBoundingClientRect();
			var content_visible       = (content_coordinates.bottom > 0);

			// Collapse the "search" section, if appropriate, before 'sticking' the sidebar
			var open_by_choice  = ($search_filters.attr('aria-expanded') == 'true' && $search_filters.hasClass('sidebar-search-open_by_choice'));
			var scrolled_enough = content_visible && content_coordinates.top < item_head_coordinates.height;
			if ( ! open_by_choice && scrolled_enough)
			{
				$search_filters.collapse('hide').css('height', 0);
			}

			// Only continue, if the screen is big enough to contain the sidebar
			if (sidebar_coordinates.height < window_height)
			{
				var footer_visible = (footer_coordinates.top < window_height);
				var sidebar_reaches_footer = sidebar_coordinates.height> footer_coordinates.top;

				if (scrolled_enough && ! (footer_visible && sidebar_reaches_footer))
				{
					$sidebar.addClass('fixed-top').css('bottom', '');
				}
				else if (content_visible && footer_visible && sidebar_reaches_footer)
				{
					$sidebar.addClass('fixed-bottom').css('bottom', window_height - footer_coordinates.top);
				}
			}
		}
	}
}

$(document).on('mouseup', '#show_profile_pass' , function(){
    $(this.getAttribute('data-target')).attr('type', 'password');
})
$(document).on('mousedown', '#show_profile_pass' , function() {
    $(this.getAttribute('data-target')).attr('type', 'text');
});
