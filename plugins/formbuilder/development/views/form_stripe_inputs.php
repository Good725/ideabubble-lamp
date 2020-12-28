<div id="stripe_inputs" class="hidden">
<input type="hidden" name="stripe_payment_intent_id" value="" />
<div class="form-group mb0">
    <div id="card-element"></div>
    <label>Enter your card details</label>
    <div id="card-errors" role="alert"></div>
</div>
</div>
<script>
    $(document).on("ready", function(){
        var inputs = $("#stripe_inputs");
        inputs.remove();
        $("#payment_form_method_stripe_inputs").append(inputs);
        $("#payment_form_method_stripe").on("click", function(){
            $("#stripe_inputs, #payment_form_method_stripe_inputs").removeClass("hidden");
            $("[name=payment_type]").val("stripe");
            $("#pay_online_submit_button").show();
        });

        $("#payment_form_type_once_off").on("click", function(){
            if ($(".payment_select").length == 1) {
                $(".payment_select").click();
                setTimeout(
                    function(){
                        console.log("s");
                        $("#pay_online_submit_button").show();
                    },
                    30
                );
            }
            $("#pay_online_submit_button").show();
        });

        $("#pay_online_submit_button").on("click", function(){
            window.disableScreenDiv.autoHide = false;
            window.disableScreenDiv.style.visibility = "visible";
            $.post(
                "/frontend/payments/stripe_create_pi",
                {
                    amount: $("input[name=payment_total]").val(),
                    currency: "EUR",
                    order_id: ""
                },
                function (payment_intent) {
                    if (payment_intent.id) {
                        $("input[name=stripe_payment_intent_id]").val(payment_intent.id);
                        stripe.handleCardPayment(
                            payment_intent.secret,
                            window.stripe_card,
                            {
                                payment_method_data: {
                                    billing_details: {
                                        name: $("#payment_form_name-first_name").val()
                                    }
                                }
                            }
                        ).then(function(result){
                            //console.log(result);
                            if (result.error) {
                                window.disableScreenDiv.autoHide = true;
                                window.disableScreenDiv.visibility = "hidden";
                                var $clone = $('#checkout-error_message-template').clone();
                                $clone.removeClass('hidden').find('.checkout-error_message-text').html('Error processing payment. If this error continues, please contact the administration.');
                                $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                            } else {
                                $("#payment_form").submit();
                            }
                        });
                    } else {
                        var $clone = $('#checkout-error_message-template').clone();
                        $clone.removeClass('hidden').find('.checkout-error_message-text').html('Error processing payment. If this error continues, please contact the administration.');
                        $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                    }
                }
            );
        });
    });
    <?php
    $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
    $stripe_public_key = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
    ?>

    window.stripe = Stripe('<?=$stripe_public_key?>');
    // Create an instance of Elements.
    window.stripe_elements = window.stripe.elements();
    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element.
    window.stripe_card = window.stripe_elements.create('card', {hidePostalCode: true, style: style});

    // Add an instance of the card Element into the `card-element` <div>.
    window.stripe_card.mount('#card-element');
</script>

