$(document).ready(function () {
    $('.alert, .checkout_message_error').on('click', '.close', function () {
        $(this).parent().remove();
    });

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

	// Validate any formbuilder forms and any forms with the class "validate-on-submit"
	$('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function()
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});

	// Expand/Collapse footer menus
	$('.footer > li > a, .contact_us, .connect_with_us_wrapper > h5').on('click', function(ev)
	{
		if ($(window).width() < 700)
		{
			ev.preventDefault();
			var $section;
			if ($(this).hasClass('contact_us'))
			{
				$section = $('.contact_us + address, .contact_us + address + div');
			}
			else if ($(this).parents('.connect_with_us_wrapper').length > 0)
			{
				$section = $(this).find('\+ div');
			}
			else
			{
				$section = $(this).parent('li').find('ul');
			}

			$section.is(':visible') ? $section.hide() : $section.show();
		}
	});
	$('[name="payment_method"]:checked').trigger('change');

	// Toggle visibility of donation form fields
	$('[name="donation_type"]').on('change', function()
	{
		var $form                     = $(this).parents('form');
		var donation_type             = $('[name="donation_type"]:checked').val();
		var $amount_fieldset          = $('#payment_form_amount_fieldset'); // step 1
		var $contact_details_fieldset = $('#payment_form_contact_details_fieldset'); // step 2
		var $payment_method_fieldset  = $('#payment_form_payment_select_fieldset'); // step 3 a
		var $payment_details_fieldset = $('#payment_form_payment_details_fieldset'); // step 3 b
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


});

// Always show footer menus on >= 700 viewports
$(window).on('resize', function()
{
	if ($(window).width() >= 700)
	{
		$('.footer > li > ul, .contact_footer > address, .contact_footer > div, .connect_with_us_wrapper > h5 + div').css('display', '');
	}
});

$(function () {
    var pull = $('#pull');
    menu = $('#main_menu').find('ul.main');
    menuHeight = menu.height();

    $(pull).on('click', function (e) {
        e.preventDefault();
        menu.slideToggle();
    });
});
