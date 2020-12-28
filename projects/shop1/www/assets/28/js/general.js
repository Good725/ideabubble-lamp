$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    $("#form-newsletter").on('submit', function()
	{
		var $captcha_section = $('#newsletter-captcha-section');

		// If the CAPTCHA has been filled out, fill out the field checked by the validation
		if (grecaptcha && grecaptcha.getResponse().length !== 0)
		{
			$('#form-newsletter-captcha-hidden').val(1);
		}

		if ($captcha_section.length && $captcha_section.hasClass('hidden'))
		{
			// CAPTCHA exists but isn't visible
			$captcha_section.removeClass('hidden');
			return false;
		}
		else if ( ! $("#form-newsletter").validationEngine('validate'))
		{
			// Form fields failed validation
			return false;
        }
        else
		{
			// Form is valid
			$('#form-newsletter').submit();
        }
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

function initialize_product_zoom()
{
	if (document.getElementById('product_image'))
	{
		var $product_image = $('#product_image').find('img');
		// Initiate the plugin and pass the id of the div containing gallery images
		$product_image.elevateZoom(
		{
			gallery: 'product_thumbs_area',
			cursor: 'pointer',
			galleryActiveClass: 'active',
			zoomWindowWidth: 200,
			zoomWindowHeight: 200,
			tint: true,
			tintColour: '#fff',
			tintOpacity: .4,
			responsive: true,
			borderSize: 0,
			lensBorderSize: 0
		});
	}
}
$(document).ready(initialize_product_zoom);