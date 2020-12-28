var unavailableDates = [] ;
var ratecardDates = [] ;
$(document).ready(function()
{
	/*------------------------------------*\
	 Show hide the menu bar on smaller screens
	\*------------------------------------*/
	$('#nav-toggle').on('click', function()
	{
		$('#menu-wrapper-outer').show();
	});

	$('#menu-wrapper-outer').click(function(ev)
	{
		if (ev.target == this)
		{
			$(this).fadeOut('fast', function()
			{
				$(this).css('display', '');
			});

		}
	});


	/*------------------------------------*\
	 Collapsibles
	\*------------------------------------*/

	$('.collapse-toggle').on('click', function(ev)
	{
		ev.preventDefault();
		var $target = $($(this).attr('data-target'));
		var text;
		var $this = $(this);

		if ($target.is(':visible'))
		{
			$target.fadeOut(function()
			{
				text = $this.attr('data-show_text');
				if (text) $this.html(text);

			});
		}
		else
		{
			$target.fadeIn(function()
			{
				text = $this.attr('data-hide_text');
				if (text) $this.html(text);
			});
		}

	});


	/*------------------------------------*\
	 Modals
	\*------------------------------------*/
	// When an element with a target modal is clicked, display the target modal
	$('[data-target_modal]').on('click', function()
	{
		var $modal = $(this.getAttribute('data-target_modal'));
		if ($modal.hasClass('modal'))
		{
			$modal.show();
		}
	});

	// When a modal close button is clicked, dismiss the modal
	$('.modal-close').on('click', function()
	{
		$(this).parents('.modal').fadeOut('fast');
	});

	// When the user clicks outside of the modal box, dismiss the modal
	$('.modal').on('click', function(ev)
	{
		if (ev.target == this)
		{
			$(this).fadeOut('fast');
		}
	});

	/*------------------------------------*\
	 Datepickers
	\*------------------------------------*/
	jQuery('.datepicker').datetimepicker(
	{
		format:'d/m/Y',
		timepicker: false,
		minDate: '-1970/01/01' // Now minus zero (1st Jan 1970 = 0)
	});

    if ($('.daterangepicker').length >0) { //prevent a js exception if there is no date range on the page
        $('.daterangepicker').each(function(){
            var rangepicker = this;
            var checkRates = $(this).data('check-rates') == 'yes';
            $(rangepicker).dateRangePicker({
                minDays: 3,
                autoClose: true,
                format: 'DD/MM/YYYY',
                beforeShowDay: function (day) {
                    return [set_available(day, checkRates), ''];
                },
                setValue: function (s, s1, s2) {
                    $(this).parent().find('.daterangepicker-start').val(s1);
                    $(this).parent().find('.daterangepicker-end').val(s2);
                    if ($('#property-details-check_in, #property-details-check_out').length > 0){
                        checkPrices();
                    }
                }
            });
        });

		/* Adjust the position of the date range picker on the property details page */
		if (document.getElementById('booking-date-range'))
		{
			function position_property_date_picker()
			{
				// On larger screens, align to the right of the inputs to stop it going off the screen.
				// On small screens, use the default left alignment.
				if (window.outerWidth > 767)
				{
					var $datepicker = $('.date-picker-wrapper');
					var $input_wrapper = $('#booking-date-range');
					// Get the position of the right edge of the input boxes
					var pos_left = $input_wrapper.offset().left + $input_wrapper.outerWidth() - $datepicker.outerWidth();
					$datepicker.css('left', pos_left);
				}
			}
			// Perform the positioning when the picker is opened or the window is resized
			$('#booking-date-range').bind('datepicker-open', position_property_date_picker);
			$(window).resize(position_property_date_picker);


		}

    }
	// Stop the number of guests input from opening the date range picker
	$('#property-details-number_of_guests').on('click', function()
	{
		return false;
	});


	$('#property-details-check_in, #property-details-check_out').on('change', function()
	{
		checkPrices();
	});

	function checkPrices()
	{
		$("#not-available-details").css("display", "none");
		var data = {};
        data.propertyId = $('[name=property_id]').val();
		data.checkin = $('#property-details-check_in').val();
		data.checkout = $('#property-details-check_out').val();
		data.guests = $('#property-details-number_of_guests').val();
        if (data.checkin != "" && data.checkout != "" && data.guests >0) {
            $.post(
                '/frontend/propman/calculate_price',
                data,
                function (response) {
                    if (response.available) {
                        $("#booking-nights-count").html(response.price.nights);
                        $("#booking-booking-fee").html(response.price.bookingfee);
                        $("#booking-fee").html(response.price.fee);
                        $("#booking-discount").html(response.price.discount);
                        $("#booking-total").html(response.price.total);
                        $(".book-button").prop("disabled", false);
						$(".property-details-pricing-table tr").css("display", "block");
                    } else {
                        var reason = "";
                        if (response.price) {
                            if (response.price.error && response.price.reason) {
                                reason = response.price.reason;
                            }
                        }
                        $("#not-available-details p").html(reason);
                        $("#not-available-details").css("display", "");
						$(".property-details-pricing-table tr").css("display", "none");
                        $(".book-button").prop("disabled", true);
                    }
                }
            );
        }
	}

	jQuery('#property-details-number_of_guests').on('change',function(){
		checkPrices();
	});

	/*------------------------------------*\
	 Validation
	\*------------------------------------*/
	// Validate any formbuilder forms and any forms with the class "validate-on-submit"
	$('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function(ev) {
		ev.preventDefault();
		var valid = $(this).validationEngine('validate');
		if (valid) this.submit();
	});

	// Validate and process booking
	$('#booking-checkout-form').on('submit',function(ev) {
        var form = this;
		ev.preventDefault();
		var valid = $(this).validationEngine('validate');
		if (valid) {
            var data = $(this).serializeObject();

            if (data.booking_id == "") {
                disableScreenShow();

                $.post(
                    '/frontend/propman/save_booking',
                    data,
                    function (response){
                        if (response.error) {
                            $("#booking_error").text(response.message);
                        } else {
                            form.booking_id.value = response.booking_id;
                            if (response.payment == 'done') {
                                $("#booking_error").text("payment complete");
                                location.href = "/thank-you-for-booking.html";
                                return;
                            } else if (response.payment == 'continue') {
                                if (data.payment_select == 'paypal') {
                                    var ppform = $("#paypal-continue-form")[0];
                                    ppform.item_name_1.value = "Booking " + response.booking_id + " " + $(ppform.item_name_1).data("name");
                                    ppform.amount_1.value = response.price;
                                    ppform.custom.value = response.booking_id;
                                    ppform.invoice.value = response.payment_id;
                                    ppform.submit();
                                    return;
                                }
                            } else {
                                $("#booking_error").text(response.payment);
                            }

                            if (response.redirect) {
                                location.href = response.redirect;
                            }
                        }
                        disableScreenHide();
                    }
                );
            }
        }
	});

	if ($("#booking-checkout-form").length > 0) {

		$("#booking-checkout-form [name=guests]").on("change", function(){
			var guests = parseInt($("#booking-checkout-form [name=guests]").val());
			$("#booking-checkout-form [name=adults], #booking-checkout-form [name=children], #booking-checkout-form [name=infants]").each(function(){
				for (var i = this.options.length - 1 ; i >= 0 ; --i) {
					this.options[i] = null;
				}
				this.selectedIndex = -1;
				var start = this.name == 'adults' ? 1 : 0;
				for (var i = start ; i <= guests ; ++i) {
					this.options[i] = new Option(i, i);
					if (this.name == 'adults' && i == guests) {
						this.options[i].selected = true;
					}
				}
			});
		});

		$("#booking-checkout-form [name=adults]").on("change", function(){
            var guests = parseInt($("#booking-checkout-form [name=guests]").val());
            var max = guests - parseInt(this.value);
            $("#booking-checkout-form [name=children], #booking-checkout-form [name=infants]").each(function(){
                for (var i = this.options.length - 1 ; i >= 0 ; --i) {
                    this.options[i] = null;
                }
                for (var i = 0 ; i <= max ; ++i) {
                    this.options[i] = new Option(i, i);
                }
            });
		});

        $("#booking-checkout-form [name=adults], #booking-checkout-form [name=children], #booking-checkout-form [name=infants]").on("change", function(){
            var total = 0;
            var guests = parseInt($("#booking-checkout-form [name=guests]").val());
            $("#booking-checkout-form [name=adults], #booking-checkout-form [name=children], #booking-checkout-form [name=infants]").each(function(){
                total += parseInt(this.value) ? parseInt(this.value) : 0;
            });
            if (total > guests) {
                alert("Total number of adults, children and infants must be " + guests);
                return false;
            }
        })
	};

    $('#booking-balance-payment-form').on('submit',function(ev) {
        var form = this;
        ev.preventDefault();
        var valid = $(this).validationEngine('validate');
        if (valid) {
            var data = $(this).serializeObject();
            var balance = parseFloat(data.balance);
            if (!isNaN(balance) && balance >0) {
                disableScreenShow();
                $.post(
                    '/frontend/propman/save_balance_payment',
                    data,
                    function (response) {
                        if (response.error) {
                            $("#payment_error").text(response.message);
                        } else {
                            form.payment_id.value = response.payment_id;
                            if (response.payment == 'done') {
                                $("#payment_error").text("payment complete");
                                location.href = "/thank-you-for-booking.html";
                                return;
                            } else if (response.payment == 'continue') {
                                if (data.payment_select == 'paypal') {
                                    var ppform = $("#paypal-continue-form")[0];
                                    ppform.item_name_1.value = "Balance Payment " + data.property + " " + data.checkin + " " + data.checkout;
                                    ppform.amount_1.value = response.amount;
                                    ppform.invoice.value = response.payment_id;
                                    ppform.submit();
                                    return;
                                }
                            } else {
                                $("#payment_error").text(response.payment);
                            }

                            if (response.redirect) {
                                location.href = response.redirect;
                            }
                        }
                        disableScreenHide();
                    }
                );
            } else {
                $("#payment_error").text("Balance must not be zero");
            }
        }
    });

    $('#booking-lookup-button').on('click',function(ev) {
        var form = $("#booking-balance-payment-form")[0];
        var bookingId = $("#billing-information-bookingid").val();
        disableScreenShow();
        $.post(
            "/frontend/propman/getbalance",
            {bookingId: bookingId},
            function (response) {
                if (response) {
                    form.firstName.value = response.first_name;
                    form.lastName.value = response.last_name;
                    form.email.value = response.email;
                    form.property.value = response.property;
                    form.checkin.value = response.checkin;
                    form.checkout.value = response.checkout;
                    form.balance.value = response.balance;
                }
                disableScreenHide();
            }
        );
    });


	/*------------------------------------*\
	 Gallery
	\*------------------------------------*/
	$('.photoswipe-button').on('click', function()
	{
		$('.pswp').show();
		var pswpElement = document.querySelectorAll('.pswp')[0];
		var items = $(this).data('images');

		var options = {
			history: false,
			focus: false,
			showAnimationDuration: 0,
			hideAnimationDuration: 0
		};

		var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
		gallery.init();
	});

	/*------------------------------------*\
	 Wishlist
	\*------------------------------------*/

	// Add an item to the wishlist
	$(document).on('click', '.button-wishlist-add', function(ev)
	{
		ev.preventDefault();
		var id = $(this).data('id');

		// Add the property to the cookie
		$.ajax({
			url: '/frontend/propman/ajax_add_to_wishlist/'+id
		}).done(function()
			{
				// All wishlist buttons for this property
				var $buttons = $('.button-wishlist-add[data-id="'+id+'"]');
				var $button;
				$buttons.each(function()
				{
					$button = $(this);
					// Update the button class, inner text (if applicable) and hover text (if applicable)
					$button.removeClass('button-wishlist-add').addClass('button-wishlist-remove');
					if ($button.data('remove_text') && $button.html() == $button.data('add_text'))
					{
						$button.html($button.data('remove_text'));
					}
					if ($button.data('remove_text') && $button.attr('title') == $button.data('add_text'))
					{
						$button.attr('title', $button.data('remove_text'));
					}
				});

				// Show the user their wishlist
				update_propman_wishlist_modal(true);
			});
	});

	// Remove an item from the wishlist
	$(document).on('click', '.button-wishlist-remove', function(ev)
	{
		ev.preventDefault();
		var id = $(this).data('id');

		$.ajax({
			url: '/frontend/propman/ajax_remove_from_wishlist/'+id
		}).done(function()
			{
				var $buttons = $('.button-wishlist-remove[data-id="'+id+'"]');
				var $button;
				$buttons.each(function()
				{
					$button = $(this);
					// Update the button class, inner text (if applicable) and hover text (if applicable)
					$button.removeClass('button-wishlist-remove').addClass('button-wishlist-add');
					if ($button.data('add_text') && $button.html() == $button.data('remove_text'))
					{
						$button.html($button.data('add_text'));
					}
					if ($button.data('add_text') && $button.attr('title') == $button.data('remove_text'))
					{
						$button.attr('title', $button.data('add_text'));
					}

				});
				update_propman_wishlist_modal(false);
			});
	});

	// Update the wishlist modal. Show the modal if the show parameter is true.
	function update_propman_wishlist_modal(show)
	{
		$.ajax({
			url: '/frontend/propman/ajax_view_wishlist/'
		}).done(function(result)
		{
			$('.view-wishlist-listing').html(result);
			if (show && result.trim() != '')
			{
				$('#view-wishlist-modal').show();
			}
			if (result.trim() == '')
			{
				$('#view-wishlist-modal').hide();
			}
		});
	}


	/*------------------------------------*\
	 Misc
	\*------------------------------------*/
	// Load map, if needed
	if (document.getElementById('map-canvas'))
	{
		initialize_map();
	}

	// Resize the widget when the window is resized
	$(window).resize(function()
	{
		if (typeof FB != 'undefined')
		{
			FB.XFBML.parse();
		}
	});

	// Toggle visible fields, depending on the selected payment method
	$('.payment-method-select input').on('change', function()
	{
		var method = $('.payment-method-select input:checked').data('method');
		console.log(method);
		$('.payment-option-fields').hide();
		$('.payment-option-fields-'+method).show();
	});

	// Fix the position of the booking form when the user scrolls down
	$(window).scroll(function()
	{
		if (document.getElementById('property-data-sidebar-inner'))
		{
			var property_details    = document.getElementById('property-details-wrapper');
			var sidebar_coordinates = document.getElementById('property-data-sidebar-inner').getBoundingClientRect();
			var window_height       = $(window).height();

			if (sidebar_coordinates.height < window_height)
			{
				var rate_card_coordinates = document.getElementById('property-data-ratecard').getBoundingClientRect();
				var footer_coordinates    = document.getElementById('main-footer').getBoundingClientRect();
				var content_coordinates   = property_details.getBoundingClientRect();

				var footer_visible = (footer_coordinates.top < window_height);
				var sidebar_reaches_footer = document.querySelector('#property-data-sidebar-inner').getBoundingClientRect().bottom > footer_coordinates.top;

				if (content_coordinates.top < rate_card_coordinates.height && ! (footer_visible && sidebar_reaches_footer))
				{
					$('#property-data-sidebar').removeClass('fixed-bottom').addClass('fixed-top').css('bottom', '');
				}
				else if (footer_visible && sidebar_reaches_footer)
				{
					$('#property-data-sidebar').removeClass('fixed-top').addClass('fixed-bottom').css('bottom', window_height - footer_coordinates.top);
				}
				else
				{
					$('#property-data-sidebar').removeClass('fixed-top').removeClass('fixed-top').css('bottom', '');
				}
			}
		}
	});

	position_sidebar_menu();

	$(window).scroll(position_sidebar_menu);
	$(window).resize(position_sidebar_menu);

	function position_sidebar_menu()
	{
		// Fix the position of the content submenu when scrolling (this and the above should be genericised)
		var $sidebar_inner = $('#side-menu-inner');
		var $sidebar = $('#side-menu');
		if ($sidebar_inner.length && window.outerWidth > 478)
		{
			var window_height          = $(window).height();
			var header_coordinates     = document.getElementsByClassName('main-header')[0].getBoundingClientRect();
			var footer_coordinates     = document.getElementById('main-footer').getBoundingClientRect();
			var footer_visible         = (footer_coordinates.top < window_height);
			var sidebar_reaches_footer = document.getElementById('side-menu-inner').getBoundingClientRect().bottom > footer_coordinates.top;

			if (header_coordinates.bottom < 0 && ! (footer_visible && sidebar_reaches_footer))
			{
				$sidebar_inner.removeClass('fixed-bottom').addClass('fixed-top').css({'bottom':  '', 'width': $sidebar.width(), 'margin-top': ''});
			}
			else if (footer_visible && sidebar_reaches_footer)
			{
				$sidebar_inner.removeClass('fixed-top').addClass('fixed-bottom').css({'bottom': window_height - footer_coordinates.top, 'width': $sidebar.width(), 'margin-top': ''});
			}
			else
			{
				$sidebar_inner.removeClass('fixed-top').removeClass('fixed-top').css({'bottom': '', 'width': '', 'margin-top': header_coordinates.height - $('.banner-section').height()});
			}
		}
		if (window.outerWidth <= 478)
		{
			$sidebar_inner.removeClass('fixed-top').removeClass('fixed-top').css({'bottom': '', 'width': '', 'margin-top': ''});
		}
	}

	/* Dismiss alert */
	$(document).on('click', '.alert-close', function()
	{
		$(this).parents('.alert').remove();
	});
});

