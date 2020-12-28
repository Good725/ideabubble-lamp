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
        var course_id = $(this).data('course_id');
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

    // Needed for input info popups
    if ($.fn.popover) {
        jQuery('.popinit').popover({ html : true ,trigger:'hover'});
    }

    // init bootstrap-tooltip
    if ($.fn.tooltip) {
        jQuery('a[rel="tooltip"]').tooltip();
    }

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


    // Open regular modal
    $(document).on('click', '[data-toggle="modal"]', function(ev)
    {
        ev.preventDefault();
        var $modal = $($(this).data('target'));

        var item_id = $(this).data('item_id');
        if (item_id) {
            $modal.find('.modal-item_id').val(item_id);
        }

        var subtitle = $(this).data('subtitle');
        if (subtitle) {
            var $subtitle = $modal.find('.popup-subtitle');

            if ($subtitle.length == 0) {
                $modal.find('.popup-title').append('<span class="popup-subtitle"></span>');
            }
            $modal.find('.popup-subtitle').text(subtitle);
        }

        $modal.ib_modal_show();
    });

	// Dismiss when "close" button is clicked
	$('.sectionOverlay').find('.basic_close, .cancel').click(function()
	{
		$(this).parents('.sectionOverlay').css('display', 'none');
		$('html, body').css('overflowY', '');
	});

    $(document).on('hide.bs.modal', '.sectionOverlay', function() {
        $('html, body').css('overflowY', '');
    });

    $('#login-overlay').on('click', '.cancel', function() {
        $('#login-overlay').collapse('hide');
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

    $.fn.ib_modal = function(action) {
        if (action == 'close' || action == 'hide') {
            $(this).ib_modal_hide();
        } else {
            $(this).ib_modal_show();
        }
    };

    $.fn.ib_modal_show = function() {
        if ($(this).hasClass('sectionOverlay')) {
            $('.sectionOverlay').hide();
            $(this).show();
            $(this).focus();
            $('body').css('overflowY', 'hidden');
        }
    };

    $.fn.ib_modal_hide = function() {
        if ($(this).hasClass('sectionOverlay')) {
            $(this).hide();
            if ($('.sectionOverlay:visible').length) {
                $('html, body').css('overflowY', '');
            }
        }
    };

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
     #Height toggles
    \*------------------------------------*/
    $.fn.ib_toggleHeight = function(height)
    {
        if ($(this).data('height')) {
            height = $(this).data('height');
        }

        $(this).addClass('toggleable_height');

        if (height) {
            $(this).css('max-height', height);
        }

        if ($(this)[0] && $(this)[0].scrollHeight <= $(this).height()) {
            // Description is short enough to not need to be toggled
            $(this).removeClass('show_more show_less');
        }
        else {
            // Wire up "show more" and "show less" buttons
            var $description = $(this);
            $(this).find('\+ .toggleable_height-toggles .toggleable_height-show_more').on('click', function() {
                $description.removeClass('show_less').addClass('show_more');
                if (height) {
                    $description.css('max-height', '');
                }
            });

            $(this).find('\+ .toggleable_height-toggles .toggleable_height-show_less').on('click', function() {
                $description.removeClass('show_more').addClass('show_less');
                if (height) {
                    $description.css('max-height', height);
                }

                // If the description is out of view after being collapsed, scroll up to it
                var coordinates = $description[0].getBoundingClientRect();
                if (coordinates.bottom < 0) {
                    $description[0].scrollIntoView();
                }
            });
        }
    };

    $('.toggleable_height').ib_toggleHeight();




	/*------------------------------------*\
	 #Sliders
	\*------------------------------------*/
	var $banner = $('#home-banner-swiper');
	if (typeof Swiper != 'undefined' && $banner.length && $banner.data('slides') > 1)
    {
        var banner_swiper = new Swiper('#home-banner-swiper', {
            autoplay: {
                delay: ($banner.data('autoplay') || true)
            },
            clickable: false,
            direction: $banner.data('direction'),
            effect: $banner.data('effect'),
            speed: $banner.data('speed'),
            loop: true,
            pagination: {
                el: '#home-banner-swiper .swiper-pagination',
                clickable: true
            },
            navigation: {
                prevEl: '#home-banner-swiper .swiper-button-prev',
                nextEl: '#home-banner-swiper .swiper-button-next'
            },
            slideChange: function() {
                console.log('111');
                // Stop videos when you change slide
                $(this).find('iframe').each(function() {
                    var src = this[0].src;
                    this[0].src = src;
                });

                $(this).find('videos').each(function() {
                    this[0].pause();
                });

                $(this).find('.video-wrapper').ib_cover_video();
            }
        });

        // If the user starts a video, don't let the banner change slide
        $banner.on('click', '.video-wrapper', function() {
            banner_swiper.autoplay.stop();
        });

        banner_swiper.on('slideChange', function() {
            // Stop videos when you change slide
            var src;
            this.$el.find('iframe').each(function() {
                src = this.src;
                this.src = src;
            });

            this.$el.find('videos').each(function() {
                this.pause();
            });

            $(this.$el).find('.video-wrapper').ib_cover_video();
        })
    }

    if (document.getElementById('news-slider')) {
        new Swiper('#news-slider', {
            autoplay : {
                delay: 5000
            },
            loop : true,
            pagination : {
                el: '#news-section .swiper-pagination',
                clickable : true
            },
            navigation : {
                prevEl : '#news-section .swiper-button-prev',
                nextEl : '#news-section .swiper-button-next'
            }
        });
    }

    if (document.querySelectorAll('#testimonials-slider .swiper-slide').length > 1) {
        new Swiper('#testimonials-slider', {
            autoplay : {
                delay: 5000
            },
            loop : true,
            pagination : {
                el: '#testimonials-section .swiper-pagination',
                clickable : true
            },
            navigation : {
                prevEl : '#testimonials-section .swiper-button-prev',
                nextEl : '#testimonials-section .swiper-button-next'
            }
        });
    }

    let slides_per_view;

    if (document.getElementById('upcoming-courses-carousel')) {
        slides_per_view = calculate_slides_per_view({max: 3});
        var upcoming_course_slider = new Swiper('#upcoming-courses-carousel', {
            slidesPerView : slides_per_view > 3 ? 3 : slides_per_view,
            pagination : {clickable: true},
            navigation : {
                prevEl : '#upcoming-courses-carousel-prev',
                nextEl : '#upcoming-courses-carousel-next'
            },
            slidesPerGroup : slides_per_view > 3 ? 3 : slides_per_view,
            spaceBetween : 30
        });
    }

    slides_per_view = calculate_slides_per_view();
    var virtual_slides = $('#courses-carousel').data('slides');
    var virtual = (virtual_slides) ? { slides: virtual_slides} : false;

    var carousel_slider = new Swiper('#courses-carousel', {
        slidesPerView : slides_per_view,
        pagination : {clickable: true},
        navigation : {
            prevEl : '#courses-carousel-prev',
            nextEl : '#courses-carousel-next'
        },
        slidesPerGroup : slides_per_view,
        spaceBetween : 25,
        virtual : virtual
    });

    $(window).resize(function()
    {
        let slides_per_view = calculate_slides_per_view();

        carousel_slider.params.slidesPerView  = slides_per_view;
        carousel_slider.params.slidesPerGroup = slides_per_view;

        if (upcoming_course_slider) {
            slides_per_view = calculate_slides_per_view({max: 3});
            upcoming_course_slider.params.slidesPerView  = slides_per_view;
            upcoming_course_slider.params.slidesPerGroup = slides_per_view;
        }
    });

    function calculate_slides_per_view(args)
    {
        const max = args && args.max ? args.max : 4;
        var width = window.innerWidth;
        var count = 4;
        if      (width <  768) count = 1;
        else if (width <  990) count = 2;
        else if (width < 1260) count = 3;

        return count > max ? max : count;
    }

    $('.availability-course-details').on(':ib-expanded', availability_course_details_expanded);

    // When the booking button is clicked...
    $(document)
        .on('click', '.availability-book', function() {
            $(this).parents('.availability-timeslot, .availability-fulltime').addClass('booked');
        })
        .on('click', '.availability-unbook', function() {
            $(this).parents('.availability-timeslot, .availability-fulltime').removeClass('booked');
        });




    /*------------------------------------*\
     #Site search
    \*------------------------------------*/
    $('.site-search-input').each(function() {
        var $search = $(this);

        $search.autocomplete({
            source: function(data, callback) {
                $.getJSON('/frontend/pages/ajax_search_autocomplete', { term: $search.val() }, callback);
            },
            select: function (event, ui) {
                window.location = '/'+ui.item.name_tag;
            }
        });
    });




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



    $(document).on('click', '.alert .close-btn', function() {
        $(this).parents('.alert').remove();
    });

    $(document).ready(function() {
        setTimeout(function() {
            $('.alert.popup_box:not(.alert-stay)').addClass('fadeOutRight');
            setTimeout(function(){ $('.alert.popup_box:not(.alert-stay) .close').trigger('click'); }, 1000);
        }, 12000);
    });


    /*------------------------------------*\
     #Modals
    \*------------------------------------*/
    // Open when trigger is clicked
    $('body').on('click', '[data-toggle="ib-modal"]', function(ev) {
        ev.preventDefault();

        var $modal = $($(this).data('target'));

        // If the button that trigger the modal is part of a form, whose data is to be transferred to a form within the modal
        if ($(this).data('transfer_form_data')) {
            var $form1 = $(this).parents('form');
            var $form2 = $modal.find('form');

            $(':input[name]', $form2).val(function() {
                return $(':input[name=' + this.name + ']', $form1).val();
            });
        }

        $modal.show().trigger(':ib-modal-open');
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

    // Dismiss when close icon is clicked
    $('.header-menu-close').on('click', function()
    {
        $('.header-menu').hide();
        $('.header-menu-expand').removeClass('expanded');
    });

	// Dismiss when clicked away from
	$(document).on('click', function(ev) {
		if ( ! $(ev.target).closest('.header-menu, .header-menu-expand').length) {
			$('.header-menu').hide();
			$('.header-menu-expand').removeClass('expanded');
		}
	});

    $('.level_1.has_submenu > [href="#"]').on('click', function() {
        $(this).find('\+ .submenu-expand').click();
    });

	$('.level2 .submenu-expand').on('click', function()
	{
		$(this).closest('li').toggleClass('expanded');
	});

    $('#top-nav-searchbar-button').on('click', function() {
        $('#top-nav-searchbar-wrapper').toggleClass('shown');
    });


	/*------------------------------------*\
	 #Calendar
	\*------------------------------------*/
	$(document).ready(function() {
		var $calendar = $('#sidebar-calendar');
		if ($calendar.length > 0) {
			$calendar.eventCalendar({
				eventsjson: '/frontend/frontend/eventcalendar_items',
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
     # Position the sidebar menu as the user scrolls
    \*------------------------------------*/
    function position_fixed_sidebar()
    {
        $('#fixed_sidebar-wrapper').removeClass('fixed-bottom').removeClass('fixed-top').css('bottom', '');

        if (document.getElementById('fixed_sidebar') && $(window).width() >= 768)
        {
            var positioner          = document.getElementById('fixed_sidebar-positioner');
            var sidebar_coordinates = document.getElementById('fixed_sidebar').getBoundingClientRect();
            var window_height       = $(window).height();

            if (positioner) {
                positioner.style.minHeight = $(positioner).height()+'px';
            }

            if (sidebar_coordinates.height < window_height && $('#fixed_sidebar').height() < $(positioner).height())
            {
                var footer_coordinates     = document.getElementById('footer').getBoundingClientRect();
                var content_coordinates    = positioner.getBoundingClientRect();

                var footer_visible         = (footer_coordinates.top < window_height);
                var sidebar_reaches_footer = sidebar_coordinates.height > footer_coordinates.top;
                var sidebar_header_height  = $('#fixed_sidebar-header').outerHeight();

                if (content_coordinates.top < sidebar_header_height && ! (footer_visible && sidebar_reaches_footer)) {
                    $('#fixed_sidebar-wrapper').removeClass('fixed-bottom').addClass('fixed-top').css('bottom', '');
                }
                else if (footer_visible && sidebar_reaches_footer) {
                    var bottom_position = window_height - footer_coordinates.top + sidebar_coordinates.height - sidebar_header_height;
                    $('#fixed_sidebar-wrapper').removeClass('fixed-top').addClass('fixed-bottom').css('bottom', bottom_position);
                }
                else {
                    $('#fixed_sidebar-wrapper').removeClass('fixed-top').removeClass('fixed-top').css('bottom', '');
                }
            }
        }
    }

    $(window).scroll(position_fixed_sidebar);
    $(window).resize(position_fixed_sidebar);
    $(window).trigger('scroll');



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

    // Only one mode is used on small screens
    $(window).resize(function()
    {
        if (window.innerWidth < 1024)
        {
            let mode = $('.course-list-display_options').data('mobile_mode') || 'grid';
            $('#course-list-display_'+mode).prop('checked', true).trigger('change');
        }
    }).trigger('resize');

	/* Display fee when a schedule is selected */
	$course_results.on('change', '.course-widget-schedule', function()
	{
		var $price        = $(this).parents('.course-widget').find('.course-widget-price');
		var $price_amount = $price.find('.course-widget-price-amount');
		var fee           = $(this).find(':selected').attr('data-fee');

		if (!fee)
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
    if ($filters.find('.search-filter-checkbox:checked').length > 0) {
        update_new_search_results();
    }

    // Clear all search criteria
    $('.search-filters-clear').on('click', function()
    {
        $('.search-filter-checkbox').prop('checked', false);
        $('.search-filter-dropdown').removeClass('filter-active');
        $('.search-filter-amount, .search-filter-total').html('');
        $('.search-filter-selected_items').html('');
        $('.search-filters-clear').removeClass('visible');

        update_new_search_results();
    });

    // Add criteria on filters click
    var search_filter_timer = null;
    $filters.on('change', '.search-filter-checkbox', function()
    {
        var $dropdown = $(this).parents('.search-filter-dropdown');
        var $amount   = $dropdown.find('.search-filter-amount');
        var $clear    = $('.search-filters-clear');
        var amount    = $dropdown.find('.search-filter-checkbox:checked').length;
        var total     = $('.search-filter-checkbox:checked').length;
        var id        = $(this).data('id');

        // Display selected items under the label
        if (this.checked) {
            var name = $(this).parents('.search-filter-dropdown-item').text().trim();
            $dropdown.find('.search-filter-selected_items').append('<span data-id="'+id+'">'+name+'</span>');
        } else {
            $dropdown.find('.search-filter-selected_items [data-id="'+id+'"]').remove();
        }

        if (amount == 0) {
            $dropdown.removeClass('filter-active');
            $amount.html('');
        } else {
            $amount.html(amount);
            $dropdown.addClass('filter-active');
        }

        $('.search-filter-total').html(total == 0 ? '' : total);

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

    $filters.on('change', '.search-filter-radio', function()
    {
        var group_name    = $(this).attr('name');
        var $dropdown     = $(this).parents('.search-filter-dropdown');
        var $selected     = $('.search-filter-radio[name="'+group_name+'"]:checked');
        var selected_text = $selected.parents('.search-filter-dropdown-item').text().trim();

        $dropdown.find('.search-filter-selected_items').html('<span>'+selected_text+'</span>');
    });

    // Mobile blackout. Dismiss menu when clicked.
    $('.search-filters-blackout').on('click', function() {
        $('#search-filters-toggle').trigger('click');
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
            // See if the item already exists before adding it again
            if ($('.search-criteria-li[data-id="'+this.getAttribute('data-id')+'"]').length == 0)
            {
                var $li = $('<li class="search-criteria-li">'+$('#course_filter_criteria_template').html()+'</li>');
                $li
                    .data('id', this.getAttribute('data-id'))
                    .attr('data-id', this.getAttribute('data-id'));
                $li.find('.search-criteria-category').html(this.getAttribute('data-category'));
                $li.find('.search-criteria-value').html(this.getAttribute('data-value'));

                $li.insertBefore('#search-criteria-reset-li');
            }
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

	var $search_criteria = $('#course_filter_criteria');

	// When a criteria item remove icon is clicked, remove the item and un-check its checkbox
	$search_criteria.on('click', '.search-criteria-remove', function()
	{
		var $li = $(this).parents('li');
		// Uncheck the corresponding checkbox and trigger its change action
		var id = $li.data('id');
		$('input[data-id="'+id+'"]').prop('checked', false).trigger('change');

		// Courses and events don't have a checkbox in the sidebar. These are handled separately.
		if ($li.find(':input').length) {
			$li.remove();
			update_search_results();
		}
	});

    // Remove all search filters
    $('.search-criteria-reset').on('click', function() {
        $('.sidebar-filter-options :checked').prop('checked', false);
        $('.search-criteria-li').remove();
        $('#course-filter-keyword').val('');
        update_search_results();
    });

	// Keyword search
	$('#course-filter-keyword').on('change', update_search_results);

    // If any filters are auto-filled via back/forward button, re-apply them.
    // No event listener for catching back/forward button autofill.
    // Any arbitrary time delay, e.g. 1 millisecond will be enough to ensure this runs after the fields have autofilled.
    setTimeout(function() {
        const $filters_changed = $('.sidebar-filter-li :checked').change();
        const keyword_changed = $('#course-filter-keyword').val();

        if (!$filters_changed.length && keyword_changed) {
            update_search_results();
        }
    }, 1);

    // Navigate through pagination results
    $course_results.on('click', '.pagination a:not(.disabled)', function(ev)
    {
        ev.preventDefault();
        $course_results.find('.pagination .current').removeClass('current');
        $(this).addClass('current');

        update_search_results({page: $(this).attr('data-page')});
        $course_results[0].scrollIntoView();
    });

    // Change the order of results
    $(document).on('change', '[name="course_list_sort"]', update_search_results);

    function update_search_results(args)
    {
        args = args || {};

        var $sidebar     = $('#sidebar');
        var sort         = $('[name="course_list_sort"]:checked').val();
        var display      = $('[name="course_list_display"]:checked').val();
        var keywords     = $('#course-filter-keyword').val();
        var county_ids   = [];
        var course_county_ids = [];
        var location_ids = [];
        var year_ids     = [];
        var category_ids = [];
        var level_ids    = [];
        var course_ids   = [];
        var subject_ids  = [];
        var type_ids     = [];
        var event_ids    = [];

        var page = args.page ? args.page : 1;

        $sidebar.find('[name="location_ids[]"]:checked').each(function(){location_ids.push(this.value);});
        $sidebar.find('[name="county_ids[]"]:checked'  ).each(function(){  county_ids.push(this.value);});
        $sidebar.find('[name="course_county_ids[]"]:checked').each(function(){ course_county_ids.push(this.value);});
        $sidebar.find('[name="year_ids[]"]:checked'    ).each(function(){    year_ids.push(this.value);});
        $sidebar.find('[name="category_ids[]"]:checked').each(function(){category_ids.push(this.value);});
        $sidebar.find('[name="level_ids[]"]:checked'   ).each(function(){   level_ids.push(this.value);});
        $sidebar.find('[name="subject_ids[]"]:checked' ).each(function(){ subject_ids.push(this.value);});
        $sidebar.find('[name="type_ids[]"]:checked'    ).each(function(){    type_ids.push(this.value);});
        $('.filter-course_ids'                         ).each(function(){  course_ids.push(this.value);});
        $('.filter-event_ids'                          ).each(function(){   event_ids.push(this.value);});

        $.ajax(
        {
            url     : '/frontend/courses/ajax_filter_results',
            data    : {
                'county_ids'    : county_ids,
                'course_county_ids' : course_county_ids,
                'location_ids'  : location_ids,
                'year_ids'      : year_ids,
                'category_ids'  : category_ids,
                'level_ids'     : level_ids,
                'subject_ids'   : subject_ids,
                'type_ids'      : type_ids,
                'course_ids'    : course_ids,
                'event_ids'     : event_ids,
                'contact_id'    : $('#search-results-organizer_id').val(),
                'venue_id'      : $('#search-results-venue_id').val(),
                'keywords'      : keywords,
                'sort'          : sort,
                'display'       : display,
                'page'          : page,
                'reminder'      : 0,
                'timeslots'     : 1
            },
            type     : 'post',
            dataType : 'json'
        }).done(function(result)
            {
                $('#course-results').html(result);
            });
    }

    $('#news-feed-pagination').on('click', '[data-page]', function() {
        var $this = $(this);
        var $feed = $('#news-feed-pagination');
        var page  = $(this).data('page');

        $.ajax('/frontend/news/ajax_get_paginated_news?page=' + page).done(function(result) {
            $('#news-feed-listing').html(result.html);

            var page   = $this.data('page');
            var length = $feed.find('li').length - 2;

            // Change the active button
            $feed.find('.current').removeClass('disabled').removeClass('current');
            $feed.find('.pagination-item [data-page="'+page+'"]').addClass('current').addClass('disabled');

            // Disable prev/next, if the first/last page
            $feed.find('.pagination-prev a').toggleClass('disabled', (page == 1     )).data('page', page - 1);
            $feed.find('.pagination-next a').toggleClass('disabled', (page == length)).data('page', page + 1);
        });
    });




    /*------------------------------------*\
     #Map
    \*------------------------------------*/
    function initMap(mapid)
    {
        $(mapid).each(function(){
            var container = this;
            var map = null;
            var initX = parseFloat($(container).data("init-x"));
            var initY = parseFloat($(container).data("init-y"));
            var initZ = parseInt($(container).data("init-z"));
            var targetX = $(container).data("target-x");
            var targetY = $(container).data("target-y");
            var showmarker = false;

            if (initX && initY) {
                showmarker = true;
            }
            if (!initX) {
                initX = 53.32693558541906;
            }
            if (!initY) {
                initY = -6.416015625;
            }
            if (!initZ) {
                initZ = 10;
            }

            var options = {
                center:new google.maps.LatLng(initX, initY),
                zoom:initZ,
                mapTypeId:google.maps.MapTypeId.ROADMAP,
                panControl:true,
                zoomControl:true,
                mapTypeControl:true,
                streetViewControl:false,
                overviewMapControl:true

            };

            map = new google.maps.Map(container, options);
            var geocoder = new google.maps.Geocoder();

            var marker = null;
            if (showmarker) {
                marker = new google.maps.Marker(
                    {
                        position: new google.maps.LatLng(initX, initY),
                        map: map
                    }
                );
            }

            var trackXY = true;
            $(targetX + "," + targetY).on("change", function(){
                if (trackXY) {
                    if (marker != null) {
                        marker.setMap(null);
                    }
                    var lat = parseFloat($(targetX).val());
                    var lng = parseFloat($(targetY).val());
                    map.setCenter(new google.maps.LatLng(lat, lng));
                    marker = new google.maps.Marker(
                        {
                            position: new google.maps.LatLng(lat, lng),
                            map: map
                        }
                    );
                }
            });
        });
    }

    initMap('#event-map');




    /*------------------------------------*\
     #Donation form
    \*------------------------------------*/
    // Toggle visibility of donation form fields
    $('[name="donation_type"]').on('change', function()
    {
        var $form                     = $(this).parents('form');
        var donation_type             = $('[name="donation_type"]:checked').val();
        var $amount_fieldset          = $('#payment_form_amount_fieldset').parents('li'); // step 1
        var $contact_details_fieldset = $('#payment_form_contact_details_fieldset').parents('li'); // step 2
        var $payment_method_fieldset  = $('#payment_form_payment_select_fieldset').parents('li'); // step 3 a
        var $payment_details_fieldset = $('#payment_form_payment_details_fieldset').parents('li'); // step 3 b
        var $buttons                  = $('[name="submit"], #payment_form_postal_submit, #pay_online_submit_button, #stripeButton, #paypal_payment_button');

        $buttons.hide();
        $form.find('> ul > li').show();

        $amount_fieldset.hide();
        $contact_details_fieldset.hide();
        $payment_method_fieldset.hide();
        $payment_details_fieldset.hide();

        switch (donation_type)
        {
            case 'once_off':
                $amount_fieldset.show();
                $contact_details_fieldset.show();
                $payment_method_fieldset.show();
                break;
            case 'direct_debit':
                $amount_fieldset.show();
                $contact_details_fieldset.show();
                $payment_details_fieldset.show();
                $('#payment_form_direct_debit_submit').show();
                $('[name="payment_method"]:checked').prop('checked', false).trigger('change');
                break;
            case 'postal':
                $('#payment_form_terms').parents('li').hide();
                $('#payment_form_postal_submit').show();
                break;
            default:
                $form.find('> ul > li:not(:first-child):not(:last-child)').hide();

        }
    }).trigger('change');




	/*------------------------------------*\
	 #Course details
	\*------------------------------------*/
	$("#schedule_selector").on("change", function()
	{
		var id = $(this).val();
		var event_id = (this.selectedIndex != -1) ? $(this.options[this.selectedIndex]).data('event_id') : '';
        var $from_price_wrapper   = $('.course-details-price--from');
        var $normal_price_wrapper = $('.course-details-price--normal');
        var $online_price_wrapper = $('.course-details-price--online');
        var is_group_booking = (this.selectedIndex != -1) ? $(this.options[this.selectedIndex]).data('is_group_booking') == '1' : false;

        if (is_group_booking || $(this).data('group_only') == 1) {
            $(".group_bookings_div").removeClass("hidden");
        } else {
            $(".group_bookings_div").addClass("hidden");
        }
        var $num_delegates_el = $('#num_delegates');

		if (id.length > 0)
		{
            $("#brochure_download [name=schedule_id]").val(id);
			$.post('/frontend/courses/get_schedule_price_by_id', {sid: id, event_id: event_id}, function (data)
			{
                $from_price_wrapper.addClass('hidden');
                $normal_price_wrapper.find('.price').html(data.discount > 0 ? data.fee_amount.replace(/\B(?=(\d{3})+(?!\d))/g, ",") : data.price.replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                if (data.discount == 0) {
                    $normal_price_wrapper.addClass('hidden');
                } else {
                    $normal_price_wrapper.removeClass('hidden');
                }

                $('#course-details-wishlist-checkbox').prop('checked', data.is_wishlisted);

                $online_price_wrapper.find('.price').html(data.price.replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $online_price_wrapper.removeClass('hidden');
                $("#book-course").prop("disabled",  (data.allow_booking === "0"));
                $("#enquire-course").prop("disabled", false);
			});
			$.post('/frontend/courses/ajax_get_schedule_availability', {schedule_id: id}, function(data){
			    console.log(data);
                if (data.remaining == 0) {
                    $("#book-course").prop("disabled", true).addClass('hidden');
                    $("#add_to_waitlist_button").prop("disabled", false).removeClass('hidden');
                    if ($num_delegates_el) {
                        if ($num_delegates_el.is('select')) {
                            $num_delegates_el.find('option').remove();
                        } else {
                            $num_delegates_el.val(0);
                            $num_delegates_el.removeAttr('max');
                        }
                        $num_delegates_el.prop('disabled', 'disabled');
                    }
                } else {
                    $("#book-course").prop("disabled", false).removeClass('hidden');
                    $("#add_to_waitlist_button").prop("disabled", true).addClass('hidden');
                    if ($num_delegates_el) {
                        if ($num_delegates_el.is('select')) {
                            $num_delegates_el.find('option').remove();
                            var i;
                            var option = '';
                            for (i = 1; i <= data.remaining; i++) {
                                option = '<option value="' + i + '">' + i + '</option>';
                                $num_delegates_el.append(option);
                            }
                            $num_delegates_el.removeAttr('disabled');
                        } else {
                            $num_delegates_el.val(0);
                            $num_delegates_el.attr('max', data.remaining);
                            $num_delegates_el.removeAttr('disabled');
                        }
                    }


                }
            });
		}
		else
		{
            $normal_price_wrapper.addClass('hidden');
            $normal_price_wrapper.find('.price').html('');
            $online_price_wrapper.addClass('hidden');
            $online_price_wrapper.find('.price').html('');
            $from_price_wrapper.removeClass('hidden');


            $('#course-details-wishlist-checkbox').prop('checked', false);

            $('#trainer_name').hide().html('');
            $("#enquire-course, #book-course").prop("disabled", true);
            if ($num_delegates_el) {
                if ($num_delegates_el.is('select')) {

                    $num_delegates_el.find('option').remove();
                } else {
                    $num_delegates_el.val(0);
                    $num_delegates_el.removeAttr('max');
                }
                $num_delegates_el.prop('disabled', 'disabled');
            }
		}
	}).trigger('change');

    $('#course-details-wishlist-checkbox').on('change', function() {
        var schedule_id = $("#schedule_selector").val();
        var course_id = $("#course_selector, [name=interested_in_course_id]").val();
        var action = (this.checked ? 'wishlist_add' : 'wishlist_remove');
        var message, success, type;

        if (schedule_id) {
            $.post('/admin/contacts3/'+action, { course_id: course_id, schedule_id: schedule_id }, function (response) {
                if (action == 'wishlist_add') {
                    success = !!response.id;
                    message = success ? 'Schedule has been added to your wishlist' : 'Error adding to wishlist.';
                    type = success ? 'success' : 'error';
                }
                else {
                    success = !!response.removed;
                    message = success ? 'Schedule has been removed from your wishlist' : 'Error removing from wishlist.';
                    type = success ? 'warning' : 'error';
                }

                $('.wrapper').add_alert(message, type+' popup_box')
            });
        }
        else {
            $('.wrapper').add_alert('Please select a schedule first.', 'warning popup_box');
            $(this).prop('checked', false);
        }
    });

    // Add a class to the menu when in its sticky state.
    $(document).on('scroll', function() {
        var $menu = $('.course-details-menu');

        if ($menu.length) {
            var has_sticky_header = !!$('.has_sticky_header').length;
            var past_sticky_start = $(document).scrollTop() > (has_sticky_header ? 0 : $('#course-details-menu-sticky-start').offset().top);
            var before_sticky_end = $('#course-details-menu-sticky-end').offset().top - $(document).scrollTop() >= $menu.height();

            $menu.toggleClass('is_fixed', (past_sticky_start && before_sticky_end));
        }
    }).trigger('scroll');

    // Allow the menu's visibility to be toggled when it is in its sticky state.
    $('.course-details-menu-header').on('click', function() {
        var $menu = $(this).parents('.course-details-menu');

        if ($menu.hasClass('is_fixed')) {
            $menu.toggleClass('course-details-menu--collapsed');
        }
    });


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

		$("#availability-order_by").eq(0).find("option:nth-child("+index+")").prop('selected', true);
	});

    // Keep the two sorting controls in sync
    $('[name="availability-order_by"]').on('change', function() {
        var selected = $('[name="availability-order_by"]:checked').val();
        $('#availability-order_by').val(selected);
        update_new_search_results();
    });

    $('#availability-order_by').on('change', function() {
        var selected = $(this).val();
        $('[name="availability-order_by"][value="'+selected+'"]').prop('checked', true);
        update_new_search_results();
    });





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
        var payment_method = $(this).data('payment_method');
        var cc_sms = $(this).data("payment_method");
        $("[name=payment_method]").val(cc_sms);
        if($(this).data('payment_method') == 'cc') {
            // if there's a purchase order tab, we're assuming it was the org's billing details, switching it over to the individual below
            if($('#payment-tabs').find("[data-payment_method='purchase_order']").length === 1) {
                switch_billing_address('individual');
            }
            $('.billing-address-header').text('Billing address');
        } else if($(this).data('payment_method') == 'purchase_order') {
            switch_billing_address('organisation');
            $('.billing-address-header').text('Organisation billing address');
        }
        $(".purchase_order_primary_biller_details").toggleClass("hidden", payment_method == 'purchase_order');
        $("#checkout-breakdown .booking_fee.cc").addClass("hidden");
        $("#checkout-breakdown .booking_fee.sms").addClass("hidden");

        $("#checkout-breakdown .booking_fee." + cc_sms).removeClass("hidden");


        const $submit_button = $('.checkout-complete_booking');
        if ($(this).data('payment_method') == 'sales_quote') {
            $submit_button.html($submit_button.data('sales_quote_text'));
        } else {
            $submit_button.html($submit_button.data('book_text'));
        }
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

	$("#guardian_auth_send").on("click", function(){
		var btn = this;
		btn.disabled = true;
		var data = {};
		data.amount = $("[name=amount]").val();
		data.mobile = $("[name=mobile_code]").val() + $("[name=mobile_number]").val();
		data.student_id = $("[name=student_id]").val();
		$.post(
				"/frontend/bookings/send_parent_auth_code",
				data,
				function (response) {
					if (response.error) {
						var $clone = $('#checkout-error_message-template').clone();
						$clone.removeClass('hidden').find('.checkout-error_message-text').html(response.error);
						$('#checkout-error_messages').append($clone)[0].scrollIntoView();
						btn.disabled = false;
					} else {
						$("[name=guardian_auth_id]").val(response.id);
						$("#guardian_auth_sent").removeClass("hidden");
					}
				}
		);
	});
});

/*------------------------------------*\
 #Checkout
\*------------------------------------*/
$(document).ready(function()
{
    // Tabs
    if ($.fn.tabs) {
        jQuery( "#payment-tabs" ).tabs({active: 1});
    }

    /* Countdown - Redirect the user to a different page after a certain amount of time */
    var $countdown = $('#checkout-countdown');

    if ($countdown[0]) {
        var hours_block   = document.getElementById('checkout-countdown-hours');
        var minutes_block = document.getElementById('checkout-countdown-minutes');
        var seconds_block = document.getElementById('checkout-countdown-seconds');

        var date = new Date();
        date.setSeconds(date.getSeconds() + parseInt($countdown.data('time')));
        var countdown_date = date.getTime();

        var now, distance, hours, minutes, seconds;

        var interval = setInterval(function()
        {
            now = new Date().getTime();

            distance = Math.floor((countdown_date - now) / 1000);

            if (distance >= 0) {
                hours    = Math.floor(distance / (3600));
                minutes  = Math.floor((distance % 3600) / 60);
                seconds  = Math.floor(distance % 60);

                hours_block.innerHTML   = ((hours   < 10) ? '0' : '') + hours;
                minutes_block.innerHTML = ((minutes < 10) ? '0' : '') + minutes;
                seconds_block.innerHTML = ((seconds < 10) ? '0' : '') + seconds;
            } else {
                clearInterval(interval);
                var redirect = $countdown.data('redirect');

                if (typeof(cms_ns) != "undefined") {
                    // So the unsaved changes warning does not block this redirect.
                    cms_ns.modified = false;
                }

                window.location.href = redirect + '?timeout=1';
            }
        });
    }

    /* Space out the credit card number as the user enters it */
    $('#checkout-ccNum').on('keyup keypress change', function ()
    {
        var start   = this.selectionStart;
        var end     = this.selectionEnd;
        var oldleft = this.value.substr(0, start).replace(/[^ ]/g, '').length;

        $(this).val(function (index, value) {
            return value.replace(/\W/gi, '').replace(/(.{4})/g, '$1 ').substr(0, 19);
        });

        var newleft = this.value.substr(0, start).replace(/[^ ]/g, '').length;
        start += newleft - oldleft;
        end   += newleft - oldleft;

        this.setSelectionRange(start, end);
    });

    $('#checkout-apply_coupon_code').on('click', function() {
        var data = {
            code:        $('#checkout-coupon_code').val(),
            event_id:    $('#checkout-event_id').val(),
            schedule_id: $('[name$="[schedule_id]"]').val()
        };

        $.post('/frontend/courses/ajax_validate_coupon', data).done(function(data) {
            var $discount = $('#checkout-breakdown-discount');
            var $total = $('#checkout-breakdown-total');

            if (data.success) {
                if ($discount.html()) {
                   var existing_discount = $discount.data('amount') * (-1);
                   var final_discount =  existing_discount + data.discount;
                   $discount.data('amount', final_discount * (-1));
                   $discount.html(final_discount.toFixed(2));
                } else {
                    $discount.html(data.discount.toFixed(2));
                }
                $discount.parents('li').toggleClass('hidden', data.discount == 0);
                if (data.total) {
                    $total.html(data.total.toFixed(2));
                    $total.data('amend-total', data.total.toFixed(2));
                }
                check_cart(false, false);
            }

            addMessage(data.message, data.success ? 'success' : 'error');
        });
    });
});




function calculate_checkout_total()
{
	if ($('#booking-checkout-form, .header-cart-breakdown').length > 0)
	{
		var subtotal    = 0;
        if ($('#checkout-breakdown-subtotal').length > 0){
            subtotal = parseFloat($('#checkout-breakdown-subtotal').html().replace(/[^0-9\.]/g, ''));
        } else {
            if ($('.cart-subtotal').length > 0){
                subtotal = parseFloat($('.cart-subtotal').html().replace(/[^0-9\.]/g, ''));
            }
        }
		var zone_fee    = 0;
        if ($('#checkout-breakdown-zone_fee').length > 0 ){
            zone_fee    = parseFloat($('#checkout-breakdown-zone_fee').html().replace(/[^0-9\.]/g, ''));
        }
		var discount    = 0;
        if ($('#checkout-breakdown-discount').length > 0) {
            discount = parseFloat($('#checkout-breakdown-discount, .cart-discount').html().replace(/[^0-9\.]/g, ''));
        } else {
            if ($('.cart-discount').length > 0) {
                discount = parseFloat($('.cart-discount').html().replace(/[^0-9\.]/g, ''));
            }
        }
        if (isNaN(discount)) {
            discount = 0;
        }

        var amend_fee   = 0;
        if ($("#checkout-amendable_tier").length > 0){
            amend_fee = $("#checkout-amendable_tier")[0].checked ? parseFloat($("li.amend-fee").data('amount')) : 0;
        }
		var booking_fee = 0;
		$('.checkout-breakdown-booking_fee').each (function(){
			if (!$(this).parents("li").hasClass('hidden')) {
				booking_fee += parseFloat($(this).html().replace(/,/g, ''));
			}
		});

        var vat = $('#checkout-breakdown-vat:visible').data('amount') || 0;
        console.log(discount);
		var total = subtotal + zone_fee - discount + booking_fee + amend_fee + vat;
        console.log(total);

		$('#checkout-breakdown-total, .cart-total').html(total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
		$('#checkout-breakdown-total-field').val(total);

		if (total > 30 || total == 0) {
			$("[aria-controls=payment-tabs-mobile_carrier]").addClass("hidden");
			$("#payment-tabs-mobile_carrier").addClass("hidden");
		} else {
			$("[aria-controls=payment-tabs-mobile_carrier]").removeClass("hidden");
			$("#payment-tabs-mobile_carrier").removeClass("hidden");
		}

		// If the cart has been emptied, redirect the user
		var $items_in_cart = $('#checkout-sidebar-items').find('> div:not(#checkout-item-template) .checkout-item');
		if ($items_in_cart.length == 0 && false)
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
			//form.submit();
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

 $('.history-dropdown').click(function (ev) {
     // Don't run this code, if a focusable field within the "history-dropdown" is clicked.
     if ($(ev.target).hasClass('history-dropdown') || !$(ev.target).is(':input, button, a[href]')) {
         $(`.toggle-hide-bookings[data-booking_id=${$(this).data('booking_id')}]`).toggleClass('hidden');
         $(this).toggleClass('current-tab');
         if($(this).attr('data-contact_id') != undefined){
             $(document).find('.selected-booking-contact').removeClass('selected-booking-contact');
             $(this).addClass('selected-booking-contact');
         } else {
             return false;
         }
     }
});


$('.action-btn > a').click(function () {
	$(this).toggleClass('open');
	//$('.action-btn ul').slideUp();
	$(this).siblings('.action-btn ul').slideToggle(500);
	return false;
});

function update_new_search_results(callback)
{
	page   = $("#pagination-new .bootpag .selected-active-page a").html() || 1;
	sortBy = $('#availability-order_by').val() || 1;

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
    var cycle       = [];
    var is_fulltime  = [];
    var trainer_ids  = [];

    // page = page ? page : 1;

    $filters.find('[data-type="location"]:checked').each(function(index,value){location_ids.push($(value).data('id'));});
    $filters.find('[data-type="subject"]:checked').each(function(index,value){subject_ids.push($(value).data('id'));});
    $filters.find('[data-type="category"]:checked').each(function(index,value){category_ids.push($(value).data('id'));});
    $filters.find('[data-type="course"]:checked').each(function(index,value){course_ids.push($(value).data('id'));});
    $filters.find('[data-type="year"]:checked').each(function(index,value){year_ids.push($(value).data('id'));});
    $filters.find('[data-type="level"]:checked').each(function(index,value){level_ids.push($(value).data('id'));});
    $filters.find('[data-type="topic"]:checked').each(function(index,value){topic_ids.push($(value).data('id'));});
    $filters.find('[data-type="cycle"]:checked').each(function(index,value){cycle.push($(value).data('id'));});
    $filters.find('[data-type="is_fulltime"]:checked').each(function(index,value){is_fulltime.push($(value).data('id'));});
    $filters.find('[data-type="trainer"]:checked').each(function(index,value){trainer_ids.push($(value).data('id'));});

    var data    = {
        'location_ids' : location_ids,
        'trainer_ids'  : trainer_ids,
        'subject_ids'  : subject_ids,
        'category_ids' : category_ids,
        'course_ids'   : course_ids,
        'year_ids'     : year_ids,
        'level_ids'    : level_ids,
        'topic_ids'    : topic_ids,
        'cycle'        : cycle,
        'is_fulltime'  : is_fulltime,
        'keywords'     : keywords,
        'given_date'   : null,
        'page'         :page,
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


        $("#content_for_courses--mobile").remove();

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
        var contentmobile = $("#content_for_courses--mobile");
        contentmobile.remove();
        $("#content_for_courses--mobile-wrapper").append(contentmobile);
        $('#content_for_courses--mobile-wrapper .availability-course-details').on(':ib-expanded', availability_course_details_expanded);

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
        var fulltime_course_ids = [];

        var cart_inputs = $('.purchase-packages').find('input.cart_hidden_inputs');
        if(cart_inputs.length>0){
            cart_inputs.each(function () {
                if ($(this).data('event-id')!='') {
                    arr_of_event_ids.push($(this).data('event-id'));
                }else{
                    arr_of_schedule_ids.push($(this).data('schedule-id'));
				}

                if ($(this).data('booking-type') == 'fulltime') {
                    fulltime_course_ids.push($(this).data('course-id'));
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

        if (fulltime_course_ids.length > 0) {
            $('.custom-calendar').find('.cart.fulltime').not('.not-allowed').each(function () {
                var $this = $(this);

                if ($.inArray( $this.data('course-id'), fulltime_course_ids) != -1) {
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

		var discount_id = $this.parents(".select-package").attr("id");
		if (discount_id) {
			data.discount_id = discount_id;
		}

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

            $package_data.find('.toggleable_height').ib_toggleHeight();

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
	try {
        type = type || 'success';
        var message_area = $('#msg_area, #checkout-error_messages').first();

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
            '<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
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

        var first_date = $this.parents('td').data('date');

		var fullDate = new Date();
		var twoDigitMonth = fullDate.getMonth() >= 9 ? (fullDate.getMonth()+1) : '0' + fullDate.getMonth() + 1;
		var twoDigitDate = fullDate.getDate() + ""; if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
		var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;

		if(first_date != currentDate){
			fullDate = new Date(clean_date_string(first_date));
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

        var last_date = $this.parents('td').data('date');
		var fullDate = new Date(clean_date_string(last_date));
		fullDate.setDate(fullDate.getDate() + 1);
		//Thu May 19 2011 17:25:38 GMT+1000 {}
		//convert month to 2 digits
		var twoDigitMonth = ((fullDate.getMonth()) >= 9)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
		var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
		var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;
		//      format  2017-04-27

		if($this.hasClass('search_courses_right')) {
			var $accordion = $('#accordion, #available_results-filters');
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

		var data    = {
			'id'         : $this.data('id'),
			'given_date' : null
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
	$(document).on('click', '.search-calendar-course-image, .availability-date-read_more', function()
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

		var fullDate = new Date(clean_date_string(d));
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
						'<span class="icon"><span class="course-details-icon fa fa-book" aria-hidden="true"></span></span>	' +
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
    if ($("#prepay_container,#pay_as_you_go_container").length > 0 && !window.application_payment && $("#ticket_id").length == 0) {
        check_cart();
    }


	$(document).on('click', '.details-wrap .wishlist.add', function(){
		var button = this;
		var $details = $(button).closest('.details-wrap');
		var schedule_id = $details.data('schedule-id');
        var course_id = $details.data('course-id');
		var timeslot_id = $details.data('booking-type') =='One Timeslot' ? $details.data('event-id') : null;

		$.post(
			'/admin/contacts3/wishlist_add',
			{
                course_id: course_id,
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
	$(document).on('click', ".details-wrap button.cart, .availability-timeslot-per_schedule .availability-book, .availability-timeslot-per_timeslot .availability-book", function ()
	{
		var button          = this;
		var details_wrap    = $(this).closest('.details-wrap, .availability-timeslot, .availability-fulltime');
		var course_year_id  = details_wrap.data('year-id');
		var $student        = $('#student_id').find(':selected');
		var student_year_id = $student.data('year-id');
		var event_id        = details_wrap.data('event-id');
		var year_mismatch   = (student_year_id && course_year_id && student_year_id != course_year_id);
        var trial_timeslot_free_booking = $(this).data("trial_timeslot_free_booking") == 1;

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

    $(document).on("click", "button.cart.fulltime, .availability-fulltime .availability-book", function(){
        add_course_to_cart($(this).data("course_id"), $(this).data("paymentoption_id"));
    });

    function add_course_to_cart(course_id, paymentoption_id)
    {
        $('.purchase-packages').append(
            '<div class="booking-item-inputs">' +
            '  <input ' +
            '  class="cart_hidden_inputs" ' +
            '  type="hidden" ' +
            '  data-booking-type="fulltime" ' +
            '  data-course-id="' + course_id + '" ' +
            '  data-paymentoption_id="' + paymentoption_id + '" ' +
            '  />' +

            '  <input type="hidden" name="booking_courses[' + course_id + '][course_id]" value="' + course_id + '" />' +
            '  <input type="hidden" name="booking_courses[' + course_id + '][paymentoption_id]" value="' + paymentoption_id + '" />' +
            '</div>'
        );

        check_cart(true);
        $("#booking-cart-empty").addClass("hidden");
    }

	function add_to_cart(button)
	{
		var details_wrap      = $(button).closest('.details-wrap, .availability-timeslot, .availability-fulltime');
		var course_title      = details_wrap.data('course-title');
		var booking_type      = details_wrap.data('booking-type');
		var schedule_id       = details_wrap.data('schedule-id');
		var user_permission   = details_wrap.data('logged-in-user-permission');
		var date_formatted    = details_wrap.data('date-formatted');
		var fee_per           = details_wrap.data('fee-per');
		var when_to_pay       = $.trim(details_wrap.find('span.payfor').text());
		var amendable         = details_wrap.data('amendable');
		var schedule_event_id = details_wrap.data('event-id');
        var course_id         = details_wrap.data('course-id');
        var number_of_delegates = details_wrap.find('.course-offer-delegates').val();
        var trial_timeslot_free_booking = $(button).data("trial_timeslot_free_booking") == 1;
        if (!course_id) {
            course_id = $(button).data("course_id");
        }

		// take just amount
		var fee = $.trim(details_wrap.find('span.price').text());
		var fee_res = fee.split(" ");
		fee = fee_res[1];

		// whole schedule
		if (booking_type != "One Timeslot" && !trial_timeslot_free_booking)
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
                    if (response.events)
					for (var i = 0 ; i < response.events.length ; ++i)
					{
                        var event_date = new Date(response.events[i].datetime_start.replace(/-/g, "/"));
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
						inputs_only:     true,
                        course_id:       course_id,
                        show_message:    true,
                        number_of_delegates: number_of_delegates
					});

					$('.details-wrap[data-schedule-id='+schedule_id+']').addClass('booked').each(function () {
						$(this).find('li .cart:not(.trial)').replaceWith('<a class="remove-booking" data-schedule-id="'+schedule_id+'" href="#">Remove Booking</a>');
                        $(this).find('li .cart.trial').addClass("hidden");
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
				inputs_only:     true,
                course_id:       course_id,
                show_message:    true,
                number_of_delegates: number_of_delegates
			});

			$('.details-wrap[data-event-id='+schedule_event_id+']').addClass('booked').each(function () {
                if (trial_timeslot_free_booking) {
                    $(this).find('li .cart.trial').replaceWith('<a class="remove-booking trial" data-schedule-id="' + schedule_id + '" data-timeslot_id="' + schedule_event_id + '" href="#">Remove Booking</a>');
                    $(this).find('li .cart:not(.trial)').addClass("hidden");
                } else {
                    $(this).find('li .cart').replaceWith('<a class="remove-booking" data-event-id="' + schedule_event_id + '" href="#">Remove Booking</a>');
                }
			});

			check_if_schedule_event_is_booked();
			check_cart();
		}
	}

	// If the user chooses to continue with a booking, despite its time conflicting with another booking
	$(document).on('click', '#bookings-conflict-continue', function()
	{
		var event_id = $(this).attr('data-event-id');
		var $event   = $('.details-wrap[data-event-id="'+event_id+'"], .availability-timeslot[data-event-id="'+event_id+'"]');

		// Flag that the mismatch warning is to be ignored and click the button again.
		$event.data('ignore_time_conflict', 1).attr('data-ignore_time_conflict', 1);
		$event.find('.cart, .availability-book').trigger('click');

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
        var trial = $(this).hasClass("trial");
		if(details_wrap.data('booking-type') == "Whole Schedule"){

			$('.details-wrap').each(function () {
				var $this = $(this);
                if (trial) {
                    if ($this.data('schedule-id') == schedule_id) {
                        $this.find('li .cart.hidden').removeClass("hidden");
                        $this.removeClass('booked')
                            .find('li .remove-booking.trial').replaceWith('<button type="button" class="button button--book cart trial" data-trial_timeslot_free_booking="1">Trial booking</button>');
                    }
                } else {
                    if ($this.data('schedule-id') == schedule_id) {
                        $this.removeClass('booked')
                            .find('li .remove-booking').replaceWith('<button type="button" class="button button--book cart">' + $this.data("logged-in-user-permission") + '</button>');
                        $this.find('li .cart.trial').removeClass("hidden");
                    }
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
	$(document).on('click', '#checkout-sidebar-items .checkout-item-remove, .availability-timeslot-per_schedule .availability-unbook, .availability-timeslot-per_timeslot .availability-unbook, .availability-fulltime .availability-unbook', function(e)
	{
		e.preventDefault();

		var $li       = $(this).parents('.checkout-item, .availability-timeslot, .availability-course-details');
        var course_id = $li.data('course-id');
        var event_ids = $li.data('event-id');
        var schedule_id = $li.data('schedule-id');
        if (!course_id) {
            if (event_ids != "all") {
                event_ids = Array.isArray(event_ids) ? event_ids : JSON.parse(event_ids);

                if (!Array.isArray(event_ids) && event_ids != null) {
                    event_ids = [event_ids];
                }
            }
        }

        // Remove from the sidebar
        if (event_ids == "all") {
            remove_item_from_cart_with_schedule(schedule_id);
        } else {
            remove_item_from_cart(event_ids, course_id);
        }

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
    console.log(args);
    var deposit          = (typeof args.deposit       != 'undefined') ? args.deposit       : '';
	var when_to_pay      = (typeof args.when_to_pay       != 'undefined') ? args.when_to_pay       : '';
	var course_title     = (typeof args.course_title      != 'undefined') ? args.course_title      : '';
	var date_formatted   = (typeof args.date_formatted    != 'undefined') ? args.date_formatted    : '';
	var fee              = (typeof args.fee               != 'undefined') ? args.fee               : '';
	var per_class_fee    = (typeof args.per_class_fee     != 'undefined') ? args.per_class_fee     : '';
	var per_day_fee      = (typeof args.per_day_fee       != 'undefined') ? args.per_day_fee       : '';
	var event_ids        = (typeof args.event_ids         != 'undefined') ? args.event_ids         : '';
	var schedule_id      = (typeof args.schedule_id       != 'undefined') ? args.schedule_id       : '';
    var course_id        = (typeof args.course_id         != 'undefined') ? args.course_id         : '';
	var booking_type     = (typeof args.booking_type      != 'undefined') ? args.booking_type      : '';
	var user_permission  = (typeof args.user_permission   != 'undefined') ? args.user_permission   : '';
	var fee_per          = (typeof args.fee_per           != 'undefined') ? args.fee_per           : '';
	var amendable        = (typeof args.amendable         != 'undefined') ? args.amendable         : '';
	var count            = (typeof args['count']          != 'undefined' && $.isNumeric(args['count'])) ? args['count'] : 1;
    var show_message     = (typeof args.show_message      != 'undefined') ? args.show_message      : true;
    var number_of_delegates = (typeof args.number_of_delegates != 'undefined') ? args.number_of_delegates : 1;

	fee = parseFloat(fee);

    var count_text;
    if (booking_type == 'Subscription') {
        count_text = "Subscription";
    } else if (number_of_delegates > 1 && count > 1) {
        count_text = number_of_delegates+' delegates<br />'+count+' classes';
    }
    else if (number_of_delegates > 1) {
        count_text = number_of_delegates+' delegates';
    } else {
        count_text = (count == 1) ? '1 class' : count+ ' classes';
    }

	var purchase_packages   = $('.purchase-packages');
	var $existing_cart_item = [];

    if (course_id && !schedule_id) {
        event_ids = [];
        $existing_cart_item = $('.checkout-item[data-course-id="' + course_id + '"]');
    } else {
        event_ids = (Array.isArray(event_ids)) ? event_ids : [event_ids];
        // If there is an item in the cart menu, from the same schedule, on the same day,
        // that item will be updated, rather than an another item added
        if (event_ids.length) {
            $existing_cart_item = $('.checkout-item[data-schedule-id="'+schedule_id+'"][data-event-id="'+(event_ids.length > 1 ? '[' + event_ids + ']' : event_ids[0])+'"]');
        }
    }

    // Schedule IDs need to be noted, in case there are no timeslots.
    purchase_packages.append('<input type="hidden" name="schedule_ids[]" value="' + schedule_id + '" />');

    if (course_id && !schedule_id) {
        purchase_packages.append(
            '<div class="booking-item-inputs">' +
            '  <input ' +
            '  class="cart_hidden_inputs" ' +
            '  type="hidden" ' +
            '  data-booking-type="fulltime" ' +
            '  data-fee="'+fee+'"' +
            '  data-course-id="' + course_id + '"' +
            '  />' +

            '  <input type="hidden" name="courses[' + course_id + '][course_id]" value="' + course_id + '" />' +
            '  <input type="hidden" name="courses[' + course_id + '][paymentoption_id]" value="" />' +
            '</div>'
        );
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
                '  data-number_of_delegates="'+number_of_delegates+'"' +
				'  />' +

				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][schedule_id]"     value="' + schedule_id     + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][course_title]"    value="' + course_title    + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][date_formatted]"  value="' + date_formatted  + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][booking_type]"    value="' + booking_type    + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][when_to_pay]"     value="' + when_to_pay     + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][fee]"             value="' + fee             + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][deposit]"         value="' + deposit         + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][count]"           value="' + count           + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][user_permission]" value="' + user_permission + '" />' +
				'  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][amendable]"       value="' + amendable       + '" />' +
                '  <input type="hidden" name="booking_items[' + schedule_id + '][' + event_id + '][number_of_delegates]" value="' + number_of_delegates + '" />' +
				'</div>'
		);
	}

	if (!args.inputs_only) {
		var is_payg = (when_to_pay.toLowerCase() == 'pay as you go' || when_to_pay.toLowerCase() == 'payg');

		if ($existing_cart_item.length) {
			// Update existing cart menu item
			var new_count = count;
			var new_fee = ((fee_per == 'Timeslot') ? (fee * new_count) : fee);
			var new_fee_text = (is_payg && per_class_fee != '')
				? parseFloat(per_class_fee).toFixed(2) + ' <small>per&nbsp;class</small>'
				: new_fee.toFixed(2);
			if (per_day_fee) {
				new_fee_text = parseFloat(per_day_fee).toFixed(2) + '<br />daily'
			}
            if (fee_per == "Month") {
                new_fee_text = parseFloat(fee).toFixed(2) + '<br />monthly';
                if (deposit) {
                    deposit = deposit.replace('.00', '');
                    new_fee_text += '<small>' + $('#checkout-cart-deposit_text').val().replace('$1', '&euro;' + deposit) + '</small>';
                }
            }
			count_text = (new_count == 1) ? '1 class' : new_count + ' classes';
            if (booking_type == 'Subscription') {
                count_text = "Subscription";
            }
			var existing_event_ids = $existing_cart_item.data('event-id');
			existing_event_ids = (Array.isArray(existing_event_ids)) ? existing_event_ids : [existing_event_ids];

			var new_event_ids = existing_event_ids.concat(event_ids);

			$existing_cart_item.data({'event-id': new_event_ids}).attr('data-event-id', JSON.stringify(new_event_ids));
			$existing_cart_item.data('count', new_count).attr('data-count', new_count);
			$existing_cart_item.find('.checkout-item-count').text(count_text);
			$existing_cart_item.find('.checkout-item-fee').html(new_fee_text);
		}
		else {
			// Add a new cart menu item
			var $clone = $('#checkout-item-template').find('li').clone();
			var event_data = (event_ids.length == 1) ? event_ids[0] : JSON.stringify(event_ids);

			$clone
				.data({
					'schedule-id': schedule_id,
                    'course-id' : course_id,
					'booking-type': booking_type,
					'event-id': event_data,
					'count': count,
					'date': date_formatted,
					'logged-in-user-permission': user_permission
				})
				.attr({
					'data-schedule-id': schedule_id,
                    'data-course-id' : course_id,
					'data-booking-type': booking_type,
					'data-event-id': event_data,
					'data-count': count,
					'data-date': date_formatted,
					'data-logged-in-user-permission': user_permission
				});

			var fee_text = (is_payg && per_class_fee != '')
				? parseFloat(per_class_fee * number_of_delegates).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' <small>per&nbsp;class</small>'
				: parseFloat(fee * number_of_delegates).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			if (per_day_fee) {
				fee_text = parseFloat(per_day_fee).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '<br />daily'
			}
            if (fee_per == "Month") {``
                fee_text = parseFloat(fee).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '<br />monthly';
                if (deposit) {
                    deposit = deposit.replace('.00', '');
                    fee_text += '<small>' + $('#checkout-cart-deposit_text').val().replace('$1', '&euro;' + deposit) + ' </small>';
                }
            }


            $clone.find('.checkout-item-title').text(course_title);
            if (schedule_id) {

                if (number_of_delegates > 1 && count > 1) {
                    count_text = number_of_delegates+' delegates<br />';
                } else if (number_of_delegates > 1) {
                    count_text = number_of_delegates+' delegates';
                } else if($('#org-rep-delegate-confirmation')!= undefined) {
                    count_text = '1 delegate';
                } else {
                    count_text = '';
                }
                if (booking_type == 'Subscription') {
                    count_text = "Subscription";
                }

                $clone.find('.checkout-item-count').html(count_text);
                if (args.timeslots) {
                    time_text = '';
                    for (timeslot_id in args.timeslots) {
                        var timeslot_id, timeslot_date;
                        for (timeslot_id in args.timeslots) {
                            timeslot_start_date = new Date(clean_date_string(args.timeslots[timeslot_id].datetime_start));
                            timeslot_start_string = timeslot_start_date.dateFormat('H:i');
                            timeslot_end_date = new Date(clean_date_string(args.timeslots[timeslot_id].datetime_end));
                            timeslot_end_string = timeslot_end_date.dateFormat('H:i');
                            time_text = ' | ' + timeslot_start_string + ' - ' + timeslot_end_string;
                        }
                    }
                    console.log(time_text);
                    $clone.find('.checkout-item-time').html(time_text);
                }
                if (args.display_timeslots_in_cart && args.timeslots) {
                    const ts_count = Object.keys(args.timeslots).length;

                    $clone.find('.checkout-item-duration').text(ts_count + (ts_count == 1 ? ' session' : ' sessions'));

                    var timeslot_id, timeslot_date;

                    var timeslots_html = '<ul class="list-unstyled checkout-item-timeslots">';

                    for (timeslot_id in args.timeslots) {
                        timeslot_date = new Date(clean_date_string(args.timeslots[timeslot_id].datetime_start));

                        timeslots_html += '<li class="mb-1 p-0">'+(timeslot_date.dateFormat('j M Y'))+'</li>';
                    }
                    timeslots_html += '</div>';
                    $clone.append(timeslots_html);


                    // Not an ideal solution, but given time constraints
                    var $count = $clone.find('.checkout-item-count');
                    $count.parent().attr('class', $count.parent().attr('class').replace('col-xs-7', 'col-xs-6'));

                    var $expand = $clone.find('.checkout-item-timeslots-expand').removeClass('hidden');
                    $expand.parents('.row').click(function() {
                        $(this).find('button .fa').toggleClass('expanded-invert');
                        $(this).parents('.checkout-item').find('.checkout-item-timeslots').toggleClass('hidden');
                    });

                } else {
                    $clone.find('.checkout-item-date').html(date_formatted);
                }

            } else {
                $clone.find('.checkout-item-date').html("Full time");
                $clone.find('.checkout-item-count').html("");
            }
			$clone.find('.checkout-item-fee').html(fee_text);
			$clone.find('.checkout-item-input').remove();

			if (when_to_pay == 'Pre-Pay') {
				$('.prepay_container').find('ul').append($clone);
				$('.prepay_container').removeClass("hidden");
			} else if (is_payg) {
				$('.pay_as_you_go_container').find('ul').append($clone);
				$('.pay_as_you_go_container').removeClass("hidden");
			}
		}
	}

    if (show_message) {
        var $icon = $('#cart-alert-icon-template');
        $icon.find('.alert-icon-amount').attr('data-amount', '+1');

        addMessage(course_title+' added to cart. '+$icon.html(), 'add');
    }

	$("#booking-cart-empty").addClass("hidden");
}

function refresh_cart_messages()
{
    var schedule_ids = [];
	var course_ids = [];
    $('.checkout-item').each(function() {
		var schedule_id = this.getAttribute('data-schedule-id');
		if (schedule_id) {
			if (schedule_ids.indexOf(schedule_id) == -1) {
				schedule_ids.push(schedule_id);
			}
		} else {
			var course_id = this.getAttribute('data-course-id');
			if (course_id) {
				if (course_ids.indexOf(course_id) == -1) {
					course_ids.push(course_id);
				}
			}
		}
    });

    $.ajax({
        url  : '/frontend/courses/ajax_get_cart_messages',
        data : {schedule_ids: schedule_ids, course_ids: course_ids}
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

function remove_item_from_cart(schedule_event_id, course_id)
{
	var $items = $('#checkout-sidebar-items');

    if (course_id) {
        var $item_to_remove = $items.find("[data-course-id='" + course_id + "']");
        var course_title = $item_to_remove.find('.checkout-item-title').text();
        var $icon = $('#cart-alert-icon-template');
        addMessage(course_title+' removed from cart '+$icon.html(), 'remove');
        $item_to_remove.remove();
        $('.purchase-packages').find("[data-course-id='" + course_id + "']").parents('.booking-item-inputs').remove();
        $('.custom-calendar').find("[data-course-id='" + course_id + "']").removeClass('already_booked');
    } else {
		var $item_to_remove = $items.find("[data-event-id='" + schedule_event_id + "'], [data-event-id='" + JSON.stringify(schedule_event_id) + "']");
	    var schedule_id = $item_to_remove.data('schedule-id');
	    var course_title = $item_to_remove.find('.checkout-item-title').text();
	    var $icon = $('#cart-alert-icon-template');

	    $icon.find('.alert-icon-amount').attr('data-amount', '-1');
	    addMessage(course_title+' removed from cart '+$icon.html(), 'remove');
	    $item_to_remove.remove();
        // If the cart item has a single event ID
        $items.find("[data-event-id='" + schedule_event_id + "']").remove();
        $items.find("[data-event-id='" + JSON.stringify(schedule_event_id) + "']").remove();

        // If the cart item has multiple event IDs, as a JSON array
        $items.find('[data-event-id]').each(function () {
            var event_ids = $(this).data('event-id');

            if (Array.isArray(event_ids)) {
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

        var schedule_with_events = $('.purchase-packages').find("input[data-event-id][data-event-id!='']");

        schedule_with_events.each (function () {
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

function check_cart(override, hasValuesOverride) {
	var hasValues=false;
	var schedules_only=$('.purchase-packages').find("input[data-event-id='']");
	console.log(schedules_only);
	var schedule_events={};
	schedules_only.each(function () {
		var ent = $(this).attr('data-schedule-id');
        if (ent) {
            if (!schedule_events[ent]) {

                schedule_events[ent] = {};
            }

            schedule_events[ent] = {'isScheduleOnly': true};

            hasValues = true;

        }
	});
    if(hasValuesOverride !== undefined) {
        hasValues = hasValuesOverride;
    }
	var schedule_with_events=$('.purchase-packages').find("input[data-event-id][data-event-id!='']");


	schedule_with_events.each(function () {
		var ent = $(this).attr('data-schedule-id');
        if (ent) {
            if (!schedule_events[ent]) {

                schedule_events[ent] = {};
            }

            schedule_events[ent][$(this).attr('data-event-id')] =
            {
                attending: 1,
                note: ' ',
                fee: $(this).attr('data-fee'),
                prepay: ($(this).attr('data-when-to-pay') == 'Pre-Pay'),
                number_of_delegates: $(this).attr('data-number_of_delegates')
            };
            hasValues = true;
        }
	});
    if(hasValuesOverride !== undefined) {
        hasValues = hasValuesOverride;
    }
    var courses = [];
    $('.purchase-packages').find("input[data-course-id]").each(function(){
        courses.push({
            course_id: $(this).data("course-id"),
            paymentoption_id: $(this).data("paymentoption_id") ? $(this).data("paymentoption_id") : 0
        })
    });


	$("#cart_total_container").find(".discountItemPlaceholder").remove();
    var student_id = $("#checkout-student-tabs-contents .tab-pane.active [name=student_id]").val();
    if (!student_id) {
        $("#checkout-student-tabs-contents .tab-pane:first [name=student_id]")
    }
    if (!student_id) {
        student_id = $("#student_id").val();
    }
	var url = '/frontend/bookings/get_order_table_html';
	if(hasValues || override){
		url = '/frontend/bookings/add_to_cart';
	}

    var new_student_params = {
        year: $("#checkout-student-tabs-contents .tab-pane.active").length ?
            $("#checkout-student-tabs-contents .tab-pane.active [name=student_year_id]").val() :
            $("#checkout-student-tabs-contents .tab-pane:first [name=student_year_id]").val()
    };

	$.ajax({
		type    : "POST",
		url     : url,
		data    : {booking:schedule_events, override: override, student_id: student_id, courses: courses, new_student_params: new_student_params},
		success : function (res) {
            var has_course_selector = $('#checkout-course').length;

            if (res.length <= 1 && !has_course_selector) { // no item added and can't add item from checkout
                if (window.location.href.indexOf("checkout") != -1) {
                    cms_ns.modified = false;
                    cms_ns.modified_inputs = [];
                    window.location.href = ($('#booking-checkout-form').data('checkout-type') == 'itt') ? '/course-list' : "/available-results";
                }
            }
			if (override == true && window.location.href.indexOf("checkout") != -1) {
                cms_ns.modified = false;
                cms_ns.modified_inputs = [];
				window.location.href = '/checkout';
			}
			if (res == null) {
				return;
			}

			var total=0;
            var subtotal=0;
            var number_of_items = 0;

			var discounts = {};
            var discount = 0;

            if ($('#booking-cart-empty').is(':visible')) {
                $("#continue-button").prop('disabled', false).css({ opacity: 1 });
            }

			$(".booking-item-inputs").remove();
			$(".prepay_container .checkout-item, .pay_as_you_go_container .checkout-item").remove();

            $(".discountItemPlaceholder").remove();

			var offers_to_display = {};
            $(".cart-payg_fee").addClass("hidden");
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
                    if (!obj.prepay) {
                        $(".cart-payg_fee").removeClass("hidden");
                    }

                    var date_formatted = obj.details.start_date;
                    // Get the first date the user is booking into
                    if (typeof obj.timeslot_details === "object" && Object.keys(obj.timeslot_details).length > 0
                        && obj.periods_attending && obj.periods_attending.length) {
                        date_formatted = new Date(clean_date_string(obj.timeslot_details[obj.periods_attending[0]].datetime_start)).dateFormat('d/M/Y');
                    } else if (!isNaN(Date.parse(date_formatted)))
                    {   // check if date is a valid date, if it is, assign it proper format
                        date_formatted = new Date(date_formatted).dateFormat('d/M/Y');
                    }
                    console.log(obj);
					add_item_to_cart({
                        deposit: obj.trial_timeslot_free_booking != 1 ? obj.details.deposit : 0,
						when_to_pay: obj.prepay || obj.type == 'course' ? 'Pre-Pay' : 'Pay as you go',
						course_title: obj.details.title,
						date_formatted: date_formatted,
						fee: obj.fee,
						per_class_fee:  obj.type == 'schedule' && first_timeslot ? first_timeslot.fee_amount : null,
						per_day_fee:  obj.details.fee_per == 'Day' ? obj.details.fee_amount : null,
						event_ids: obj.periods_attending,
						schedule_id: obj.type == 'schedule' ? obj.id : null,
                        course_id: obj.type == 'course' ? obj.id : null,
						booking_type: obj.details.booking_type,
						user_permission: $(".details-wrap[data-schedule-id=" + obj.id + "]").data("logged-in-user-permission"),
						count: obj.type == 'schedule' ? obj.periods_attending.length : null,
						fee_per: obj.fee_per,
						amendable: obj.details.amendable,
						show_message: false,
						inputs_only: false,
                        number_of_delegates: obj.number_of_delegates,
                        display_timeslots_in_cart: obj.details.display_timeslots_in_cart == 1,
                        timeslots: obj.timeslot_details
					});

                    number_of_items++;
				}

				if(obj.type=='schedule') {
					if (obj.prepay) {
                        total    += obj.total;
						subtotal += obj.fee;
					} else {
                        if (obj.details && obj.details.deposit && obj.trial_timeslot_free_booking != 1) {
                            total    += parseFloat(obj.details.deposit);
                            subtotal += parseFloat(obj.details.deposit);
                        }
                    }

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
								if (obj.prepay == 1) {
									discount += disc.amount;
								}

                                if (discounts[disc.id].template == null) {
                                    discounts[disc.id].template = $discountItemTemplate.clone();
                                    discounts[disc.id].template.css("display", "");
                                    discounts[disc.id].template.removeClass("template");

                                    discounts[disc.id].template.find(".title").html(disc.title);
                                    discounts[disc.id].template.attr("data-schedule-id", obj.id);
                                    discounts[disc.id].template.attr("data-discount-id", disc.id);
                                    discounts[disc.id].template.attr("data-discount", disc.amount);
                                    discounts[disc.id].template.find(".amount").html(Number.parseFloat(disc.amount).toFixed(2));
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
										if (obj.prepay == 1) {
											$(".prepay_container > ul").append(discounts[disc.id].template);
										} else {
											$(".pay_as_you_go_container > ul").append(discounts[disc.id].template);
										}
                                    }
                                }

                                discounts[disc.id].template.find(".amount").html(Number.parseFloat(discounts[disc.id].amount).toFixed(2));
								++discount_index;
							}
						})
					}
				}
                //total = subtotal;
				if(obj.type=='subtotal') {
					if (obj.booking_fees > 0) {
						total += obj.booking_fees;
					}
                    total += obj.cc_fee;
                    var discounts_html = '';
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
                                discount += disc.amount;

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

								++discount_index;
                                discounts[disc.id].template.find(".amount").html(Number.parseFloat(discounts[disc.id].amount).toFixed(2));

                                discounts_html += discounts[disc.id].template.html();
							}
						});
					}

                    $('#cart-discounts_container').html(discounts_html);
				}
			});

			subtotal = subtotal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

            if (res.length <= 1) { // no item added
                subtotal = 0;
                total = 0;
                discount = 0;

                $(".cart-payg_fee, .cart-cc_fee, .cart-sms_fee").addClass("hidden");
            } else {
                var amount;
                if ($("[name=payment_method]").val() == "cc") {
                    amount = $('.cart-cc_fee[data-amount]').data('amount');
                    $(".cart-cc_fee").toggleClass("hidden", (amount == 0));
                }
                if ($("[name=payment_method]").val() == "sms") {
                    amount = $('.cart-sms_fee[data-amount]').data('amount');
                    $(".cart-sms_fee").toggleClass("hidden", (amount == 0));
                }
            }
			// Update totals on search results page
			$(".cart-subtotal").text(subtotal);
            $(".cart-discount").text(discount);
			$(".cart_total_amount").text("€" + total);
            $(".cart-total, #checkout-breakdown-total").text(total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

            $('.cart-discount-total-wrapper').toggleClass('hidden', (discount == 0));

            refresh_cart_messages();


            check_if_schedule_event_is_booked();

            $('.header-cart-amount').attr('data-amount', number_of_items).data('amount', number_of_items);

            var prepay_container = $('.prepay_container');
            var pay_as_you_go_container = $('.pay_as_you_go_container');

            var cart_prepay_bookings = prepay_container.find('.checkout-item');
            var cart_pay_as_you_go_bookings = pay_as_you_go_container.find('.checkout-item');
            var has_checkout_items = $('.checkout-item').length - $('#checkout-item-template').find('.checkout-item').length;

            var course_selected = $('#checkout-course').val();

            // If selecting a course from a dropdown instead, we don't want "no booking selected" notification to show
            if (has_checkout_items || course_selected != null) {
                $('#booking-cart-empty').addClass('hidden');
                if (cart_prepay_bookings.length > 0) {
                    prepay_container.removeClass('hidden');
                } else {
                    prepay_container.addClass('hidden');
                }
                if (cart_pay_as_you_go_bookings.length > 0) {
                    pay_as_you_go_container.removeClass('hidden');
                } else {
                    pay_as_you_go_container.addClass('hidden');
                }
                var cart_total_amount = $(".cart_amount");

                //  var sum = 0;
//         $('.cart_amount').each(function(){
//             sum += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
//         });
                //   $(".cart_total_amount").text('€'+sum);
            } else {
                prepay_container.addClass('hidden');
                pay_as_you_go_container.addClass('hidden');
                $(".cart_total_amount").text('€' + 0);
                $('#booking-cart-empty').removeClass('hidden');
            }
		}
	});
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
            var footer = document.getElementsByClassName('page-footer')[0] ? document.getElementsByClassName('page-footer')[0] : document.getElementsByClassName('footer')[0];

            var item_head_coordinates = document.getElementsByClassName('item-summary-head')[0].getBoundingClientRect();
			var footer_coordinates    = footer.getBoundingClientRect();
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
});
$(document).on('mousedown', '#show_profile_pass' , function() {
    $(this.getAttribute('data-target')).attr('type', 'text');
});


// ////////////////////////


$(document).ready(function () {
    var form_clicked = false;

	if (typeof $.datepicker != 'undefined')
	{
		$(function () {
			$("#student_date_of_birth").datepicker({
				changeMonth: true,
				changeYear: true,
				minDate: "-100y",
				maxDate: "-16y",
				dateFormat: 'dd-mm-yy',
				yearRange: '1930:2012'
			});
		});
	}

    $('#show_filters').on('click', function()
    {
        var search_block = $('.search-block');
        (search_block.hasClass('filters_shown')) ? search_block.removeClass('filters_shown') : search_block.addClass('filters_shown');
    });
    $('.template-default').find('.menu-expand, .submenu-expand').on('click', function(ev)
    {
        ev.preventDefault();
        var menu = $(this).find('\+ ul');
        menu.is(':visible') ? menu.hide() : menu.show();
    });
    $("#pay_now_button").click(function()
    {
        if(form_clicked)
        {
            return false;
        }
        var $button = $(this);
        $button.prop('disabled', true);
        $button.after('<span id="pay_now_button_wait">Please wait...</span>');
        form_clicked = true;
        try
        {
            var checkout_data = checkout_data || {};
            checkout_data.payment_ref       = document.getElementById("payment_ref") ? document.getElementById("payment_ref").value : "";
			checkout_data.plan_payment_id = $("[name=plan_payment_id]").val();
			checkout_data.contact_id = $("[name=contact_id]").val();
            checkout_data.transaction_id = $("[name=transaction_id]").val();
            checkout_data.payment_total     = document.getElementById("payment_total").value;
            checkout_data.comments          = document.getElementById("comments").value;

            // Name and address
            checkout_data.name         = document.getElementById("name").value;
            checkout_data.phone        = document.getElementById("phone").value;
            checkout_data.email        = document.getElementById("email").value;
            checkout_data.course_name  = document.getElementById("course_name")  ? document.getElementById("course_name").value  : '';
            checkout_data.student_name = document.getElementById("student_name") ? document.getElementById("student_name").value : '';
            checkout_data.location     = document.getElementById("location")     ? document.getElementById("location").value     : '';

            // CAPTCHA
            checkout_data.recaptcha_response_field  = document.getElementById("recaptcha_response_field")        ? document.getElementById("recaptcha_response_field").value        : '';
            checkout_data.recaptcha_challenge_field = document.getElementById("recaptcha_challenge_field")       ? document.getElementById("recaptcha_challenge_field").value       : '';
            checkout_data['g-recaptcha-response']   = document.getElementById('payment')['g-recaptcha-response'] ? document.getElementById('payment')['g-recaptcha-response'].value : '';

            // Credit card
            checkout_data.ccName  = document.getElementById("ccName").value;
            checkout_data.ccType  = document.getElementById("ccType").options[document.getElementById("ccType").selectedIndex].value;
            checkout_data.ccNum   = document.querySelector("#ccNum, #checkout-ccNum").value.replace(/[\s\-]/g, '');
            checkout_data.ccv     = document.getElementById("ccv").value;
            checkout_data.ccExpMM = document.getElementById("ccExpMM").options[document.getElementById("ccExpMM").selectedIndex].value;
            checkout_data.ccExpYY = document.getElementById("ccExpYY").options[document.getElementById("ccExpYY").selectedIndex].value;
            checkout_data = JSON.stringify(checkout_data);

            var submit_status = $("#payment").validationEngine('validate');

            if(submit_status)
            {
                window.disableScreenDiv.style.visibility = "visible";
                $.post('/frontend/payments/payment_processor_ib_pay',{checkout:checkout_data},function(data){

                    if (data.status == 'success')
                    {
                        // Stop the form-modified warning appearing before the page is changed
                        cms_ns.modified = false;
                        // Redirect to the thank you page
                        location.href = data.redirect;
                    }
                    else
                    {
                        $button.prop('disabled', false);
                        $('#pay_now_button_wait').remove();

                        window.disableScreenDiv.style.visibility = "hidden";
                        form_clicked = false;
                        checkout_data = '';
                        $("#error_message_area").html('Error: ' +data.message);
                    }
                },'json').fail(function(data){
                    $button.prop('disabled', false);
                    $('#pay_now_button_wait').remove();


                    form_clicked = false;
                    checkout_data = '';
                    $("#error_message_area").html('Error: Network error, please check your internet connection');
                });
            }
            else
            {
                form_clicked = false;
                $button.prop('disabled', false);
                $('#pay_now_button_wait').remove();
            }
            checkout_data = '';
        }
        catch(error)
        {
            form_clicked = false;
            $button.prop('disabled', false);
            $('#pay_now_button_wait').remove();
        }
    });

	$('.slider-pagination > li').on('click', function()
	{
		var slide = $(this).data('slide');
		var $slider = $(this).parents('.slider');

		$(this).parents('.slider-pagination').find('.active').removeClass('active');
		$(this).addClass('active');

		$slider.find('.slider-slide.active').removeClass('active');
		$slider.find('.slider-slide[data-slide="'+slide+'"]').addClass('active');
	});

	$('.slider-nav--prev').on('click', function()
	{

	});


    $(".course-detail").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data("id");
        var title = $(this).data("title");
        var schedule = $('#start_date_'+id).val();
        var selected_schedule = (schedule == '') ? '' : '&schedule_id='+schedule ;
        var href = "/course-detail/" + title + ".html/?id=" + id+selected_schedule;
        window.location = "http://" + window.location.host + href;
        return false;
    });
    $('.course-book, .course-enquire').click(function (ev) {
        ev.preventDefault();

        var course_id = $(this).data('id');
        var event_id = $(this).parents('.contentBlock').find('.start_date :selected').data('event_id');
        var schedule_id = $(this).data("schedule");
        var valid = $('#select_schedule' + course_id).validationEngine('validate');
        if (valid) {
            var start_date_id = $('#start_date_' + course_id).val();
            var location_id = $('#location_' + course_id).val();
            var title = $(this).data("title");

            var href = "/booking-form/"+title+".html/?id="+schedule_id+'&eid='+event_id;
            window.location = "http://"+window.location.host+href;
        } else {
            setTimeout('removeBubbles()', 5000);
        }
        return false;
    });

    $("#enquire-course, #book-course").click(function (ev) {
        ev.preventDefault();

        var $form = $('#selectcform');

        var valid = $form.validationEngine('validate');

        if (valid)
        {
            var schedule_selector = $("#schedule_selector");
            var id = schedule_selector.val();
            var event_id = schedule_selector.find(':selected').data('event_id');
            var num_delegates = $("#num_delegates").val();
            var title = $(this).data("title");
            var checkout_types = ['itt', 'ibec'];
            var action = checkout_types.indexOf($form.data('checkout-type')) != -1 ?
            '/frontend/bookings/add_to_cart?add_to_cart_schedule_id=' + id + '&add_to_cart_timeslot_id='+ event_id + '&num_delegates=' + num_delegates + '&reset_cart=1'
            :
            $form.data("action") + "/"+title+".html/?id="+id+'&eid='+event_id;
            var href = action;
            window.location = "http://"+window.location.host+href;
            return false;

        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });
    $("#schedule_selector").on("change", function () {
        var $selected_option = $(this).find(':selected');
        $("#selectc").html($selected_option.val() ? $selected_option.html() : '');

        var schedule_id = $(this).val();

        var data = {
            schedule_id: schedule_id,
            after: new Date().dateFormat('Y-m-d')
        };

        if ($selected_option.data('event_id') && $selected_option.data('event_id') != 'all') {
            data.event_id = $selected_option.data('event_id');
        }

        $.ajax({
            url:'/frontend/courses/ajax_get_timeslots',
            data: data
        }).done(function(data) {
            let timeslots_html = '', location = '', show_section = false;
            const timeslots = data.timeslots || [];
            const $dates = $('#course-details-timeslots-dates');
            const date_format = $dates.data('date_format') || 'l j F Y H:i';

            if (timeslots[0] && timeslots[0].display_timeslots_in_cart) {
                var i, start_time, end_time;
                show_section = true;
                timeslots_html = '<ul>';

                for (i = 0; i < timeslots.length; i++) {
                    start_time = new Date(clean_date_string(timeslots[i].datetime_start)).dateFormat(date_format);
                    end_time   = new Date(clean_date_string(timeslots[i].datetime_end)).dateFormat('H:i');
                    location   = timeslots[i].room;

                    timeslots_html += '<li>' + start_time + ' - ' + end_time + '</li>';
                }

                timeslots_html += '</ul>';
            }

            $('#course-details-county').text(data.county).parents('li').toggleClass('hidden', !data.county);
            $('#course-details-duration').text(data.duration_formatted).parents('li').toggleClass('hidden', data.duration <= 0);
            $('#course-details-intro-duration').text(data.duration_formatted);
            $('#course-details-intro-duration-wrapper').toggleClass('hidden', data.duration <= 0);

            $('.course-details-intro-data').toggleClass('hidden', !data.county && data.duration <= 0);
            $('.course-details-intro-data').prev('hr').toggleClass('hidden', !data.county && data.duration <= 0);

            $('#course-details-timeslots-location').text(location);
            $dates
                .toggleClass('hidden', timeslots_html == '')
                .html(timeslots_html);
            $('#course-details-timeslots-wrapper').toggleClass('hidden', !show_section);
        });
    });

    /* Back/forward butons repopulate form fields. This runs at the same time as the document ready event.
     * This code needs to run after the back/forward button repopulation has been done.
     * There is no specific event to capture exactly when this happens, but any arbitrary delay should work.
     * 1 ms in this case
     */
    setTimeout(function() {
        $("#schedule_selector").change();
    }, 1);

    $(".start_date").on("change", function () {
        var id = $(this).val();
        var cid = $(this).data("id");
        var event_id = this.selectedIndex != -1 ? $(this.options[this.selectedIndex]).data("event_id") : "";
        var lid = $(".location[data-id='" + cid + "']").val();
        var price_wrapper = $(this).parents('.contentBlock').find('.price_wrapper')[0];
        var book_button = $(this).parents('.contentBlock').find('.course-book')[0];

        if (id.length > 0)
        {
            $(".course-book[data-id='" + cid + "']").data("schedule", id);
            $(".course-enquire[data-id='" + cid + "']").data("schedule", id);
            $.post('/frontend/courses/get_schedule_price_by_id', {sid: id, event_id: event_id}, function (data) {
                $(price_wrapper).find('.price').html(data.price);
                price_wrapper.style.visibility = 'visible';
                if (data.price.toLowerCase() == 'no fee' || data.price.toLowerCase() == '' || data.book_on_website == 0) {
                    book_button.style.visibility = 'hidden';
                }
                else {
                    book_button.style.visibility = 'visible';
                }
            });

            if ($(".start_date[data-id='" + cid + "']").val().length === 0) {
                $.post('/frontend/courses/get_all_locations_for_date', {cid: cid}, function (data) {
                    if (data.message === 'success') {
                        //
                        //$(".location[data-id='" + cid + "']").empty();
                        //$(".location[data-id='" + cid + "']").html(data.response);
                    }
                    return false;
                }, "json");
            }
        }
        else {
            book_button.style.visibility = 'hidden';
            price_wrapper.style.visibility = 'hidden';
            $(price_wrapper).find('.price').html('');
            $(".course-book[data-id='" + cid + "']").data("schedule", '0');
            $(".course-enquire[data-id='" + cid + "']").data("schedule", '0');
            {
                $.post('/frontend/courses/get_locations_for_date', {id: cid, sid: sid}, function (data) {
                    if (data.message === 'success') {
                        //$(".location[data-id='" + cid + "']").empty();
                        //$(".location[data-id='" + cid + "']").html(data.response);
                    }
                    return false;
                }, "json");
            }
        }
    });

    $(".location").on("change", function ()
    {
        var block = $(this).parents('.contentBlock');
        var id = $(this).val();
        var cid = $(this).data("id");
        var sid = $(".start_date[data-id='" + cid + "']").val();

        // When a location is changed, price isn't calculated until a time is also chosen
        block.find('.price_wrapper')[0].style.visibility = 'hidden';
        block.find('.price_wrapper span').html('');

        if (sid != null && sid.length > 0)
        {
            $(".course-book[data-id='" + cid + "']").data("schedule", id);
            $(".course-enquire[data-id='" + cid + "']").data("schedule", id);
        }

        $('#start_date_' + cid)[0].style.visibility = 'visible';
        $.post('/frontend/courses/get_dates_for_location', {lid: id, sid: sid, cid: cid}, function (data) {
            if (data.message === 'success') {
                $(".start_date[data-id='" + cid + "']").empty();
                $(".start_date[data-id='" + cid + "']").html(data.response);
            }
            return false;
        }, "json");
    });

    $("#schedule_selector").on('change', (function ()
    {
        $("#enquire-course, #book-course").prop("disabled", true);
        $("#display_group_booking_input").addClass("hidden");
        if ($(this).val().length > 0) {
            var id = $(this).find(':selected').data('event_id');
            if (isNaN(parseInt(id))) {
                return;
            }
            $("#enquire_course").data("id", id);
            $("#book_course").data("id", id);
            $.post('/frontend/courses/get_schedule_event_detailed_info', {event_id: id}, function (data)
            {
                if (data.message === 'success') {
                    if (data.book_on_website == 0) {
                        $("#book-course").hide();
                    } else {
                        $("#book-course").show();
                    }
                    if (data.is_group_booking == 1) {
                        $("#display_group_booking_input").removeClass("hidden");
                    }
                    $("#schedule_description").html(data.description);
                    $("#schedule-description").html(data.description);
                    $("#schedule_date").html(data.date);
                    $("#schedule_duration").html(data.duration);
                    $("#schedule_frequency").html(data.frequency);
                    $("#schedule_time").html(data.time);
                    $("#schedule_start_time").html(data.start_time);
                    $("#schedule_days").html(data.days);
                    $("#schedule_location").html(data.location);
                    $("#schedule_trainer").html(data.trainer);
                    $('#trainer_name').html(data.trainer);
                    $('#trainer_name').show();
                    if(data.repeat != "")
                    {
                        $("#frequency_change").show();
                        $("#frequency_time").html(data.repeat);
                    }
                    $("#desc").fadeIn();
                    $("#book-course").prop("disabled",  (data.allow_booking === "0"));
                    $("#enquire-course").prop("disabled", false);
                }
                return false;
            }, "json");
        }

    }));

    $("#checkout-county").change(function(){
        var id = parseInt($(this).val());
        if (id > 0) {
            $.post('/admin/courses/ajax_get_cities_for_county', {id: id}, function (data) {
                $("#city_id").html(data);
                if ($("#checkout-city").data("default")) {
                    $("#checkout-city").val($("#checkout-city").data("default"));
                }
            }, "text");
        } else {
            $("#checkout-city").html('<option value="" selected="selected">Please select county first</option>');
        }
    });

    $("#use_guardian_addr").on("click", function () {
        $("#student_address1").val($("#guardian_address1").val());
        $("#student_address2").val($("#guardian_address2").val());
        $("#student_address3").val($("#guardian_address3").val());
        $("#student_city").val($("#guardian_city").val());
        $("#student_county").val($("#guardian_county").val());
        $("#selectstudent_county").html($("#guardian_county option:selected").text());
        return false;
    });
    $("#use_guardian_addr2").on("click", function () {
        $("#student_email").val($("#guardian_email").val());
        $("#student_mobile").val($("#guardian_mobile").val());
        $("#student_phone").val($("#guardian_phone").val());
        return false;
    });
    $("#booking-course").click(function (ev) {
        ev.preventDefault();
        $("#trigger").val('booking2');
        $("#subject").val('New booking');
		if ($("#guardian_email").length > 0) {
			if ($("#guardian_email").val().length > 5) {
				$("#guardian_mobile").removeClass("validate[required]");
				$("#guardian_mobile_require").remove();
			}
		}
        var valid = ($("#booking_form").validationEngine('validate'));
        if (valid) {
            var data = $("#booking_form").serialize();
            $.post('/frontend/courses/ajax_save_booking_with_cart/', data, function (data) {
                if (data.success === 1)
                    $("#booking_form").attr('action', '/payment.html').submit();
            //blank for free use!
            }, "json");
        } else {
            setTimeout('removeBubbles()', 5000);
        }
        return false;
    });
    $("#enquiring-course").click(function (ev) {
        ev.preventDefault();
        $("#trigger").val('enquiry');
        $("#subject").val('Enquiry from webpage');
        $("#redirect").val('thank-you.html');
        if ($("#guardian_email, #booking_form-guardian_email").val().length > 5) {
            $("#guardian_mobile, #booking_form-guardian_mobile").removeClass("validate[required]");
            $("#guardian_mobile_require").remove();
        }
        var valid = ($("#booking_form").validationEngine('validate'));
        if (valid) {
            $("#booking_form").attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }
    });


    $("#submit-checkout").click(function (ev) {
        ev.preventDefault();
        /*
         * Set Form Fields to be Validated
         * WE DO THIS HERE, AS USING THE PROPER WAY OF SETTING: class="some other_class validate[required]" TO THE REQUIRED FIELD IN THE HTML,
         * IS BREAKING THE FUNCTIONALITY OF THE: custom-form-elements.js SCRIPT
         * Example: setting of a class="styled validate[required]" to a select drop-down: will NOT WORK
         */
        var validate_fields_ids = Array(
            'ccName', 'ccNum', 'ccType', 'ccv', 'ccExpYY', 'ccExpMM',
            'accept_span', 'contact_span'
        );

        for (var i = 0; i < validate_fields_ids.length; i++) {
            // Usually SPANS are GENERATED FOR CHECKBOXES and RADIOS - following the: custom-form-elements.js SCRIPT
            if ($('#' + validate_fields_ids[i]).get(0).tagName == 'SPAN') {
                // Set validated - checkboxes
                if ($('#' + validate_fields_ids[i]).attr('data-checked') == 'false' && !$('#' + validate_fields_ids[i]).hasClass('validate[required]')) {
                    $('#' + validate_fields_ids[i]).addClass("validate[required]");
                }
                else if ($('#' + validate_fields_ids[i]).attr('data-checked') == 'true' && $('#' + validate_fields_ids[i]).hasClass('validate[required]')) {
                    $('#' + validate_fields_ids[i]).removeClass('validate[required]');
                }
            }
            else {
                /*
                 * Other fields like: INPUT, SELECT etc. will have a corresponding SPANS with different than their IDs
                 * Example:
                 * 		- select with ID: ccExpYY -=> will have a corresponding SPAN with ID: selectccExpYY
                 */
                if (!$('#' + validate_fields_ids[i]).hasClass('validate[required]')) $('#' + validate_fields_ids[i]).addClass('validate[required]');
            }
        }

        // Validate the form
        var valid = $('#payment_form').validationEngine('validate');

        if (valid) {
            var data = {
                title: $("#title").val(),
                amount: $("#amount").val(),
                custom: $("#ids").val(),
                thanks_page: 'http://' + window.location.host + '/course-booking-success.html',
                error_page: 'http://' + window.location.host + '/course-booking-error.html',
                ccType: $("#ccType").val(),
                ccName: $("#ccName").val(),
                ccAddress1: $("#ccAddress1").val(),
                ccAddress2: $("#ccAddress2").val(),
                ccNum: $("#ccNum").val().replace(/[\s\-]/g,''),
                ccv: $("#ccv").val(),
                ccExpMM: $("#ccExpMM").val(),
                ccExpYY: $("#ccExpYY").val(),
                comments: $("#comments").val(),
                signupCheckbox: (($("#signupCheckbox_span").attr('data-checked') == 'true') ? $('#signupCheckbox_span').attr('value') : ''),
                form_identifier: $('#form_identifier').val(),
                payment_form_name: $('#payment_form_name').val(),
                payment_form_email_address: $('#payment_form_email_address').val(),
                payment_form_tel: $('#payment_form_tel').val()
            };
            $.post('/frontend/courses/cart_processor', {data: JSON.stringify(data)}, function (response) {
                if (response.status === 'success') {
                    window.location = response.redirect;
                } else {
                    window.location = response.redirect;
                }
            }, "json");
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }
    });

	if (typeof jQuery.ui != 'undefined')
	{
		$("#search-box").autocomplete({
			source: "/frontend/courses/search_course",
			minLength: 2,
			select: function (event, ui) {
				if ($("#search-location").val().length > 0) {
					var location = $("#search-location").val();
				}
				else {
					var location = 0;
				}
				if ($("#search-category").val().length > 0) {
					var category = $("#search-category").val();
				}
				else {
					var category = 0;
				}
				$.post('/frontend/courses/get_locations_and_categories_for_course', {title: ui.item.value, location: location, category: category}, function (response) {
					if (response.success === '1') {
						$("#search-category").empty();
						$("#search-location").empty();
						$("#search-category").html(response.categories);
						$("#search-location").html(response.locations);

					}
				}, "json");
			}
		});
	}

    $("#search-submit").click(function (ev) {
        ev.preventDefault();
        var add = '/?title=' +
            ((encodeURIComponent($("#search-box").val()) != 'KEYWORDS') ? encodeURIComponent($("#search-box").val()) : '') +
            '&location=' + $("#search-location").val() +
            '&category=' + $("#search-category").val() +
            '&page=' + ((typeof $('#current_page') !== 'undefined' && $('#current_page').val() > '1') ? $("#current_page").val() : 1);
        window.location = '/course-list.html' + add;
    });

    $("#search-form").on("submit", function (ev) {
        ev.preventDefault();
        var add = '/?title=' +
            ((encodeURIComponent($("#search-box").val()) != 'KEYWORDS') ? encodeURIComponent($("#search-box").val()) : '') +
            '&location=' + $("#search-location").val() +
            '&category=' + $("#search-category").val() +
            '&page=' + ((typeof $('#current_page') !== 'undefined' && $('#current_page').val() > '1') ? $("#current_page").val() : 1);
        window.location = '/course-list.html' + add;
    });

    if ($("#schedule_selector").length > 0)
    {
        var id = $("#schedule_selector").find(':selected').data('event_id');
        $("#enquire_course").data("id", id);
        $("#book_course").data("id", id);
        if (id) {
            $.post('/frontend/courses/get_schedule_event_detailed_info', {event_id: id}, function (data) {
                if (data.message === 'success') {
                    if (data.book_on_website == 0) {
                        $("#book-course").hide();
                    } else {
                        $("#book-course").show();
                    }
                    $("#schedule_description").html(data.description);
                    $("#schedule_date").html(data.date);
                    $("#schedule_duration").html(data.duration);
                    $("#schedule_frequency").html(data.frequency);
                    $("#schedule_time").html(data.time);
                    $("#schedule_start_time").html(data.start_time);
                    $("#schedule_days").html(data.days);
                    $("#schedule_location").html(data.location);
                    $("#schedule_trainer").html(data.trainer);
                    $('#trainer_name').html(data.trainer);
                    $('#trainer_name').show();
                    $("#desc").fadeIn();
                }
                return false;
            }, "json");
        }
    }

    $("#form-newsletter, #newsletter-form").on('submit', function(ev) {
        var $form = $(this);
        var $captcha_section = $form.find('.captcha-section');

        // If the CAPTCHA has been filled out, fill out the field checked by the validation
        if (typeof grecaptcha != 'undefined' && grecaptcha.getResponse().length !== 0) {
            $form.find('[id*=captcha-hidden]').val(1);
        }

        if ($captcha_section.length && $captcha_section.hasClass('hidden')) {
            // CAPTCHA exists but isn't visible
            // Make the CAPTCHA visible. User will need to submit the form again, after filling it out.
            $captcha_section.removeClass('hidden');
            return false;
        }
        else if (!$form.validationEngine('validate')) {
            // Form fields failed validation
            setTimeout(removeBubbles, 5000);
            return false;
        }
        else {
            // Form is valid
            $form.attr('action', '/frontend/formprocessor').submit();
        }
    });

    $("#submit-contact-us").click(function (ev) {
        ev.preventDefault();
        //validate[required,custom[email]] validate[required]
        var valid = ($("#form-contact-us").validationEngine('validate'));
        if (valid) {
            $('#form-contact-us').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });
    $("#reset-booking").click(function (ev) {
        ev.preventDefault();
        window.location = 'http://' + window.location.host;
    });

    $("#clear-filter").click(function (ev) {
        ev.preventDefault();
        window.location = '/course-list.html';
    });

    /*
    $("#sort-asc").click(function (ev) {
        ev.preventDefault();
        var add = "/course-list.html/?";
        var current_page = 1;
        if (typeof $('#current_page') !== 'undefined' && $('#current_page').val() > '1') {
            current_page = $('#current_page').val()
        }
        if ($("#search-box").val().length > 0) {
            add = add + "title=" + encodeURIComponent($("#search-box").val()) + '&';
        }
        if ($("#search-location").val().length > 0) {
            add = add + "location=" + $("#search-location").val() + '&';
        }
        if ($("#search-category").val().length > 0) {
            add = add + "category=" + $("#search-category").val() + '&';
        }
        add = add + "sort=asc" + "&page=" + current_page;
        window.location = add;
    });
    $("#sort-desc").click(function (ev) {
        ev.preventDefault();
        var add = "/course-list.html/?";
        var current_page = 1;
        if (typeof $('#current_page') !== 'undefined' && $('#current_page').val() > '1') {
            current_page = $('#current_page').val()
        }
        if ($("#search-box").val().length > 0) {
            add = add + "title=" + encodeURIComponent($("#search-box").val()) + '&';
        }
        if ($("#search-location").val().length > 0) {
            add = add + "location=" + $("#search-location").val() + '&';
        }
        if ($("#search-category").val().length > 0) {
            add = add + "category=" + $("#search-category").val() + '&';
        }
        add = add + "sort=desc" + "&page=" + current_page;
        window.location = add;
    });
    */

    var s_location_default_opt = $("#search-location option").eq(0);
    var s_category_default_opt = $("#search-category option").eq(0);

    $("#recaptcha_response_field").addClass('validate[required]');

    // Courses Pagination Navigation Buttons
    $('.courses_list_pagination .pagination-button').click(function (ev) {
        ev.preventDefault();

        // Take First Child / the span within this button
        var clicked_button = $(this).children().eq(0);

        if (clicked_button.hasClass('active')) {
            var move_to_page_link = $(clicked_button).data('link_url');

            if (move_to_page_link.length != 0) window.location = move_to_page_link;

        }//else the button has been DISABLED - DO NOTHING
    });

    $('[action="frontend/formprocessor/"]:not(#payment_form)').on('submit',function(ev)
    {
        ev.preventDefault();
        var valid = $(this).validationEngine('validate');
        if (valid)
        {
            this.submit();
        }
        else
        {
            setTimeout(removeBubbles, 5000);
        }
    });

    $('.bookings_form').on('submit',function(ev)
    {
        ev.preventDefault();
        var valid = $(this).validationEngine('validate');
        if (valid)
        {
            this.submit();
        }
        else
        {
            setTimeout(removeBubbles, 5000);
        }
    });
     window.validate_pps =  function (field, rules, i, options){
        if ($('#accreditation-nationality').val() !=='') {
            if ($('#accreditation-nationality').val() === 'Irish') {
                var pps = $('#accreditation-pps').val();
                if (pps === undefined || pps === '') {
                    rules.push('required');
                    return 'Applicants domiciled in Ireland must provide a PPS number as it is a requirement by the Higher Education Authority in relation to the annual statistical returns and is part of the Registrations process';
                } else {
                   if (pps.length !== 8) {
                       return 'PPS number must have 8 symbols';
                   } else {
                       var formatRegex = /^(\d{7})([A-Z]{1,2})$/i;
                       if (!formatRegex.test(pps)) {
                           return "The format of the provided PPS number is invalid";
                       }
                   }
                }
            }
        }
    }

    $(document).on('click', '.formError', function()
    {
        $(this).fadeOut();
    });

    $('.checkout-years-completed').find('input').on('change', function()
    {
        var $table = $('#application_year_details_table');
        var $tr = $table.find('tr[data-year_id="' + this.value + '"]');

        var $repeat = $($(this).parents('.checkout-years-completed').data('repeat'));
        var index   = $(this).parents('label').index();
        var $corresponding_repeat = $repeat.find('.btn-group:nth-child(' + (index + 1) + ')');

        if (this.checked) {
            $tr.removeClass("hidden");
            $corresponding_repeat.removeClass('invisible');
        } else {
            $tr.addClass("hidden");
            $corresponding_repeat.addClass('invisible');
        }

        if ($('#application-years-completed').find(':checked').length) {
            $table.removeClass('hidden');
        } else {
            $table.addClass('hidden');
        }
    });

    $('.payment-options-selector :radio').on('change', function() {
        var $selector = $(this).parents('.payment-options-selector');
        var $tabs     = $selector.find('\+ .payment-options-tabs');
        var index     = $selector.find(':radio:checked').data('index');

		$(".booking_fee.interest").addClass("hidden");
        $(".booking_fee.deposit_due_now").addClass("hidden");
		$(".header-cart-breakdown-deposit").addClass("hidden");
		$(".header-cart-breakdown-interest").addClass("hidden");
        $tabs.find('.payment-options-tab').addClass('hidden');
        $tabs.find('.payment-options-tab[data-index='+index+']').removeClass('hidden');
		if (index > 0) {
			$(".booking_fee.interest").removeClass("hidden");
            $(".booking_fee.deposit_due_now").removeClass("hidden");
			$(".header-cart-breakdown-deposit").removeClass("hidden");
			$(".header-cart-breakdown-interest").removeClass("hidden");
            var interest = $tabs.find('.payment-options-tab[data-index='+index+'] .table.installments tfoot').data('interest');
            var deposit = $tabs.find('.payment-options-tab[data-index='+index+'] .table.installments tfoot').data('deposit');
			$(".booking_fee.interest .checkout-breakdown-booking_fee, .header-cart-breakdown-interest .interest").html(interest);
            $(".booking_fee.deposit_due_now .checkout-breakdown-booking_fee").html(deposit);
			$(".header-cart-breakdown-deposit .deposit").html("€" + deposit);
            $("#checkout-breakdown-total").each(function(){
				$(".header-cart-breakdown-total .cart-total").html(parseFloat($(this).data("total").replace(",", "")) + parseFloat(interest));
                $(this).html(
                    parseFloat($(this).data("total").replace(",", "")) + parseFloat(interest)
                );
            });

		}
    });

    $("#checkout-student-tabs li").on("click", function(){
        $("#checkout-student-tabs-contents .tab-pane").addClass("hidden");
		$("#checkout-student-tabs-contents .tab-pane").find("input, select, textarea").prop("disabled", true);
        var student_id = $(this).data("student_id");
        $("#checkout-student-tabs-contents #student_tab_" + student_id).removeClass("hidden");
		$("#checkout-student-tabs-contents #student_tab_" + student_id).find("input, select, textarea").prop("disabled", false);
    });
});

// Hide the pop-up bubbles from the jQuery Validation
function removeBubbles() {
    $('.formError').each(function (i, e) {
        document.body.removeChild(e);
    });
}

function checkCCDates()
{
    var ok = false;
    var d = new Date();
    var month = $.trim($("#ccExpMM").val()) - 1;
    if ($("#ccExpYY").val() >= d.getFullYear().toString().replace('20',''))
    {
        if(month >= d.getMonth())
        {
            ok = true;
        }
        else if($("#ccExpYY").val() > d.getFullYear().toString().replace('20',''))
        {
            ok = true;
        }
    }
    if(!ok)
    {
        return '* Expiration date must not have passed';
    }
}

function switch_billing_address(type) {
    let id;
    $('.billing-content').not('.primary-biller-content').find('input, select').each(function () {
        id = $(this).attr('id') || '';
        var data = `${type}-` + ((this.nodeName === 'SELECT') ? 'address-' : '') + (id.replace('checkout-', ''));
        $(this).val($(this).data(data));
        if(this.nodeName === 'SELECT') {
            // force the dropdown to update pseudo-label not updating correctly without it
            $(this).change();
        }
    });
}

window.luhnTest = function(input)
{
	var value = input.val();
	// accept only digits, dashes or spaces
	if (/[^0-9-\s]+/.test(value))
	{
		return 'Invalid credit/debit card number';
	}

	// The Luhn Algorithm.
	var nCheck = 0, nDigit = 0, cDigit = 0, bEven = false;
	value = value.replace(/\D/g, "");

	for (var n = value.length - 1; n >= 0; n--)
	{
		cDigit = value.charAt(n);
		nDigit = parseInt(cDigit, 10);

		if (bEven)
		{
			if ((nDigit *= 2) > 9) nDigit -= 9;
		}

		nCheck += nDigit;
		bEven = !bEven;
	}

	return (nCheck % 10) == 0 ? undefined : 'Invalid credit/debit card number';
};

function checkout_upload_file(file, callback) {
    var uri = "/frontend/bookings/checkout_file_upload";
    var xhr = new XMLHttpRequest();
    var fd = new FormData();

    xhr.open("POST", uri, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                callback(JSON.parse(xhr.responseText));
            } catch (exc) {

            }
        }
    };
    fd.append('file', file);
    // Initiate a multipart/form-data upload
    xhr.send(fd);
}

$(document).on("change", ".file_select", function(){
    var file_select = this;
    function set_file_id(response)
    {
        $(file_select).parents(".file_id_container, td").find(".file_id").val(response.file_id);
    }
    for (var i in this.files) {
        if (this.files[i].name && this.files[i].size) {
            checkout_upload_file(this.files[i], set_file_id);
        }
    }
});

function object_group_by(input_object, key)
{
    var output_object = [];

    for (var i = 0; i < input_object.length; i++) {
        if (!output_object[input_object[i][key]]) {
            output_object[input_object[i][key]] = [];
        }

        output_object[input_object[i][key]].push(input_object[i]);
    }

    return output_object;
}

function availability_course_details_expanded()
{
    var swiper_id = this.id;
    var display_timeslots = $(this).data('display_timeslots') == 'YES';
    if ($(this).data('is_fulltime') == 'YES') {
        $('#'+swiper_id).find('.swiper-slide-active').trigger('click');
        return;
    }

    var data = {};
    if ($(this).data('display_availability') == 'per_schedule') {
        data.schedule_id = $(this).data('schedule_id');
    } else {
        data.course_id = $(this).data('course-id');
    }
    $.ajax({
        url: '/frontend/courses/ajax_get_timeslots',
        data: data,
        dataType: 'json'
    }).done(function(result) {
        var timeslots       = object_group_by(result.timeslots, 'start_date');
        var $template       = $('#availability-date-template');
        var $empty_template = $('#availability-date-empty-template');

        var days   = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        var date   = new Date();
        var slides = [];
        var t      = 0;
        var $slide, cheapest, iso_date, date_formatted;

        date.setDate(date.getDate() -1);

        var first_slide = false;

        // Display days for the next year
        for (var i = 0; i < 366; i++) {
            date.setDate(date.getDate() + 1);
            iso_date = date.toISOString().substr(0, 10);

            // If there are timeslots in the given date
            if (timeslots[iso_date]) {
                cheapest = false;

                // Take note of the first date with timeslots, so we can focus it later
                if (first_slide === false) {
                    first_slide = i;
                }

                // Find the cheapest timeslot on a date
                for (t = 0; t < timeslots[iso_date].length; t++) {
                    if (cheapest === false || parseFloat(timeslots[iso_date][t].fee_amount ? timeslots[iso_date][t].fee_amount : timeslots[iso_date][t].schedule_fee_amount) < cheapest) {
                        cheapest = timeslots[iso_date][t].fee_amount ? timeslots[iso_date][t].fee_amount : timeslots[iso_date][t].schedule_fee_amount;
                    }
                }

                // Get the slide template from the DOM and popuplate it with data for the day
                $slide = $template.clone();
                $slide.find('.availability-date').attr('data-timeslots', JSON.stringify(timeslots[iso_date]));

                if (cheapest) {
                    $slide[0].getElementsByClassName('availability-date-minimum')[0].innerHTML = cheapest;
                }
            }
            else {
                $slide = $empty_template.clone();
            }

            date_formatted = days[date.getDay()] + ' ' + date.getDate() + ' ' + months[date.getMonth()];
            $slide[0].getElementsByClassName('timeline-swiper-date-formatted')[0].innerHTML = date_formatted;

            slides.push($slide.html());
        }

        var availability_slider = new Swiper('#'+swiper_id+' .swiper-container', {
            slidesPerView : 3,
            watchSlidesVisibility: true,
            navigation    : {
                prevEl        : '#'+swiper_id+' .timeline-swiper-prev',
                nextEl        : '#'+swiper_id+' .timeline-swiper-next'
            },
            spaceBetween  : 0,
            virtual       : {
                slides        : slides
            }
        });

        // Begin on the first date with timeslots (or today, if there are none)
        availability_slider.slideTo(first_slide);

        // Display timeslots when a date is clicked
        $('#'+swiper_id).on('click', '.swiper-slide', function(ev)
        {
            // Only continue if there are timeslots this day
            if ($(this).find('.availability-date:not(.timeline-swiper-date--empty)')[0]) {
                ev.preventDefault();

                // Highlight the selected date, unhighlight others
                $('#'+swiper_id).find('.swiper-slide.selected').removeClass('selected');
                $(this).addClass('selected');
                availability_slider.virtual.update();

                // Display details for the selected date
                var timeslots = $(this).find('.availability-date').data('timeslots');
                var date_formatted = this.getElementsByClassName('timeline-swiper-date-formatted')[0].innerHTML;
                var timeslots_html = '';
                var $clone, start_time, end_time, hours, minutes, duration;

                if (timeslots[0].booking_type == "Subscription") {
                    var displayed_schedules = [];
                    for (var i = 0; i < timeslots.length; i++) {
                        if (displayed_schedules.indexOf(timeslots[i].schedule_id) != -1) {
                            continue;
                        }
                        duration = "";
                        displayed_schedules.push(timeslots[i].schedule_id);
                        $clone = $('#availability-schedule_subscription-template').clone();
                        if (display_timeslots) {
                            start_time = new Date(clean_date_string(timeslots[i].datetime_start+"Z")).toGMTString();
                            end_time = new Date(clean_date_string(timeslots[i].datetime_end+"Z")).toGMTString();
                            $clone = $('#availability-timeslot-template').clone();
                            duration = '';
                            hours = parseInt(timeslots[i].duration.split(':')[0]);
                            minutes = parseInt(timeslots[i].duration.split(':')[1]);

                            if (hours == 1) duration += '1 hour ';
                            if (hours > 1) duration += hours + ' hours ';
                            if (minutes == 1) duration += '1 minute';
                            if (minutes > 1) duration += minutes + ' minutes';

                            if (timeslots[i].fee_per == 'Timeslot') {
                                $clone.find('.availability-timeslot-per_schedule').remove();
                                $clone.find(".availability-timeslot-per_timeslot").show();
                            } else {
                                $clone.find('.availability-timeslot-per_timeslot').remove();
                                $clone.find('.availability-timeslot-per_schedule').show();
                            }

                            $clone.find('.availability-timeslot-date').html(date_formatted);
                            $clone.find('.availability-timeslot-start_time').html(start_time.substr(17, 5));
                            $clone.find('.availability-timeslot-end_time').html(end_time.substr(17, 5));
                            $clone.find('.availability-timeslot-duration').text(duration);
                        }

                        $clone.find('.availability-timeslot-duration').text(duration);
                        $clone.find('.availability-timeslot-trainer').text(timeslots[i].trainer);
                        $clone.find('.availability-timeslot-room').text(timeslots[i].room ? timeslots[i].room : "");
                        $clone.find('.availability-timeslot-location').text(timeslots[i].location ? timeslots[i].location : "");
                        $clone.find('.availability-timeslot-payment_type').text("Subscription");
                        $clone.find('.availability-timeslot-price').html(timeslots[i].fee_amount ? timeslots[i].fee_amount : timeslots[i].schedule_fee_amount);

                        $clone.find(".availability-book, .availability-unbook, .availability-timeslot")
                            .attr("data-course_id", timeslots[i].course_id)
                            .attr("data-schedule-id", timeslots[i].schedule_id)
                            .attr("data-event-id", "all")
                            .attr("data-fee-per", timeslots[i].fee_per)
                            .attr("data-fee_amount", timeslots[i].fee_amount)
                            .attr("data-payment_type", timeslots[i].payment_type)
                            .attr("data-booking-type", timeslots[i].booking_type)
                            .attr("data-year-id", "");
                        $clone.find(".availability-timeslot-per_timeslot").hide();
                        $clone.find(".availability-timeslot-per_schedule").show();
                        if (timeslots[i].trial_timeslot_free_booking == 1) {
                            $clone.find(".availability-book.trial").removeClass("hidden");
                            $clone.find(".availability-book.trial").attr("data-event-id", timeslots[i].event_id)
                        } else {
                            $clone.find(".availability-book.trial").addClass("hidden");
                        }
                        timeslots_html += $clone.html();
                    }
                } else if (!display_timeslots) {
                     duration = "";
                     $clone = $('#availability-schedule_notimeslot-template').clone();
                     $clone.find('.availability-timeslot-duration').text(duration);
                     $clone.find('.availability-timeslot-trainer').text(timeslots[0].trainer);
                     $clone.find('.availability-timeslot-room').text(timeslots[0].room);
                     $clone.find('.availability-timeslot-location').text(timeslots[0].location);
                     $clone.find('.availability-timeslot-payment_type').text(timeslots[0].payment_type_name);
                     $clone.find('.availability-timeslot-price').html(timeslots[0].fee_amount ? timeslots[0].fee_amount : timeslots[0].schedule_fee_amount);

                    $clone.find(".availability-book, .availability-unbook, .availability-timeslot")
                        .attr("data-course_id", timeslots[0].course_id)
                        .attr("data-schedule-id", timeslots[0].schedule_id)
                        .attr("data-event-id", "all")
                        .attr("data-fee-per", timeslots[0].fee_per)
                        .attr("data-fee_amount", timeslots[0].fee_amount)
                        .attr("data-payment_type", timeslots[0].payment_type)
                        .attr("data-booking-type", timeslots[0].booking_type)
                        .attr("data-year-id", "");
                    $clone.find(".availability-timeslot-per_timeslot").hide();
                    $clone.find(".availability-timeslot-per_schedule").show();
                    if (timeslots[0].trial_timeslot_free_booking == 1) {
                        $clone.find(".availability-book.trial").removeClass("hidden");
                        $clone.find(".availability-book.trial").attr("data-event-id", timeslots[0].id)
                    } else {
                        $clone.find(".availability-book.trial").addClass("hidden");
                    }
                    timeslots_html = $clone.html();
                } else {
                     // Populate timeslot template for each timeslot in the day
                     for (var i = 0; i < timeslots.length; i++) {
                         start_time = new Date(clean_date_string(timeslots[i].datetime_start+"Z")).toGMTString();
                         end_time = new Date(clean_date_string(timeslots[i].datetime_end+"Z")).toGMTString();
                         $clone = $('#availability-timeslot-template').clone();
                         duration = '';
                         hours = parseInt(timeslots[i].duration.split(':')[0]);
                         minutes = parseInt(timeslots[i].duration.split(':')[1]);

                         if (hours == 1) duration += '1 hour ';
                         if (hours > 1) duration += hours + ' hours ';
                         if (minutes == 1) duration += '1 minute';
                         if (minutes > 1) duration += minutes + ' minutes';

                         if (timeslots[i].fee_per == 'Timeslot') {
                             $clone.find('.availability-timeslot-per_schedule').remove();
                             $clone.find(".availability-timeslot-per_timeslot").show();
                         } else {
                             $clone.find('.availability-timeslot-per_timeslot').remove();
                             $clone.find('.availability-timeslot-per_schedule').show();
                         }

                         $clone.find('.availability-timeslot-date').html(date_formatted);
                         $clone.find('.availability-timeslot-start_time').html(start_time.substr(17, 5));
                         $clone.find('.availability-timeslot-end_time').html(end_time.substr(17, 5));
                         $clone.find('.availability-timeslot-duration').text(duration);
                         $clone.find('.availability-timeslot-trainer').text(timeslots[i].trainer || '');
                         $clone.find('.availability-timeslot-room').text(timeslots[i].room || '');
                         $clone.find('.availability-timeslot-location').text(timeslots[i].location || '');
                         $clone.find('.availability-timeslot-payment_type').text(timeslots[i].payment_type_name || '');
                         $clone.find('.availability-timeslot-price').html(timeslots[i].fee_amount ? timeslots[i].fee_amount : timeslots[i].schedule_fee_amount);
                         $clone.find(".availability-timeslot-per_timetable").show();

                         $clone.find(".availability-book, .availability-unbook, .availability-timeslot")
                             .attr("data-course_id", timeslots[i].course_id)
                             .attr("data-schedule-id", timeslots[i].schedule_id)
                             .attr("data-event-id", timeslots[i].event_id)
                             .attr("data-fee-per", timeslots[i].fee_per)
                             .attr("data-fee_amount", timeslots[i].fee_amount)
                             .attr("data-payment_type", timeslots[i].payment_type)
                             .attr("data-booking-type", timeslots[i].booking_type)
                             .attr("data-year-id", "");

                         if (timeslots[i].trial_timeslot_free_booking == 1) {
                             $clone.find(".availability-book.trial").removeClass("hidden");
                         } else {
                             $clone.find(".availability-book.trial").addClass("hidden");
                         }
                         timeslots_html += $clone[0].innerHTML;
                     }
                }

                var $timeslots = $(this).parents('.availability-course').find('.availability-timeslots');

                // Replace the old timeslot HTML with the new.
                $timeslots.hide();
                $timeslots.html(timeslots_html);
                $timeslots.slideDown();
            }
        });

        // Automatically open the first day with timeslots
        $('#'+swiper_id).find('.swiper-slide-active').trigger('click');
    });
}

$(document).on('change', '#student_tab_new input', function(){
    var $select = $('select#student_id');
    var option  = $select.find('option[data-new]');
    var $tab    = $('#student_tab_new');
    $select.off('change');
    option.html($tab.find('[name=student_first_name]').val() + ' ' + $tab.find('[name=student_last_name]').val());
    option.prop('selected', true);
    $select.trigger('change');
});

$(document).on('change', '[name=student_year_id]', function(){
    check_cart();
});

$(document).on("change", "[name=cc_new]", function(){
    if (this.value == 0 && this.checked) {
        $(".new-card").addClass("hidden");
        $(".saved-card").removeClass("hidden");
    }
    if (this.value == 1 && this.checked) {
        $(".new-card").removeClass("hidden");
        $(".saved-card").addClass("hidden");
    }
});