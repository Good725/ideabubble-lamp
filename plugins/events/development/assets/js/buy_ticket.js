function disableScreenShow()
{
	if(!window.disableScreenDiv){
		window.disableScreenDiv = document.createElement( "div" );
		window.disableScreenDiv.style.display = "block";
		window.disableScreenDiv.style.position = "fixed";
		window.disableScreenDiv.style.top = "0px";
		window.disableScreenDiv.style.left = "0px";
		window.disableScreenDiv.style.right = "0px";
		window.disableScreenDiv.style.bottom = "0px";
		window.disableScreenDiv.style.textAlign = "center";
		window.disableScreenDiv.style.zIndex = 99999999;
		window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;background-color:#000000;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
			'<img src="/engine/shared/img/ajax-loader.gif" style="position:absolute;top:50%;left:50%;margin:-16px;z-index:2;" alt="processing..."/>';

		document.body.appendChild(window.disableScreenDiv);
	}
	window.disableScreenDiv.style.visibility = "visible";
}

function disableScreenHide()
{
	if (window.disableScreenDiv) {
		window.disableScreenDiv.style.visibility = "hidden";
	}
}

/*
 * Older template uses foundation modals. Newer template uses in-house.
 * Run all modal functions through this function to ensure the modal JS corresponding to the template is used.
 */
function checkout_modal(modal, action)
{
    if (typeof $.fn.foundation == 'function' && modal.hasClass('reveal')) {
        modal.foundation(action);
    }
    else if (typeof $.fn.ib_modal == 'function') {
       modal.ib_modal(action);
    }
}


var countries = JSON.parse($('#checkout-country_json').val());

