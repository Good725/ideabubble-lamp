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

    if (document.getElementById('search_filter_make'))
    {
        update_filters();
        $('#search_filter_make').on('change',update_filters);
    }
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

$(window).resize(function()
{
    var window_width = window['innerWidth'];
    if(window_width > 320 && menu.is(':hidden')) {
        menu.removeAttr('style');
    }
});

function update_filters()
{
    var make  = document.getElementById('search_filter_make').value;
    var model = document.getElementById('search_filter_model').value;
    $.post('/frontend/cars/ajax_get_models_by_make/?make='+make+'&current='+model, function(results)
    {
        if (typeof results == 'string')
        {
            document.getElementById('search_filter_model').innerHTML = '<option value="">Select Model</option>'+results;
        }
    });
}