/*------------------------------------*\
 Google map
\*------------------------------------*/

function initialize_map()
{
	var map;
	var bounds = new google.maps.LatLngBounds();
	var mapOptions = {
		mapTypeId: 'roadmap'
	};
	var map_div = document.getElementById('map-canvas');

	// Display a map on the page
	map = new google.maps.Map(map_div, mapOptions);
	map.setTilt(45);

	var oms = new OverlappingMarkerSpiderfier(map);
	var iw = new google.maps.InfoWindow();
	oms.addListener('click', function(marker, event) {
		iw.setContent(marker.desc);
		iw.open(map, marker);
	});
	oms.addListener('spiderfy', function(markers) {
		iw.close();
	});

	// Multiple Markers
	var marker_data = $(map_div).data('needles');
	var infoWindow = new google.maps.InfoWindow();
	var infoWindowContent = $(map_div).data('infowindows');
	var markers = [];
	var marker;
	var position;

	// Loop through our array of markers & place each one on the map
	for (var i = 0; i < marker_data.length; i++)
	{
		position = new google.maps.LatLng(marker_data[i][1], marker_data[i][2]);
		bounds.extend(position);
		marker = new google.maps.Marker({
			position: position,
			map: map,
			title: marker_data[i][0]
		});
		markers[i] = marker;

		// Allow each marker to have an info window
		marker.desc = infoWindowContent[i][0];
		oms.addMarker(marker);

		// Automatically center the map fitting all markers on the screen
		map.fitBounds(bounds);
	}

	// Overwrite our map zoom level once our fitBounds function runs (Make sure it only runs once)
	var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event)
	{
		// Zoom out a bit if there's only one marker
		if (marker_data.length == 1)
		{
			this.setZoom(14);
		}
		google.maps.event.removeListener(boundsListener);
	});

}




