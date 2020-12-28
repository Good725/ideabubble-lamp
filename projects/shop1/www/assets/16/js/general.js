$(document).ready(function () {
    $('.alert, .checkout_message_error').on('click', '.close', function () {
        $(this).parent().remove();
    });


    // hide book button if no schedules available
    var thevalue = 'No schedules available';
    var display = true;
    $('#schedule_selector option').each(function () {
        console.log(this.text + ' = '+thevalue);
        if (this.text == thevalue) {
            display = false;
            return false;
        }
    });
    if (display) {
        $("#course_detail_booking_button").show();
    }
    else {
        $("#course_detail_booking_button").hide();
    }

    menu = $('#main_menu').find('> ul');
    $('#pull').on('click', function (e) {
        e.preventDefault();
        menu.slideToggle();
    });
    $(window).resize(function () {
        var window_width = $(window).width();
        if (window_width > 320 && menu.is(':hidden')) {
            menu.removeAttr('style');
        }
        if (window_width < 960) {

        }
    });

    $('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function(ev)
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});

    $('.products_menu .expand').on('click', function (ev) {
        ev.preventDefault();
        var submenu = $(this).find('\+ ul');
        if (submenu.is(':visible')) {
            $(this).removeClass('expanded');
            submenu.hide();
        }
        else {
            $(this).addClass('expanded');
            submenu.show();
        }
    });


    $('.course_result_booking_button').on('click', function (ev) {
        ev.preventDefault();
        var $form = $('#' + this.getAttribute('data-form'));
        if ($form.validationEngine('validate')) {
            $form.submit();
        }
        else {
            setTimeout(function () {
                $('.formError').fadeOut(function () {
                    $('.formError').remove();
                })
            }, 5000);
        }
    });

    $('.course_result_start_date').on('change', function () {
        var select = this;
        $(select).parents(".course_result").find(".course_result_booking_button").prop("disabled", true);
        var form = this.form;
        var timeslot_id = this.value;
        var course_id = this.getAttribute('data-id');
        var price_wrapper = document.getElementById('course_result_' + course_id + '_price_wrapper');
        price_wrapper.style.visibility = 'hidden';

        if (timeslot_id.length > 0 || timeslot_id == 'all') {
            var schedule_id = this[this.selectedIndex].getAttribute('data-schedule_id');
            $.post(
                '/frontend/courses/ajax_get_schedule_price_and_discount',
                {
                    schedule_id : schedule_id,
                    timeslot_id : timeslot_id
                },
                function (data) {
                    data = JSON.parse(data);
                    if (data.success) {
                        form.schedule_id.value = schedule_id;
                        price_wrapper.getElementsByClassName('course_result_price')[0].innerHTML = (data.is_free) ? 'Free' : '&euro;' + data.fee;
                        price_wrapper.getElementsByClassName('course_result_discount_info')[0].innerHTML = data.discount_info;
                        price_wrapper.style.visibility = 'visible';
                        $(select).parents(".course_result").find(".course_result_booking_button").prop("disabled", false);
                    }
                }
            );
        }
    });

});

jQuery(function () {
    $('#newsfeed_slider').bxSlider({
        mode: 'fade',
        auto: true,
        pager: false,
        controls: false,
        speed: 3000
    });
});