$(document).on("ready", function(){

    $("#use_payment_plan_yes").on("click", function(){
        $(".payment_plan_yes").removeClass("hidden");
        $(".payment_plan_no").addClass("hidden");
    });

    $("#use_payment_plan_no").on("click", function(){
        $(".payment_plan_no").removeClass("hidden");
        $(".payment_plan_yes").addClass("hidden");
    });

    $('#checkout-discount-apply').on('click', refresh_breakdown);

    var $promo_field = $('#checkout-promo_code');

    $promo_field.on('change', function(ev) {
        ev.preventDefault();
        refresh_breakdown();
    });

    // If the user got to this page, after submitting the login form, remember the promo code
    if (window.localStorage && window.URLSearchParams) {
        var login_form_submitted = (new URLSearchParams(window.location.search).get('login') == 1);

        if (login_form_submitted && localStorage.getItem('checkout_promo_code')) {
            $promo_field.val(localStorage.getItem('checkout_promo_code')).trigger('change');
        }
        else {
            localStorage.setItem('checkout_promo_code', '');
        }
    }

    function refresh_breakdown()
    {
        disableScreenShow();
        $.ajax(
            '/frontend/events/refresh_breakdown',
            {
                type    : 'POST',
                data    : $('#checkout, .checkout-form').serialize(),
                success : function(data) {
                    data = JSON.parse(data);

                    if (data.error) {
                        $('#checkout-error_modal-message').html(data.error_message);
                        checkout_modal($('#checkout_error_modal'), 'open');
                    }
                    else
                    {
                        $('#checkout-discount-label').text(data.discount_label);
                        $('#discount-display').text(data.discount.toFixed(2));
                        $('#fees-display').text(data.commission.toFixed(2));
                        $('#vat-display').text(data.vat.toFixed(2));
                        $('#total-display').text(data.total.toFixed(2));

                        $promo_field.validationEngine('hide');

                        if (data.discount > 0) {
                            $('#checkout-discount-wrapper').removeClass('hidden');
                        } else {
                            $('#checkout-discount-wrapper').addClass('hidden');

                            if ($promo_field.val()) {
                                $promo_field.validationEngine('showPrompt', '* Invalid discount code', null, 'topLeft', true);
                            }
                        }
                    }

                    disableScreenHide();
                },
                error: order_hang_check
            }
        );
    }

	$('#checkout, .checkout-form').find('[name=action][value=buy]').on('click', function(ev) {
		ev.preventDefault();
		if (($("#checkout-email").val() || '').trim() == "") {
		    $("#checkout-email").removeClass("validate[required,custom[email]]").addClass("validate[required]");
		}else{
			$("#checkout-email").removeClass("validate[required]").addClass("validate[required,custom[email]]");
		} 
		if ($(this).parents('form').validationEngine('validate'))
		{
			var changed = false;

            // '#checkoutDetails :input' is for legacy support
			var $fields = $('#checkoutDetails :input, .checkout-form [data-saveable]');

            $fields.each(function()
			{
				switch (this.nodeName)
				{
					case 'SELECT':
						if (this[this.selectedIndex] != this.querySelector('[selected]')) changed = true;
						break;

					case 'TEXTAREA':
						if (this.value != this.innerHTML) changed = true;
						break;

					default:
						if (this.value != this.getAttribute('value')) changed = true;
						break;
				}
			});

			if (changed) {
				checkout_modal($('#checkout_save_details_modal'), 'open');
			}
			else {
				submit_checkout(0);
			}
		}
	});

	$('#checkout_save_details_modal').find('button').on('click', function(ev)
	{
		$('#checkout, .checkout-form').find('#saveCheckout').val(ev.target.getAttribute('data-saveCheckout'));
		submit_checkout(0);
	});

    function submit_checkout(skip_duplicate_test)
    {
       checkout_modal($('#checkout_save_details_modal.reveal'), 'close');

        $("#skip_duplicate_test").val(skip_duplicate_test);
        var $form = $('#checkout, .checkout-form');
        var $button = $form.find('[name=action][value=buy]');

        button_text = $button.html();
        $button.html('Processing...');
        window.disableScreenDiv.autoHide = false;
        disableScreenShow();

        var data = $form.serialize();
        email_data = data;
        $.ajax(
            '/frontend/events/process_order?queue_id=' + order_queue_id,
            {
                type: 'POST',
                data: data,
                success: order_response_handler,
                error: order_hang_check
            }
        ).done(function()
            {
                if (typeof grecaptcha != 'undefined') {
                    // If the user needs to submit the form again, they will not be able to use the same CAPTCHA
                    grecaptcha.reset();
                }
            });
    }
    
    $("#checkout_duplicate_modal button.yes").on("click", function(){
        submit_checkout(1);
    });

    $("#checkout_duplicate_modal button.no").on("click", function(){
        checkout_modal($('#checkout_duplicate_modal'), 'close');
    });

    function paymore_update()
    {
        var paymore_amount = $("[name=paymore_amount]").val();
        var partial_payment_id = $("[name=partial_payment_id]").val();
        var use_payment_plan = $("[name=use_payment_plan]").val();
        var paymentplan_id = $("[name=paymentplan_id]").val();
        var total = $("[name=total]").val();
        var items = window.event_items;
        $.ajax(
            '/frontend/events/paymore_update_calculate',
            {
                type: 'POST',
                data: {
                    partial_payment_id: partial_payment_id,
                    amount: paymore_amount,
                    use_payment_plan: use_payment_plan,
                    paymentplan_id: paymentplan_id,
                    total: total,
                    items: items
                },
                success: function (response){
                    for (var i in response) {
                        $("#paymentplan_id_" + response[i].id + " .checkout-group_booking-payment-amount").html(response[i].total);
                    }
                    $("#checkout-breakdown-subtotal").html(response[0].payment_amount);
                    $(".checkout-breakdown-booking_fee").html(response[0].fee);
                    $("#checkout-breakdown-vat").html(response[0].vat);
                    $("#checkout-breakdown-total").html(response[0].total);
                }
            }
        )
    }
    $("#paymore_update").on("click", paymore_update);

    var order_hang_check_attempt = 0;
    var order_hang_check_timeout = null;

    function order_hang_check()
    {
        var $form = $('#checkout, .checkout-form');
        var $button = $form.find('[name=action][value=buy]');

        if (order_hang_check_attempt == 3) {
            disableScreenHide();
            $('#checkout-error_modal-message').html("An Unexpected error happened. Please check your inbox.");
            checkout_modal($('#checkout_error_modal'), 'open');
            $button.html(button_text);
        } else {
            ++order_hang_check_attempt;

            order_hang_check_timeout = setTimeout(
                function() {
                    $.ajax(
                        '/frontend/events/order_hang_check',
                        {
                            type: 'POST',
                            data: {
                                attempt: order_hang_check_attempt
                            },
                            success: function (response) {
                                if (response.last_order_result) {
                                    order_response_handler(response.last_order_result);
                                } else {
                                    order_hang_check();
                                }
                            },
                            error: function(){
                                order_hang_check();
                            }
                        }
                    );
                },
                5000
            );
        }
    }

    var button_text = '';
    var email_data = '';
    var email_process_started = false;

    var check_email_process_attempt = 0;
    function check_email_process_started()
    {
        var $form = $('#checkout, .checkout-form');
        var $button = $form.find('[name=action][value=buy]');
        ++check_email_process_attempt;

        $.post(
            '/frontend/events/email_order_status',
            email_data,
            function (response) {
                if (response.order_status == 'started' || response.order_status == 'completed' || check_email_process_attempt == 10) {
                    window.location = document.getElementById('checkout-success-redirect').value;
                } else {
                    setTimeout(check_email_process_started, 1000);
                }
            }
        );
    }

    function complete_payment_intent(order_response, payment_intent, generate_ticket)
    {
        $.post(
            '/frontend/events/complete_3ds2',
            {
                order: order_response,
                payment_intent: payment_intent
            },
            function (response) {
                if (response.error) {
                    disableScreenHide();
                    $('#checkout-error_modal-message').html(response.error);
                    checkout_modal($('#checkout_error_modal'), 'open');
                    $button.html(button_text);
                } else {
                    if (generate_ticket == 1 || (response.generate_ticket && response.generate_ticket == 1)) {
                        email_data = {order_id: order_response.order_id};
                        // start ticket pdf email generation in background request. goto success without waiting pdf generation(takes time)
                        check_email_process_started();
                        $.post(
                            '/frontend/events/email_order',
                            email_data,
                            function (response) {
                                window.location = document.getElementById('checkout-success-redirect').value;
                            }
                        );
                    } else {
                        window.location = document.getElementById('checkout-success-redirect').value;
                    }
                }
            }
        );
    }

    function order_response_handler(response)
    {
        var $form = $('#checkout, .checkout-form');
        var $button = $form.find('[name=action][value=buy]');

        try {
            if (response.duplicate_warning == 1) {
                $button.html(button_text);
                disableScreenHide();
                $('#checkout_duplicate_modal .duplicate_item').html(response.error);
                checkout_modal($('#checkout_duplicate_modal'), 'open');
            } else if (response.error) {
                disableScreenHide();
                $('#checkout-error_modal-message').html(response.error);
                checkout_modal($('#checkout_error_modal'), 'open');
                $button.html(button_text);
            } else {
                if (response.payment_intent_secret) {
                    disableScreenShow();
                    stripe.handleCardPayment(
                        response.payment_intent_secret,
                        window.stripe_card,
                        {
                            payment_method_data: {
                                billing_details: {
                                    name: $("#checkout-first_name").val() + " " + $("#checkout-last_name").val()
                                }
                            }
                        }
                    ).then(function(result){
                        //console.log(result);
                        if (result.error) {
                            disableScreenHide();
                            $('#checkout-error_modal-message').html(result.error.message);
                            checkout_modal($('#checkout_error_modal'), 'open');
                            $button.html(button_text);
                        } else {
                            if (result.paymentIntent) {
                                complete_payment_intent(response, result.paymentIntent, response.generate_ticket)
                            }
                        }
                    });
                } else {
                    if (typeof fbq === 'function') {
                        fbq('track', 'Purchase', {
                            value: document.getElementById('checkout').total.value,
                            currency: document.getElementById('checkout').currency.value
                        });
                    }

                    if (response.generate_ticket == 1) {
                        email_data = {order_id: response.order_id};
                        check_email_process_started();
                        $.post(
                            '/frontend/events/email_order',
                            email_data,
                            function (response) {
                                window.location = document.getElementById('checkout-success-redirect').value;
                            }
                        );
                    } else {
                        window.location = document.getElementById('checkout-success-redirect').value;
                    }
                }
            }
        } catch (exc) {
            disableScreenHide();
            $('#checkout-error_modal-message').html("An Unexpected error happened. Please check your inbox.");
            checkout_modal($('#checkout_error_modal'), 'open');
            $button.html(button_text);
            console.log(exc);
            $.post(
                '/frontend/frontend/js_error_log',
                {
                    exc: exc.toString()
                },
                function (response) {
                    alert("Contact admin with error log id:" + response.errorlog_id);
                }
            );
        }
    }

    $("[type=checkbox][name=enable_multiple_payers]").on("change", function(){
        if (this.value == 1) {
            if (this.checked) {
                $("#multiple_payers").removeClass("hidden");
                add_multiple_buyer();
            } else {
                $("#multiple_payers").addClass("hidden");
                $("#multiple_payers tbody").html("");
            }
        }
    });

    $("[name=use_payment_plan]").on("change", function(){
        if (this.value != 0) {
            if (this.checked) {
                $("#paymentplan").removeClass("hidden");
            } else {
                $("#paymentplan").addClass("hidden");
            }
        }
    });
});

