<?php
$stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
$stripe_pkey = Settings::instance()->get($stripe_testing ? 'stripe_test_public_key'  : 'stripe_public_key');
?>
<input type="hidden" name="stripe_token" id="stripe_token" value="" />
<script src="https://checkout.stripe.com/checkout.js"></script>
<script>
    function getStripeToken() {
        return $.Deferred(deferedTokenRequest);
    }
    function deferedTokenRequest(defer) {
        var stripe_handler = StripeCheckout.configure(
            {
                key: '<?= $stripe_pkey ?>',
                currency: 'EUR',
                zipCode: false,
                image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
                locale: 'auto',
                token: function(token) {
                    $("#stripe_token").val(token.id);
                    defer.resolve();
                    console.log("Token created: " + token);
                },
                opened: function() {
                    console.log("stripe opened");
                },
                closed: function() {
                    defer.reject();
                    console.log("stripe closed");
                }
            }
        );
        stripe_handler.open(
            {
                name: '<?= $_SERVER['HTTP_HOST'] ?>',
                description: 'Course Booking',
                amount: ($('#checkout-breakdown-total').data('amend-total') || <?= $total ?>) * 100
            }
        );

        // Close Checkout on page navigation:
        $(window).on('popstate', function() {
            stripe_handler.close();
        });

    }
</script>