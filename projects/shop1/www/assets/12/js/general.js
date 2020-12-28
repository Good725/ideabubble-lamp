$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    move_sidebar();

    $("#submit-newsletter").click(function (ev) {
        ev.preventDefault();
        var valid = $("#form-newsletter").validationEngine('validate');
        if (valid) {
            $('#form-newsletter').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }
    });

	// Validate any formbuilder forms and any forms with the class "validate-on-submit"
	$('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function()
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});

	$('.datepicker-input').datetimepicker({
		timepicker: false,
		format: 'Y-m-d'
	});

	$(document).on('click', '.modal-close', function()
	{
		$(this).parents('.modal-wrapper').hide();
	});

	$('#header-login-dropdown-toggle').on('click', function()
	{
		var $dropdown = $('#header-login-dropdown');

		$dropdown.is(':visible') ? $dropdown.hide() : $dropdown.show();
	});


	$('.products_menu .expand').on('click', function(ev)
    {
        ev.preventDefault();
        var submenu = $(this).find('\+ ul');
        if (submenu.is(':visible'))
        {
            $(this).removeClass('expanded');
            submenu.hide();
        }
        else
        {
            $(this).addClass('expanded');
            submenu.show();
        }
    });


	$('#payment_form_day').on('change', function()
	{
		var day = this[this.selectedIndex].value.toLowerCase();

		var time_selectors = document.getElementsByClassName('paymentform-time-li');
		var select_list;

		// Hide time selectors
		for (var i = 0; i < time_selectors.length; i++)
		{
			time_selectors[i].style.display = 'none';
			select_list = time_selectors[i].querySelector('select');
			select_list.disabled = true;
			select_list.selectedIndex = '';
		}

		// Show the time selector for the chosen date
		var relevant_time_selector = document.getElementById('payment_form_times_'+day);
		relevant_time_selector.disabled = false;
		$(relevant_time_selector).parents('.paymentform-time-li').show();
	});

	$('.course_result-start_date').on('change', function () {
		var form = this.form;
		var timeslot_id = this.value;
		var $course = $(this).parents('.course_result');
		var $discount = $course.find('.course-discount');

		if (timeslot_id.length > 0 || timeslot_id == 'all')
		{
			var schedule_id = this[this.selectedIndex].getAttribute('data-schedule_id');
			form.schedule_id.value = schedule_id;
			$.post(
				'/frontend/courses/ajax_get_schedule_price_and_discount',
				{
					schedule_id : schedule_id,
					timeslot_id : timeslot_id
				},
				function (data) {
					data = JSON.parse(data);
					if (data.success)
					{
						$course.find('.course_result-price').html((data.is_free) ? 'Free' : '&euro;' + data.fee);
						$course.find('.course-discount_info').html(data.discount_info);
						(data.discount_info.trim()) ? $discount.show() : $discount.hide();
					}
					else
					{
						$discount.hide();
					}
				}
			);
		}
	});

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
});

$(function() {
    var pull    = $('#pull');
    menu        = $('#main_menu').find('ul.main');
    menuHeight  = menu.height();

    $(pull).on('click', function(e) {
        e.preventDefault();
        menu.slideToggle();
    });
});

$(window).resize(function(){
    var window_width = $(window).width();
    if(window_width > 320 && menu.is(':hidden')) {
        menu.removeAttr('style');
    }

    move_sidebar();
});

function move_sidebar()
{
    var window_width = window['innerWidth'];
    if (window_width < 960) {
        $('#sideLt').detach().appendTo($('#ct'));
    }
    else {
        $('#sideLt').detach().prependTo($('#main'));
    }
}