var $multiple_buyer_template = $("#multiple_payers tbody tr.hidden");
$multiple_buyer_template.remove();
function add_multiple_buyer()
{
    var $tr = $multiple_buyer_template.clone();

    $tr.removeClass("hidden");

    var index = $("#multiple_payers tbody > tr").length;
    $tr.find("input").each (function(){
        if (index == 0) {
            this.readOnly = true;
            if (this.name == "payer[index][name]") {
                this.value = "You";
            }
        }
        this.name = this.name.replace("payer[index]", "payer[" + index + "]");
    });

    $("#multiple_payers tbody").append($tr);
    get_multiple_payer_amounts();
}

function get_multiple_payer_amounts()
{
    $.post(
        "/frontend/events/calculate_multiple_payers",
        {
            amount: $("#total-display, #checkout-breakdown-total").html(),
            payers: $("#multiple_payers tbody > tr").length,
            use_payment_plan: $("[type=checkbox][name=use_payment_plan]").prop("checked") ? $("[type=checkbox][name=use_payment_plan]").val() : 0
        },
        function (response) {
            var $tr = $("#multiple_payers tbody > tr");
            for (var i = 0 ; i < response.length ; ++i) {
                $($tr[i]).find(".amount").val(response[i].amount);
            }
        }
    )
}

