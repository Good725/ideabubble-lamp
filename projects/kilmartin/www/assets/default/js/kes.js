if( !window.disableScreenDiv ){
    window.disableScreenDiv = document.createElement( "div" );
    window.disableScreenDiv.style.display = "block";
    window.disableScreenDiv.style.position = "fixed";
    window.disableScreenDiv.style.top = "0px";
    window.disableScreenDiv.style.left = "0px";
    window.disableScreenDiv.style.right = "0px";
    window.disableScreenDiv.style.bottom = "0px";
    window.disableScreenDiv.style.textAlign = "center";
    window.disableScreenDiv.style.zIndex = 99999999;
    window.disableScreenDiv.style.visibility = "hidden";
    window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;background-color:#ffffff;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
        '<div class="ajax_loader_icon_inner" style="width: 32px; height: 32px;margin: 0 auto;background-image: url(\'/engine/shared/img/ajax-loader.gif\');position:absolute;top:50%;left:50%;margin:-16px;z-index:2;"></div>';
    window.disableScreenDiv.autoHide = true;
    document.body.appendChild(window.disableScreenDiv);
}

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
            checkout_data.payment_ref       = document.getElementById("payment_ref").value;
            checkout_data.payment_total     = document.getElementById("payment_total").value;
            checkout_data.comments          = document.getElementById("comments").value;

            //Name and address
            checkout_data.ccName    = document.getElementById("ccName").value;
            checkout_data.phone     = document.getElementById("phone").value;
            checkout_data.email     = document.getElementById("email").value;
            checkout_data.course_name     = document.getElementById("course_name").value;
            checkout_data.student_name     = document.getElementById("student_name").value;
            checkout_data.location     = document.getElementById("location").value;
            if(document.getElementById("recaptcha_response_field"))
            {
                checkout_data.recaptcha_response_field   =  document.getElementById("recaptcha_response_field").value;
            }
            else
            {
                checkout_data.recaptcha_response_field   =  "";
            }

            if(document.getElementById("recaptcha_challenge_field"))
            {
                checkout_data.recaptcha_challenge_field   =  document.getElementById("recaptcha_challenge_field").value;
            }
            else
            {
                checkout_data.recaptcha_challenge_field   =  "";
            }

			if (document.getElementById('payment')['g-recaptcha-response'])
			{
				checkout_data['g-recaptcha-response'] = document.getElementById('payment')['g-recaptcha-response'].value;
			}

            //Credit Card Payment Details
            checkout_data.ccType  =  document.getElementById("ccType").options[document.getElementById("ccType").selectedIndex].value;
            checkout_data.ccNum   =  document.getElementById("ccNum").value.replace(/[\s\-]/g,'');
            checkout_data.ccv     =  document.getElementById("ccv").value;
            checkout_data.ccExpMM =  document.getElementById("ccExpMM").options[document.getElementById("ccExpMM").selectedIndex].value;
            checkout_data.ccExpYY =  document.getElementById("ccExpYY").options[document.getElementById("ccExpYY").selectedIndex].value;
            checkout_data = JSON.stringify(checkout_data);

            var submit_status = $("#payment").validationEngine('validate');

            if(submit_status)
            {
                window.disableScreenDiv.style.visibility = "visible";
                $.post('/frontend/payments/payment_processor_ib_pay',{checkout:checkout_data},function(data){

                    if(data.status == 'success')
                    {
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

        var valid = $('#selectcform').validationEngine('validate');

        if (valid)
        {
            var schedule_selector = $("#schedule_selector");
            var id = schedule_selector.val();
            var event_id = schedule_selector.find(':selected').data('event_id');
            var title = $(this).data("title");
            var href = "/booking-form/"+title+".html/?id="+id+'&eid='+event_id;
            window.location = "http://"+window.location.host+href;
            return false;

        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });
    $("#schedule_selector").on("change", function () {
        $("#selectc").html($('#schedule_selector option:selected').text());
    });
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
                    $("#enquire-course, #book-course").prop("disabled", false);
                }
                return false;
            }, "json");
        }

    }));

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
        if ($("#guardian_email").val().length > 5) {
            $("#guardian_mobile").removeClass("validate[required]");
            $("#guardian_mobile_require").remove();
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

    $("#submit-newsletter").click(function (ev) {
        ev.preventDefault();
        var valid = ($("#form-newsletter").validationEngine('validate'));
        if (valid) {
            $('#form-newsletter').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
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

    $(document).on('click', '.formError', function()
    {
        $(this).fadeOut();
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