/*------------------------------------*\
 Paypal booking
\*------------------------------------*/
$(document).on('click', '#paypal-property-booking-x', function(ev)
{
	ev.preventDefault();
	var $form = $(this).parents('form');
	var valid = $form.validationEngine('validate');
	if (valid)
	{
		$.post('/frontend/propman/ajax_paypal_booking', {form_data: $form.serialize()}, function(results)
		{
			results = JSON.parse(results);
			var status = results.status;
			var data   = results.data;
			var whereDisplay;
			if (typeof payPalRedirect == "undefined" || payPalRedirect != 0)
			{
				whereDisplay = 'body';
			}
			else if (payPalRedirect == 0)
			{
				var newWindow = window.open('', 'PayPal', "scrollbars=1,height=500,width=980");
				var newWindowBody = newWindow.document.body;

				newWindowBody.style.background = 'url("/assets/default/images/loading.gif") no-repeat center 200px';
				whereDisplay = newWindowBody;
			}

			var $paypal_form = $('<form id="paypal_form_'+$form.attr('id')+'" method="post" action="https://www.'+(data.test_mode ? 'sandbox.' : '')+'paypal.com/cgi-bin/webscr">').appendTo(whereDisplay);
			for (var property in data)
			{
				$paypal_form.append($('<input type="hidden" name="'+property+'" value="'+data[property]+'" />'));
			}
			$paypal_form.submit();
		});
	}
});

