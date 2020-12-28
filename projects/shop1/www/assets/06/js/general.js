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


    $(".side_menu [href]").each(function () {
        if (this.href == window.location.href) {
            $(this).closest('li').addClass("menu_active");
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

$(window).resize(function()
{
    var window_width = window['innerWidth'];
    if(window_width > 320 && menu.is(':hidden')) {
        menu.removeAttr('style');
    }
    move_sidebar();

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