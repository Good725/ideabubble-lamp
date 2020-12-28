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
        $('.header-menu').slideToggle();
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
        if (this.value == 'reserve_and_collect')
        {
            $button.html('Reserve').on('click', function()
            {
                show_collect_dialog();
            });
        }
        else
        {
            $button.html('Buy Now').on('click', function()
            {
                submitCheckout();
            });
        }

        // Toggle the display of the PO number input
        if (this.value == 'credit_account')
        {
            $('#checkout_po_number_wrapper').show();
            $('#paymentSelect, #CardDetails').hide();
        }
        else if (this.value == 'reserve_and_collect')
        {
            $('#checkout_po_number_wrapper').hide();
            $('#CardDetails, #paymentSelect').hide();
        }
        else
        {
            $('#checkout_po_number_wrapper').hide();
            $('#paymentSelect, #CardDetails').show();
        }

    });

    function show_collect_dialog()
    {
        if ($("#creditCardForm").validationEngine('validate'))
        {
            $('#collect_dialogue').dialog(
            {
                resizable: false,
                draggable: false,
                modal: true,
                buttons: {
                    Reserve: function () { submitCheckout() },
                    Cancel: function() { $(this).dialog('close') }
                }
            });
        }
    }

	$('#shopping_actions_name').on('click', function()
	{
		var $dropout = $('#shopping_actions_dropout');
		if ($dropout.hasClass('visible'))
		{
			$dropout.removeClass('visible');
		}
		else
		{
			$dropout.addClass('visible');
		}
	});

	$('[action="frontend/formprocessor/"]').on('submit', function(ev)
	{
		ev.preventDefault();
		var valid = $(this).validationEngine('validate');
		if (valid)
		{
			this.submit();
		}
		else
		{
			return false;
		}
	});

	// Expand/Collapse footer menus
	$('.footer-company-info-title, .footer-info > li > a').on('click', function(ev)
	{
		if ($(window).width() <= 540)
		{
			ev.preventDefault();
			var $section;
			if ($(this).hasClass('footer-company-info-title'))
			{
				$section = $('.footer-company-info');
			}
			else
			{
				$section = $(this).parent('li').find('ul');
			}

			$section.is(':visible') ? $section.css('display', '') : $section.css('display', 'block');
		}
	});
});
