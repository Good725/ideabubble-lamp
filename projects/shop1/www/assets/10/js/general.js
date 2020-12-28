$(document).ready(function()
{
    $('#row_shipping_email').remove();
    $('#ccName').removeClass('"validate[required]');
    $('#full_name_row').hide();
    $('#first_name_row').show();
    $('#last_name_row').show();
    $('#first_name').addClass('validate[required] text-input');
    $('#last_name').addClass('validate[required] text-input');
    $('#template_name').val('Invoice');
    $('#ccName').val($('#first_name').val()+' '+$('#last_name').val());

    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    $(document).on('change','#first_name',function(){
        $('#ccName').val($('#first_name').val()+' '+$('#last_name').val());
    });
    $(document).on('change','#last_name',function(){
        $('#ccName').val($('#first_name').val()+' '+$('#last_name').val());
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

    jo_zoom_init();
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

function jo_zoom_init() {
    $('.thumb_product_image img').each(function(i,e){
        $(e).bind('mouseover', function(){
            jo_zoom_over(this);
        });
        $(e).bind('mouseout', function(){
            jo_zoom_out(this);
        });
        $(e).bind('mousemove', function(event){
            jo_zoom_move(this, event);
        });
    });
}

function jo_zoom_over(e) {
    $(e).css('height', '250px');
    $(e).css('width', '250px');
    $(e).css('position', 'relative');
}

function jo_zoom_out(e) {
    $(e).css('height', '158px');
    $(e).css('width', '158px');
    $(e).css('top', '0px');
    $(e).css('left', '0px');
}

function jo_zoom_move(e, event) {
    var left = (event.pageX - $(e).position().left) * -0.6;
    var top  = (event.pageY - $(e).position().top ) * -0.6;
    if (left < -92) left = -92;
    if (top  < -92) top  = -92;
    $(e).css('left', left);
    $(e).css('top', top);
}