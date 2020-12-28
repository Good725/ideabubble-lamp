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


    $('#account_registration_form').find('[type="submit"]').on('click', function(ev)
    {
        ev.preventDefault();
        var form  = $('#account_registration_form');

        if (form.validationEngine('validate'))
        {
            $.ajax({url: '/frontend/users/ajax_register_user', data: form.serialize(), type: 'post', dataType: 'json' })
                .success(function(results)
                {
                    var message = '<div class="alert alert'+results.status+'"><a class="close" data-dismiss="alert">&times;</a>'+
                        '<strong>'+results.status.charAt(0).toUpperCase()+results.status.substring(1)+':</strong> '+results.message+'</div>';

                    if (results.status == 'success')
                    {
                        $('#ct').load('/login.html #ct .content', function()
                        {
                            $('#ct').prepend(message);
                        });
                    }
                    else
                    {
                        form.before(message);
                    }
                })
                .fail(function()
                {
                    form.before('<div class="alert alert-error">' +
                        '<a class="close" data-dismiss="alert">&times;</a>' +
                        '<strong>Error</strong> Internal server error. Please try again later.' +
                        '</div>');
                });
        }
    });

    $('#login_button').on('click', function(ev)
    {
        ev.preventDefault();
        var form = $('#login_form');
        if (form.validationEngine('validate'))
        {
            form.submit();
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

function move_sidebar() {
    var window_width = $(window).width();
    if (window_width < 960) {
        $('#sideLt').appendTo($('#ct'));
    }
    else {
        $('#sideLt').prependTo($('#main'));
    }
}


