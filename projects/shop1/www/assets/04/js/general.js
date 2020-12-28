$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    move_sidebar();
});

$(function() {
    var pull        = $('#pull');
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
