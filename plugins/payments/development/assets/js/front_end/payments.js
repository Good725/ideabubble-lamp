$(document).ready(function()
{
	var payment_form_submitting = false;
	$('#payment_form, #quick_payment_form').after('<div id="payment-form-blackout" style="display:none;position: fixed;top: 0;bottom: 0;right: 0;left: 0;background: rgba(0,0,0,.3);z-index: 10000;"></div>')

    // Add PayPal button, after the regular submit button. The two will be toggled between
    if (document.querySelector('[name="payment_method"][value="PayPal"]'))
    {
        $('#pay_online_submit_button').after(
            '<a id="paypal_payment_button" href="#" style="display: none;">'+
                '<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="PayPal Checkout" />'+
            '</a>'
        );
    }

    // Remove Realex fields, if Realex is not enabled
	if ($('[name="payment_method"][value="Realex"], [name="payment_method"][value="Realex"] + label').length > 0) {
		$.ajax({
			url: '/frontend/payments/ajax_check_for_realex',
			success: function (data) {
				if (data == 0) {
					var $payment_form = $('#payment_form, #quick_payment_form');
					$('#payment_form_cc_payment_fieldset').remove();
					$payment_form.find('[name="payment_method"][value="Realex"], [name="payment_method"][value="Realex"] + label').remove();
					$payment_form.find('#pay_online_submit_button').remove();
					$payment_form.find('#paypal_payment_button').show();
					$payment_form.find('[name="payment_method"][value="PayPal"]').prop('checked', true);
				}

			}
		});
	}

    // Remove Paypal fields, if Paypal is not enabled
    if ($('[name="payment_method"][value="PayPal"], [name="payment_method"][value="PayPal"] + label').length > 0) {
        $.ajax({
            url: '/frontend/payments/ajax_check_for_paypal',
            success: function (data) {
                if (data == 0) {
                    var $payment_form = $('#payment_form, #quick_payment_form');
                    $payment_form.find('[name="payment_method"][value="PayPal"], [name="payment_method"][value="PayPal"] + label').remove();
                }

            }
        });
    }

	// Stripe
	if (document.getElementById('stripe-button'))
	{
		var stripe_button = document.getElementById('stripe-button');
		var form          = ($(stripe_button).parents('form').length > 0) ? $(stripe_button).parents('form')[0] : null;
		var handler       = StripeCheckout.configure(
			{
				key   : stripe_button.getAttribute('data-key'),
				token : function(token)
				{
					var post_data = get_payment_form_data(form);
					post_data.token = token;
					// multiply the price by 100 to convert from cents to Euro
					post_data.price = 100 * ((document.getElementById('payment_total')) ? document.getElementById('payment_total').value : 0);
					post_data.payment_type = 'stripe';

                    // Stop the blackout from automatically dismissing when the AJAX request is done.
                    // It should continue until the user has been redirected
                    prevent_blackout_dismissal();

					$.ajax({
						url      : '/frontend/payments/payment_processor_ib_pay',
						data     : post_data,
						type     : 'post',
						dataType : 'json',
						async    : true
					})
						.done(function(data)
						{
                            if (data.status == 'success')
                            {
                                if (typeof cms_ns != 'undefined') {
                                    // Form has been successfully submitted. These are no longer considered unsaved changes
                                    // This will stop the unsaved changes warning blocking the redirect.
                                    cms_ns.modified = false;
                                }

                                location.href = data.redirect;
                            }
							else
							{
                                if (window.disableScreenDiv) {
                                    dismiss_blackout();
                                }

                                $('#error_message_are, #checkout_messages').html('<div class="alert alert-danger checkout_message"><a href="#" class="close">×</a>'+ data.message +'</div>');

								console.log('Error: ' +data.message);
							}
						})
						.fail(function(data)
						{
                            dismiss_blackout();

                            $('#error_message_are, #checkout_messages').html('<div class="alert alert-danger checkout_message"><a href="#" class="close">×</a>Unexpected internal error. If this problem continues, please contact the administration.</div>');

                            console.log(data);
							console.log('Error: Network error.');
						});
				},
                closed: function() {
                    // Check if it closed because the user is making a payment, rather than them clicking the close icon.
                    if (this.key) {
                        // Show the blackout
                        if (window.disableScreenDiv) {
                            window.disableScreenDiv.style.visible = 'visible';
                            prevent_blackout_dismissal();
                        }
                    }
                }
			});

		stripe_button.addEventListener('click', function(ev)
		{
			ev.preventDefault();

			var submit_status = $(stripe_button).parents('form').validationEngine('validate');
			if (submit_status)
			{
				handler.open({
					description : 'Payment',
					amount      : 100 * ((document.getElementById("payment_total")) ? document.getElementById("payment_total").value : 0),
					currency    : 'eur'
				});

			}

		});
	}
	// Stripe - end

    function prevent_blackout_dismissal()
    {
        if (window.disableScreenDiv) {
            window.disableScreenDiv.autoHide = false;
        }
    }

    function dismiss_blackout()
    {
        if (window.disableScreenDiv) {
            window.disableScreenDiv.autoHide = true;
            window.disableScreenDiv.style.visibility = 'hidden';
        }
    }

    $('#payment_form, #quick_payment_form').on('submit', function(ev)
    {
		ev.preventDefault();

		// Prevent double form submissions
		// Check that we are not in the middle of a form submission, before submitting again
		if ( ! payment_form_submitting)
		{
			payment_form_submitting = true;
			var $blackout = $('#payment-form-blackout');
			$blackout.show();

			if ($(this).validationEngine('validate'))
			{
				try
				{
					var post_data = get_payment_form_data(this);

                    // The blackout is to continue, even after the AJAX request is complete
                    // It should continue until the user has been redirected
                    prevent_blackout_dismissal();

                    $.post('/frontend/payments/payment_processor_ib_pay', post_data, function(data) {
                        if (data.status == 'success') {
							location.href = data.redirect;
						}
						else
						{
							checkout_data = '';
							$('.cc-data').val('');
							$("#error_message_area").html('Error: '+data.message);
							payment_form_submitting = false;
							$blackout.hide();
						}
					},'json')
						.fail(function(data)
						{
							checkout_data = '';
							$('.cc-data').val('');
							$("#error_message_area").html('Error: Network error, please check your internet connection');

							payment_form_submitting = false;
							$blackout.hide();
                            dismiss_blackout();
						});

					checkout_data = '';
				}
				catch(error)
				{
					console.log(error);
					payment_form_submitting = false;
					$blackout.hide();
                    dismiss_blackout();
				}
			}
			else
			{
				setTimeout('removeBubbles()', 5000);
				payment_form_submitting = false;
				$blackout.hide();
			}
		}
    });

    // Select a payment method on the initial page load, to ensure the right content is visible
    $('[name="payment_method"]:checked').trigger('change');

    // When the payment method is changed, toggle what content is available
    $('[name="payment_method"]').on('change', function()
    {
        var method = $('[name="payment_method"]:checked').val();

        if (!method && $('[name="payment_method"]').length == 1) {
            method = $('[name="payment_method"]').val();
        }

		$('#pay_online_submit_button').hide();
		$('#payment_form_cc_payment_fieldset').hide();
		$('#paypal_payment_button').hide();
		$('#stripeButton').hide();

        switch (method)
		{
			case 'PayPal':
				$('#paypal_payment_button').show();
				break;
			case 'Stripe':
				$('#stripeButton').show();
				break;
			case 'Realex':
				$('#pay_online_submit_button').show();
				$('#payment_form_cc_payment_fieldset').show();
		}
    }).trigger('change');
});

