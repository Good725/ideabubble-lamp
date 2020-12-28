$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    move_sidebar();

    $("#form-newsletter").on('submit', function(ev) {
        var $captcha_section = $('#newsletter-captcha-section');

        // If the CAPTCHA has been filled out, fill out the field checked by the validation
        if (typeof grecaptcha != 'undefined' && grecaptcha.getResponse().length !== 0) {
            $('#form-newsletter-captcha-hidden').val(1);
        }

        if ($captcha_section.length && $captcha_section.hasClass('hidden')) {
            // CAPTCHA exists but isn't visible
            // Make the CAPTCHA visible. User will need to submit the form again, after filling it out.
            $captcha_section.removeClass('hidden');
            return false;
        }
        else if (!$("#form-newsletter").validationEngine('validate')) {
            // Form fields failed validation
            setTimeout(removeBubbles, 5000);
            return false;
        }
        else {
            // Form is valid
            $('#form-newsletter').attr('action', '/frontend/formprocessor').submit();
        }
    });

	$('[action="frontend/formprocessor/"], [action="frontend/formprocessor"]').on('submit', function(ev)
	{
		ev.preventDefault();
		if ($(this).validationEngine('validate'))
		{
			this.submit();
		}
		else
		{
			setTimeout('removeBubbles()', 5000);
		}
	});

	$('#booking_form_service').on('change', function()
	{
		var service = this.value.toLowerCase();
		var $service_type = $('#booking_form_service_type');

		if (service != 'fire and security')
		{
			$service_type.find('[data-service]').hide().prop('disabled', true);
			$service_type.find('[data-service="'+service+'"]').show().prop('disabled', false);
			$service_type.find(':selected:disabled').prop('selected', false)
		}
	}).trigger('change');

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

function move_sidebar()
{
    var window_width = $(window).width();
    if (window_width < 960) {
        $('#sideLt').appendTo($('#ct'));
    }
    else {
        $('#sideLt').prependTo($('#main'));
    }
}