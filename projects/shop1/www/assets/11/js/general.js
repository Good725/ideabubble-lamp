$(document).ready(function(){

    $('#row_shipping_email').hide();
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    $(document).on('click','input[type="submit"]',function(e){
        e.preventDefault();
        var form = $(this).closest('form');
        var valid = form.validationEngine('validate');
        if(valid)
        {
            form.submit();
        }
        else
        {
            //bleh
        }
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

    $('.menu-expand, .submenu-expand').on('click', function(ev)
    {
        ev.preventDefault();
        var menu = $(this).find('\+ ul');
        menu.is(':visible') ? menu.hide() : menu.show();
    });

    $('[action="frontend/formprocessor/"]').on('submit',function(e){
        e.preventDefault();
        var valid = $(this).validationEngine('validate');
        if(valid)
        {
            this.submit();
        }
        else
        {
            return false;
        }
    })
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
    if ($(window).outerWidth() < 970)
    {
        $('#sideLt').detach().appendTo($('#ct'));
    }
    else
    {
        $('#sideLt').detach().prependTo($('#main'));
    }
}
