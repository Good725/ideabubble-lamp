$(document).ready(function()
{

    // Validate any formbuilder forms and any forms with the class "validate-on-submit"
    $('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function(ev) {
        ev.preventDefault();
        var valid = $(this).validationEngine('validate');

        // If the form has a CAPTCHA and the CAPTCHA has not been filled out, display a validation error
        var $captcha_iframe = $(this).find('iframe[src*="/recaptcha/"]');
        if ($captcha_iframe.length && grecaptcha && grecaptcha.getResponse().length == 0) {
            $captcha_iframe.validationEngine('showPrompt', '* This field is required', 'load', 'topLeft', true);
            valid = false;
        }

        if (valid) this.submit();
    });

    $('.menu-icon').on('click', function(ev) {
        ev.preventDefault();
        $(this).parents('.top-bar').toggleClass('expanded');
    });


    var $banner = $('#page-banner-swiper');
	if ($banner.length && $banner.data('slides') > 1)
    {
        var banner_swiper = new Swiper('#page-banner-swiper', {
            autoplay: {
                delay: ($banner.data('autoplay') || true)
            },
            direction: $banner.data('direction'),
            effect: $banner.data('effect'),
            speed: $banner.data('speed'),
            loop: true,
            pagination: {
                el: '#page-banner-swiper .swiper-pagination',
                clickable: true
            },
            navigation: {
                prevEl: '#page-banner-swiper .swiper-button-prev',
                nextEl: '#page-banner-swiper .swiper-button-next'
            }
        });
    }

});