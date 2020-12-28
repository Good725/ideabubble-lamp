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
			$(".decrease_product_amount").attr('src', 'assets/25/images/minus_small.png');
			$(".increase_product_amount").attr('src', 'assets/25/images/plus_small.png');
			$(".cartIcon.delete_product").attr('src', 'assets/25/images/cross_small.png');
		};

		checkout_data.updateCounterImages();
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
