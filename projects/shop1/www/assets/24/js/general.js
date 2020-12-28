$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

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

	if (typeof checkout_data != 'undefined')
	{
		checkout_data.updateCounterImages = function(){
			$(".decrease_product_amount").attr('src', 'assets/24/images/minus_small.png');
			$(".increase_product_amount").attr('src', 'assets/24/images/plus_small.png');
			$(".cartIcon.delete_product").attr('src', 'assets/24/images/cross_small.png');
		};

		checkout_data.updateCounterImages();
	}


	// Refresh the Facebook widget when the window is resized
	$(window).resize(function()
	{
		if (typeof FB != 'undefined')
		{
			FB.XFBML.parse();
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

$(window).resize(function(){
    var window_width = $(window).width();
    if(window_width > 320 && menu.is(':hidden')) {
        menu.removeAttr('style');
    }

});
