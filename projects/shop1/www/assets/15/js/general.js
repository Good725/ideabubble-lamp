$(document).ready(function () {
    $('.alert, .checkout_message_error').on('click', '.close', function () {
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

    $('#contact-us').submit(function (ev) {
        ev.preventDefault();
        var valid = $(this).validationEngine('validate');
        if (valid) {
            this.submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }
    });

    $('.products_menu .expand').on('click', function (ev) {
        ev.preventDefault();
        var submenu = $(this).find('\+ ul');
        if (submenu.is(':visible')) {
            $(this).removeClass('expanded');
            submenu.hide();
        }
        else {
            $(this).addClass('expanded');
            submenu.show();
        }

    });

    $('#postalZone').on('change', function () {
        var destination = this[this.selectedIndex].innerHTML;
        var country_input = document.getElementById('address_4');
        console.log(destination);
        if (destination == 'Ireland' || destination == 'UK') {
            country_input.value = destination;
        }
        else if (destination == 'Worldwide' && (country_input.value == 'Ireland' || country_input.value == 'UK')) {
            country_input.value = '';
        }
    });

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

$(window).resize(function () {
    var window_width = $(window).width();
    if (window_width > 320 && menu.is(':hidden')) {
        menu.removeAttr('style');
    }

    move_sidebar();
});

function move_sidebar() {
    var window_width = window['innerWidth'];
    if (!document.getElementById('home')) {
        if (window_width < 960) {
            $('#sideLt').appendTo($('#ct'));
        }
        else {
            $('#sideLt').prependTo($('#main'));
        }
    }
    else if (typeof document.getElementById('sideLt') != 'undefined') {
        if (window_width < 960) {
            $('#sideLt').before($('#ct'));
        }
        else {
            $('#sideLt').after($('#ct'));
        }
    }
}