$(document).on('click', '#paypal_payment_button', function(ev)
{
    ev.preventDefault();
    var $form = $(this).parents('form');
    var valid = $form.validationEngine('validate');
    if (valid)
    {
        $.post('/frontend/payments/ajax_get_paypal_form', {form_data: $form.serialize()}, function(results)
        {
            results = JSON.parse(results);
            var status = results.status;
            var data   = results.data;
            var whereDisplay;
            if (typeof payPalRedirect == "undefined" || payPalRedirect != 0)
            {
                whereDisplay = 'body';
            }
            else if (payPalRedirect == 0)
            {
                var newWindow = window.open('', 'PayPal', "scrollbars=1,height=500,width=980");
                var newWindowBody = newWindow.document.body;

                newWindowBody.style.background = 'url("/assets/default/images/loading.gif") no-repeat center 200px';
                whereDisplay = newWindowBody;
            }

            var $paypal_form = $('<form id="paypal_form_'+$form.attr('id')+'" method="post" action="https://www.'+(data.test_mode ? 'sandbox.' : '')+'paypal.com/cgi-bin/webscr">').appendTo(whereDisplay);
            for (var property in data)
            {
                $paypal_form.append($('<input type="hidden" name="'+property+'" value="'+data[property]+'" />'));
            }
            $paypal_form.submit();
        });
    }
});

// Get form data as an object
$.fn.serializeObject = function()
{
    'use strict';

    var result = {};
    $.each(this.serializeArray(), function (i, element)
    {
        var field = result[element.name];

        if (typeof field !== 'undefined' && field !== null)
        {
            ($.isArray(field))
                ? field.push(element.value) // if there are multiple inputs with the same name e.g. checkboxes
                : result[element.name] = [node, element.value]
            ;
        }
        else
        {
            result[element.name] = element.value;
        }
    });

    return result;
};

function get_payment_form_data(form)
{
	var post_data               = $(form).serializeObject();
	var checkout_data           = checkout_data || {};

	checkout_data.payment_ref   = (document.getElementById("payment_ref"))   ? document.getElementById("payment_ref").value   : '';
	checkout_data.payment_total = (document.getElementById("payment_total")) ? document.getElementById("payment_total").value : '';
	checkout_data.comments      = (document.getElementById("comments"))      ? document.getElementById("comments").value      : '';
	checkout_data.ccName        = (document.getElementById("ccName"))        ? document.getElementById("ccName").value        : '';
	checkout_data.phone         = (document.getElementById("phone"))         ? document.getElementById("phone").value         : '';
	checkout_data.email         = (document.getElementById("email"))         ? document.getElementById("email").value         : '';
	checkout_data.purchase_order_reference = (document.getElementById("checkout_purchase_order_reference")) ? document.getElementById("checkout_purchase_order_reference").value   : '';

	// CAPTCHA details
	checkout_data.recaptcha_response_field  = (document.getElementById("recaptcha_response_field"))  ? document.getElementById("recaptcha_response_field").value  : '';
	checkout_data.recaptcha_challenge_field = (document.getElementById("recaptcha_challenge_field")) ? document.getElementById("recaptcha_challenge_field").value : '';
	checkout_data['g-recaptcha-response']   = post_data['g-recaptcha-response'];

	// Credit Card payment details
	checkout_data.ccType  = $("#ccType").val();
	checkout_data.ccNum   = (document.getElementById("ccNum")) ? document.getElementById("ccNum").value : '';
	checkout_data.ccv     = (document.getElementById("ccv"))   ? document.getElementById("ccv").value   : '';
	checkout_data.ccExpMM = $("#ccExpMM").val();
	checkout_data.ccExpYY = $("#ccExpYY").val();
	checkout_data         = JSON.stringify(checkout_data);

	post_data.checkout    = checkout_data;

	return post_data;
}