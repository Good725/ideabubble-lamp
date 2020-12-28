$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
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




});