function get_unavailable_dates()
{
    var property_id = $('#property_id').val();
    $.post('/frontend/propman/unavailable_dates',{property_id: property_id},function(result)
    {
        unavailableDates = result.not_available;
        ratecardDates = result.ratecard_date;
    }, "json");
}

function set_available(date, checkRates) {
    var dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
    var now = new Date();
    if ($.inArray(dmy, unavailableDates) != -1) { // Date not avaiilable
        return false;
    } else if (date < now) { // Date in past
        return false;
    } else if (checkRates && $.inArray(dmy, ratecardDates) == -1) { // Date with no ratecard
        return false;
    } else
    { // Date Available
        return true;
    }
}

function disableScreenShow()
{
    if(!window.disableScreenDiv){
        window.disableScreenDiv = document.createElement( "div" );
        window.disableScreenDiv.style.display = "block";
        window.disableScreenDiv.style.position = "fixed";
        window.disableScreenDiv.style.top = "0px";
        window.disableScreenDiv.style.left = "0px";
        window.disableScreenDiv.style.right = "0px";
        window.disableScreenDiv.style.bottom = "0px";
        window.disableScreenDiv.style.textAlign = "center";
        window.disableScreenDiv.style.zIndex = 99999999;
        window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;background-color:#000000;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
            '<img src="/engine/shared/img/ajax-loader.gif" style="position:absolute;top:50%;left:50%;margin:-16px;z-index:2;" alt="processing..."/>';

        document.body.appendChild(window.disableScreenDiv);
    }
    window.disableScreenDiv.style.visibility = "visible";
}

function disableScreenHide()
{
    if (window.disableScreenDiv) {
        window.disableScreenDiv.style.visibility = "hidden";
    }
}