var order_queue_id = null;
var warn_queue_count = 6;

function add_order_queue()
{
    $.post(
        '/frontend/events/add_order_queue',
        {

        },
        function (response) {
            order_queue_id = response.id;
            if (response.count >= warn_queue_count) {
                $("button[value=buy]").html("You have been put in a queue. Your position: " + response.count);
                $("button[value=buy]").prop("disabled", false);
            }
            set_check_queue_count();
        }
    );
}

function check_queue_count(callback)
{
    var autohide = window.disableScreenDiv.hide;
    window.disableScreenDiv.hide = false;
    $.post(
        '/frontend/events/check_order_queue',
        {
            id: order_queue_id
        },
        function (response) {
            callback(response);
        }
    );
    window.disableScreenDiv.hide = autohide;
}

var check_queue_count_timeout = null;
function set_check_queue_count()
{
    check_queue_count(
        function(response){
            if (response.count == 0) {
                order_queue_id = null;
                //alert("Your queue has been expired.");
                add_order_queue();
                return;
            }

            if (response.count >= 6) {
                $("button[value=buy]").html("You have been put in a queue. Your position: " + response.count);
                $("button[value=buy]").prop("disabled", true);
            } else {
                if ($("button[value=buy]").prop("disabled")) {
                    $("button[value=buy]").html("Confirm Your Booking");
                    $("button[value=buy]").prop("disabled", false);
                }
            }
            check_queue_count_timeout = setTimeout(set_check_queue_count, 3000);
        }
    );
}

add_order_queue();

