$('#registration_form_submit').on('click', function(ev)
{
    ev.preventDefault();
    var form  = $('#customer_registration_form');
    var valid = form.validationEngine().validationEngine('validate');

    if (valid)
    {
		var submit_button = this;
		submit_button.innerHTML = "Please wait...";
		submit_button.disabled = true;
        $.ajax({url: '/frontend/extra/register_customer', data: form.serialize(), type: 'post', dataType: 'json' }).done(function(results)
        {
            if (results['status'] == 'success')
            {
                window.location = '/registration-successful.html';
            }
            else
            {
                $('#customer_registration_form').prepend('<div class="error_message">'+results['message']+'<div class="dismiss">&times;</div></div>');
				submit_button.innerHTML = "Create Account";
				submit_button.disabled = false;
            }
        });
    }
});

$('.service-form').on('click', '.error_message .dismiss', function()
{
    $(this).parents('.error_message').remove();
});

$('.service-tabs').find('li a').on('click', function(ev)
{
    ev.preventDefault();
    $(this).parents('ul').find('.active').removeClass('active');
    $(this).parent('li').addClass('active');
    var selected_tab_pane = $($(this).attr('href'));
    selected_tab_pane.parent().find('.service-tab-pane').removeClass('active');
    selected_tab_pane.addClass('active');
});
