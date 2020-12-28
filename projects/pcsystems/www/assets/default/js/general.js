$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    $("#submit-newsletter").click(function (ev) {
        ev.preventDefault();
        var valid = ($("#form-newsletter").validationEngine('validate'));
        if (valid) {
            $('#form-newsletter').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }
    });

    $('#header-menu-expand').on('click', function(ev)
    {
        ev.preventDefault();
        $('#header-menu').slideToggle();
    });

    $('.submenu-expand').on('click', function(ev)
    {
        ev.preventDefault();
        var $submenu = $(this).find('\+ ul');
        if ($submenu.is(':visible'))
        {
            $(this).removeClass('expanded');
        }
        else
        {
            $(this).addClass('expanded');
        }
        $submenu.slideToggle();
    });

    $('#sidebar-expand').on('click', function(ev)
    {
        ev.preventDefault();
        var $sidebar      = $('#sidebar');
        var $sidebarInner = $('#sidebar-inner');
        var $content      = $('#ct');
        var banner_width  = $('.cs_sequence_item').width();

        if ($sidebarInner.is(':visible'))
        {
            $sidebar.removeClass('sidebar-expanded');
            $content.removeClass('content-shrunk');
        }
        else
        {
            $sidebar.addClass('sidebar-expanded');
            $content.addClass('content-shrunk');
        }
        $(window).resize();

    });

    $('#account_registration_form').find('button').on('click', function(ev)
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
                        $('.registration').load('/login.html .registration', function()
                        {
                            $('.registration').first().prepend(message);
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

    $('.body-sub-menu-expand').on('click', function(ev)
    {
        ev.preventDefault();
    });

    $("#price_slider")
        .noUiSlider({
            start: [0, 5000],
			
            range: {'min': 0, '50%': 1000, '75%': 4000, 'max': 9999},
            format: wNumb({decimals: 0})
        })
        .Link('lower').to($('#minprice'))
		.Link('upper').to($('#maxprice'))
		.Link('lower').to($('#price-min .num'))
        .Link('upper').to($('#price-max .num'));


    $('#checkout_delivery_method').on('change', function()
    {
        // Change text and click event on checkout submit button,
        // when the user is reserving, rather than paying
        var $button = $('#submit_checkout_button').removeAttr('onclick').off();

        $button.html('Buy Now').on('click', function()
        {
                submitCheckout();
        });

        // Toggle the display of the PO number input
        if (this.value == 'credit_account')
        {
            $('#checkout_po_number_wrapper').show();
            $('#paymentSelect, #CardDetails').hide();
        }
        else
        {
            $('#checkout_po_number_wrapper').hide();
            $('#paymentSelect, #CardDetails').show();
        }

    });

    $('#checkout_store').on('change', function()
    {
        if (this.value)
        {
            $('#collect_dialogue').dialog(
            {
                resizable: false,
                draggable: false,
                modal: true,
                buttons: {
                    OK: function() { $(this).dialog('close') }
                }
            });
        }
    });

});