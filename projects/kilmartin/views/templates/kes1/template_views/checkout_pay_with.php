<?php
$custom_checkout = Settings::instance()->get('checkout_customization');
if (@$application_payment == false) {
    $tmp_cart = Session::instance()->get('ibcart');
    if (!$tmp_cart) {
        $tmp_cart = array(
            'booking' => array(),
            'booking_id' => null,
            'client_id' => null,
            'discounts' => array(),
            'courses' => array()
        );
    }
    
    $tmp_cart_data = Controller_FrontEnd_Bookings::get_cart_data($tmp_cart['booking'], $tmp_cart['booking_id'], $tmp_cart['client_id'], $tmp_cart['discounts'], $tmp_cart['courses']);
    //ob_clean();print_r($tmp_cart_data);exit;
    $has_playment_plan = 0;
    $has_subscription = 0;
    $has_fulltime = false;
    foreach ($tmp_cart_data as $tmp_item) {
        if ($tmp_item['type'] == 'course') {
            $has_fulltime = true;
        }
        if (isset($tmp_item['details']['paymentoptions'])) {
            $has_playment_plan += count($tmp_item['details']['paymentoptions']);
        }
        if (@$tmp_item['details']['booking_type'] == 'Subscription') {
            ++$has_subscription;
        }
    }
} else {
    $has_fulltime = true;
    $tmp_cart_data = array();
    $has_playment_plan = 0;
    $has_subscription = 0;
    $total = 0;
    foreach ($application['courses'] as $course) {
        $tmp_item = array(
            'id' => $course['course_id'],
            'details' => $course,
            'fee' => $course['fulltime_price']
        );
        $total += $course['fulltime_price'];
        $has_playment_plan += count($course['paymentoptions']);
        $tmp_cart_data[] = $tmp_item;
    }
}

$checkoutDetails = isset($checkoutDetails) ? $checkoutDetails : array();
$stripe_popup = false;
$contact_details = isset($contact_details) ? $contact_details : new Model_Contacts3(Auth::instance()->get_contact()->id);
$invoice_purchase_order_compatible = (is_object($schedule) && !empty($schedule->allow_purchase_order)
    && (isset($contact_details) && count($contact_details->get_contact_relations(array('contact_type' => 'organisation'))) > 0));
$credit_card_compatible = (!empty($realex_enabled) || !empty($stripe_enabled)) && $schedule->allow_credit_card !== '0';
$organisation_contact = ($invoice_purchase_order_compatible) ? new Model_Contacts3($contact_details->get_contact_relations(array('contact_type' => 'organisation'))[0]['parent_id']) : null;
$organisation = ($invoice_purchase_order_compatible) ? Model_Organisation::get_org_by_contact_id($organisation_contact->get_id()) : null;
$payment_methods = [];
if (!empty($mobile_payment_enabled)) {
    $payment_methods['mobile_carrier'] = $mobile_payment_enabled;
}

if (!empty($stripe_enabled)) {
    $payment_methods['card'] = $stripe_enabled;
    $stripe_popup = false;
}
elseif (!empty($realex_enabled)) {
    $payment_methods['card'] = $realex_enabled;
}

if (!empty($cash_payment_enabled)) {
    $payment_methods['cash'] = $cash_payment_enabled;
}

if (!empty($credit_payment_enabled)) {
    $invoice_purchase_order_compatible = true;
}
?>

<?php if (($total > 0 || @$application_payment) && $stripe_popup) {
    include 'checkout_pay_with_stripe_handler.php';
}

?>

<?php
 /*
  * If there are multiple payment methods, show this section.
  * If there is only one payment method, show this section, unless that payment method is the Stripe popup. The Stripe popup is placed elsewhere.
  */
?>
<?php if (count($payment_methods) > 1 || (count($payment_methods) == 1 && !$stripe_popup)): ?>
    <div class="pay-with">
        <?php if ($total > 0) { ?>
            <h2><?= __('Pay with') ?></h2>
        <?php } ?>

        <div class="form-group">
            <div class="col-sm-12" id="payment-tabs">
                <?php if ($total > 0 || @$application_payment) { ?>
                    <ul class="fullwidth--mobile" id="payment-methods">
                        <?php if (!empty($mobile_payment_enabled)): ?>
                            <li data-payment_method="sms"><a href="#payment-tabs-mobile_carrier"><?= __('Mobile carrier') ?></a></li>
                        <?php endif; ?>
                        <?php if ($credit_card_compatible): ?>
                            <li class="active" data-payment_method="cc"><a href="#payment-tabs-credit_card"><?= __('Card') ?></a></li>
                        <?php endif; ?>
                        <?php if (@$cash_payment_enabled) { ?>
                            <li class="" data-payment_method="cash"><a href="#payment-tabs-cash"><?= __('Cash') ?></a></li>
                        <?php } ?>
                        <?php if ($invoice_purchase_order_compatible): ?>
                            <li data-payment_method="purchase_order">
                                <a href="#payment-tabs-purchase_order"><?= __('Invoice') ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (is_object($schedule) && $schedule->allow_sales_quote): ?>
                            <li data-payment_method="sales_quote">
                                <a href="#payment-tabs-sales_quote"><?= __('Quote') ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php } ?>

                <?php if (!empty($mobile_payment_enabled)): ?>
                    <div id="payment-tabs-mobile_carrier">
                        <p><?= sprintf(__('We need to to send an SMS to confirm your booking with us today. Please note a charge of €%s will be applied to your mobile carrier.'), number_format($sms_fee, 2)) ?></p>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <label class="form-label" for="checkout-charge-mobile-code"><?= __('Enter preferred mobile number to be charged') ?></label>
                            </div>

                            <input type="hidden" name="charge_mobile" id="checkout-charge-mobile" />

                            <div class="col-sm-4">
                                <?php
                                $enabled_mobile_payment_operators = Settings::instance()->get('allpoints_charge_operators');
                                $options = array('' => '');
                                foreach (Model_Allpoints::get_operators() as $operator_id => $operator) {
                                    if ($enabled_mobile_payment_operators && in_array($operator_id, $enabled_mobile_payment_operators)) {
                                        $options[$operator_id] = $operator;
                                    }
                                }
                                $attributes = array('class' => 'validate[required]', 'id' => 'checkout-charge-operator');

                                echo Form::ib_select(__('Operator'), 'charge_mobile_operator', $options, null, $attributes);
                                ?>
                            </div>

                            <div class="col-xs-5 col-sm-3">
                                <?php
                                $attributes = array('class' => 'validate[required]', 'id' => 'checkout-charge-mobile-code');
                                $options = array('' => '');
                                foreach (Model_Contacts3::$mobile_provider_codes as $code) {
                                    $options[$code] = $code;
                                }
                                echo Form::ib_select(null, 'charge_mobile_code', $options, @$mobile['code']);
                                ?>
                            </div>

                            <div class="col-xs-7 col-sm-5">
                                <?php
                                $attributes = array('class' => 'validate[required]', 'id' => 'checkout-charge-mobile-number');
                                echo Form::ib_input(__('Mobile number'), 'charge_mobile_number', @$mobile['number'], $attributes);
                                ?>
                            </div>
                        </div>

                        <input type="button" value="Send me my booking code" class="button button--full" id="mobile_verification_send" />

                        <div class="hidden form-group" id="mobile_verification_sent">
                            <p><?= __('Please enter the verification code you received below') ?></p>
                            <div class="col-xs-7 col-sm-6">
                                <?php
                                $attributes = array('placeholder' => __('Verification Code'), 'class' => 'validate[required]', 'id' => 'checkout-charge-mobile-verification-code');
                                echo Form::ib_input(null, 'charge_mobile_verification_code', null, $attributes);
                                ?>
                                <input type="hidden" name="allpoints_tx_id" />
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($credit_card_compatible): ?>
                    <?php if ($total > 0 || @$application_payment || @$confirmation == 'subscription') { ?>
                        <input type="hidden" name="confirmation" value="<?= isset($confirmation) ? html::chars($confirmation) : $confirmation ?>" />
                        <input type="hidden" name="booking_id" value="<?= isset($booking_id) ? $booking_id : '' ?>" />
                        <div id="payment-tabs-credit_card">
                            <?php if ($stripe_popup) { ?>
                                <div class="checkout-processed_by" style="float: right;">
                                    <div><?= __('Processed by') ?></div>

                                    <a href="https://stripe.com/ie" target="_blank">
                                        <img src="<?= URL::get_engine_assets_base() ?>img/stripe.svg" alt="Powered by Stripe" style="width: 100px;">
                                    </a>
                                </div>

                                <p><?= htmlentities(__('You will be prompted to enter your credit card details when you click the "complete booking" button.')) ?></p>
                            <?php } else if (!empty($stripe_enabled)) { ?>
                                <input type="hidden" name="stripe_payment_intent_id" value="" />
                                <div class="form-group mb0">
                                    <div id="card-element"></div>
                                    <div id="card-errors" role="alert"></div>
                                </div>
                                <script>
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
                            <?php } else { ?>
                                <?php if (@$payments_store_card && @count($cards) > 0) { ?>
                                    <div class="form-group">
                                        <?php
                                        echo Form::ib_radio("New Card", "cc_new", 1, true);
                                        echo Form::ib_radio("Use Saved Card", "cc_new", 0, false);
                                        ?>
                                    </div>

                                    <div class="form-group saved-card hidden">
                                        <?php
                                        $options = array();
                                        foreach ($cards as $card) {
                                            $options[$card['id']] = '**** **** **** ' . $card['last_4'] . ($card['exp_month'] != '' ? ' (' . $card['exp_month'] . '-' . $card['exp_year'] . ')' : '');
                                        }
                                        echo Form::ib_select(__('Select a saved card'), 'saved_card_id', $options);
                                        ?>
                                    </div>
                                <?php } ?>

                                <div class="form-group new-card">
                                    <div class="col-sm-3">
                                        <?php
                                        $options = array('' => '', 'visa' => 'Visa', 'mc' => 'MasterCard');
                                        $attributes = array('autocomplete' => 'cc-type', 'class' => 'validate[required]', 'id' => 'checkout-ccType');
                                        echo Form::ib_select(__('Card Type'), 'ccType', $options, null, $attributes)
                                        ?>
                                    </div>

                                    <div class="col-sm-9">
                                        <?php
                                        $attributes = array(
                                            'type' => 'text',
                                            'autocomplete' => 'cc-number',
                                            'class' => 'validate[required,funcCall[luhnTest]]',
                                            'data-automove_pattern' => '([0-9][^0-9]*){16}', // Tab onto the next field once this pattern has been met (16 numbers with any amount of non-numbers between)
                                            'id' => 'checkout-ccNum',
                                            'pattern' => '\d*' // control iOS keypad input
                                        );
                                        echo Form::ib_input(__('Card Number'), 'ccNum', null, $attributes);
                                        ?>

                                        <?php /* Four-part CC number input. To revise
                                    <div class="row gutters hidden-xs">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <div class="col-xs-3">
                                                <input type="text" class="checkout-ccNum-component validate[required]" id="checkout-ccNum-component-<?= $i ?>" />
                                            </div>
                                        <?php endfor; ?>
                                    </div>

                                    <div class="hidden-sm hidden-md hidden-lg hidden-xl">
                                        <input type="text" class="validate[required]" id="checkout-ccNum" name="ccNum" />
                                    </div>
                                    */ ?>
                                    </div>
                                </div>

                                <div class="form-group new-card">
                                    <div class="col-xs-12">
                                        <?php
                                        $value = isset($checkoutDetails['ccName']) ? $checkoutDetails['ccName'] : null;
                                        $attributes = array('autocomplete' => 'cc-name', 'class' => 'validate[required]', 'id' => 'checkout-ccName', 'data-saveable' => true);
                                        echo Form::ib_input(__('Cardholder name'), 'ccName', $value, $attributes)
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <div class="col-sm-4">
                                        <label class="form-label new-card" for="ccExpMM"><?= __('Expiration date') ?>*</label>

                                        <div class="row gutters new-card">
                                            <div class="col-xs-6">
                                                <?php
                                                $options = array('' => __('mm'), '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12');
                                                $attributes = array(
                                                    'autocomplete'      => 'cc-exp-month',
                                                    'class'             => 'validate[required,funcCall[checkCCDates]] cc-data',
                                                    'id'                => 'ccExpMM',
                                                );
                                                echo Form::ib_select(null, 'ccExpMM', $options, null, $attributes);
                                                ?>
                                            </div>

                                            <div class="col-xs-6">
                                                <?php
                                                $options = array('' => __('yy'));
                                                $year = date('Y' ,time());
                                                for ($final_year = $year + 11; $year < $final_year; $year++) {
                                                    $options[$year % 100] = $year % 100;
                                                }
                                                $attributes = array(
                                                    'autocomplete' => 'cc-exp-year',
                                                    'class'        => 'validate[required,funcCall[checkCCDates]] cc-data',
                                                    'id'           => 'ccExpYY'
                                                );
                                                echo Form::ib_select(null, 'ccExpYY', $options, null, $attributes)
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <label for="checkout-cvv"><?= __('CVV number') ?>*</label>

                                        <div class="row gutters vertically_center">
                                            <div class="col-xs-6">
                                                <?php
                                                $attributes = array(
                                                    'type' => 'number',
                                                    'autocomplete' => 'cc-csc',
                                                    'class' => 'validate[required]',
                                                    'id' => 'checkout-cvv',
                                                    'pattern' => '\d*'
                                                );
                                                echo Form::ib_input(null, 'ccv', null, $attributes)
                                                ?>
                                            </div>
                                            <div class="col-xs-6">
                                                <?php $cvv_text = __('The last three digits on the back of your card.') ?>

                                                <span class="d-inline-block checkout-cvv-icon" title="<?= htmlspecialchars($cvv_text) ?>">
                                                    <?= IbHelpers::embed_svg('CVV Icon', ['width' => '46', 'height' => '36']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="checkout-processed_by" style="float: right;">
                                            <div><?= __('Processed by') ?></div>

                                            <?php if (!empty($stripe_enabled)): ?>
                                                <a href="https://stripe.com/ie" target="_blank">
                                                    <img src="<?= URL::get_engine_assets_base() ?>img/stripe.svg" alt="Stripe" style="width: 100px;">
                                                </a>
                                            <?php elseif (!empty($realex_enabled)): ?>
                                                <a href="https://www.realexpayments.com/" target="_blank">
                                                    <img src="<?= URL::get_engine_assets_base() ?>img/secured_by_global_payments.png" alt="Securely by Global Payments" />
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (@$payments_store_card) { ?>
                                <div class="form-group new-card">
                                    <div class="col-sm-1">
                                        <?php
                                        $attributes = array('id' => 'cc-store-yes', 'class' => 'validate[required]');
                                        echo Form::ib_checkbox(null, 'cc_store', 'YES', null, $attributes);
                                        ?>
                                    </div>
                                    <div class="col-sm-11">
                                        <label for="cc-store-yes">
                                            <p><?=__('Save credit card. You credit card number is not stored on our server. It\'s stored securely on realex servers.')?></p>
                                        </label>
                                    </div>
                                </div>
                                <?php if ((isset($contact) && $contact->has_role('student'))) { ?>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?=Form::ib_select('Card to be saved on', 'cc_store_guardian', ['YES' => 'Guardian/parent', '' => 'Student'], 'YES')?>
                                    </div>
                                </div>
                                <?php } ?>

                                    <?php if (($has_playment_plan || $has_subscription) && @$payments_recurring_payments) { ?>
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <?php
                                        $attributes = array('id' => 'cc-recurring-payments-yes', 'class' => 'validate[required]');
                                        echo Form::ib_checkbox(null, 'cc_recurring_payments', 'YES', null, $attributes);
                                        ?>
                                    </div>
                                    <div class="col-sm-11">
                                        <label for="cc-recurring-payments-yes">
                                            <p><?=__('Automatically pay your installments using stored credit card.')?></p>
                                        </label>
                                    </div>
                                </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php endif; ?>

                <?php if ($total > 0 || @$application_payment) { ?>
                <?php if (!empty($cash_payment_enabled)): ?>
                    <div id="payment-tabs-cash">
                        <p><?= __('Cash') ?></p>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo Form::ib_checkbox(
                                    __('Cash payment in office'),
                                    'cash_payment_yes',
                                    'yes',
                                    null,
                                    array('id' => 'checkout-cash_payment_yes')
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php } ?>

                <?php if ($invoice_purchase_order_compatible): ?>
                    <?php
                    $invoice_fields = Settings::instance()->get('invoice_payment_fields');
                    $invoice_fields = $invoice_fields ? $invoice_fields : [];
                    $show_has_aiq_account = (in_array('has_aiq_account', $invoice_fields));
                    ?>

                    <div class="checkout-invoice-wrapper" id="payment-tabs-purchase_order">
                        <?php if (Settings::instance()->get('invoice_payment_intro')): ?>
                            <div class="checkout-invoice-intro">
                                <?= IbHelpers::parse_page_content(Settings::instance()->get('invoice_payment_intro')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_has_aiq_account): ?>
                            <div class="col-sm-10 pl-1">
                                <p class="mt-0">Does your organisation have an existing credit account with <?=Settings::instance()->get('project_name')?></p>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-4">
                                    <?= form::btn_options('has_aiq_account', array('yes' => 'Yes', 'no' => 'No'),
                                        null, false, ['class' =>'validate[required]'])?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('aiq_customer_code', $invoice_fields) || in_array('aiq_billing_email', $invoice_fields)): ?>
                            <div class="form-group aiq-yes<?= $show_has_aiq_account ? ' hidden' : ''; ?>">
                                <div class="col-sm-10 mt-3 mt-0">
                                    <p class="p-0">
                                        <?php if (in_array('aiq_customer_code', $invoice_fields) && in_array('aiq_billing_email', $invoice_fields)): ?>
                                            Please note the account reference code and billing email need to match records on file in order to complete the booking.
                                        <?php elseif (in_array('aiq_customer_code', $invoice_fields)): ?>
                                            Please note the account reference code needs to match records on file in order to complete the booking.
                                        <?php elseif (in_array('aiq_billing_email', $invoice_fields)): ?>
                                            Please note the billing email needs to match records on file in order to complete the booking.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group aiq-yes<?= $show_has_aiq_account ? ' hidden' : ''; ?>">
                                <?php if (in_array('aiq_customer_code', $invoice_fields)): ?>
                                    <div class="col-sm-6">
                                        <?=form::ib_input('Account Reference Code e.g. CC101111', 'aiq_customer_code')?>
                                    </div>
                                <?php endif; ?>

                                <?php if (in_array('aiq_billing_email', $invoice_fields)): ?>
                                    <div class="col-sm-6">
                                        <?=form::ib_input('Billing Email', 'aiq_billing_email')?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('purchase_order_no', $invoice_fields)): ?>
                            <div class="form-group aiq-yes<?= $show_has_aiq_account ? ' hidden' : ''; ?>">
                                <div class="col-sm-6">
                                    <?php if (in_array('aiq_billing_email', $invoice_fields)): ?>
                                        <?= Form::ib_input(__('Purchase order number'), 'purchase_order_no', null, ['id' => 'checkout-invoice-purchase_order_no']) ?>
                                    <?php else: ?>
                                        <div
                                            <label class="form-label" for="checkout-invoice-purchase_order_no">
                                                <abbr title="Purchase Order">PO</abbr> Number<span class="validation-star">*</span>
                                            </label>
                                        </div>
                                        <?= Form::ib_input(
                                            null,
                                            'purchase_order_no',
                                            null,
                                            ['class' =>'validate[required]', 'id' => 'checkout-invoice-purchase_order_no']
                                        ) ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (Settings::instance()->get('invoice_payment_footer')): ?>
                                    <div class="col-sm-12 mt-3 checkout-invoice-footer">
                                        <?= IbHelpers::parse_page_content(Settings::instance()->get('invoice_payment_footer')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_has_aiq_account): ?>
                            <div class="form-group aiq-no hidden">
                                <div class="col-sm-12">
                                    <p class="pb-0">Please contact us to discuss setting up a new account or choose “Card” to complete your booking with a credit or debit card.</p>
                                </div>
                            </div>

                            <script>
                            $("[name=has_aiq_account]").on("change", function() {
                                let has_account = ($("[name=has_aiq_account]:checked").val() == 'yes');

                                $(".aiq-yes").toggleClass("hidden", !has_account);
                                $(".aiq-no").toggleClass("hidden", has_account);
                            });
                            </script>
                        <?php endif; ?>
                    </div>
                    
                <?php endif; ?>

                <?php if (is_object($schedule) && $schedule->allow_sales_quote): ?>
                    <div class="checkout-invoice-wrapper" id="payment-tabs-sales_quote">
                        <div class="form-row checkout-sales_quote-checkbox-wrapper">
                            <?= Form::ib_checkbox(
                                'Tick here to receive your quote*',
                                'is_sales_quote',
                                1,
                                false,
                                ['class' => 'validate[required]']
                            ) ?>
                        </div>

                        <div class="checkout-sales_quote-notice">
                            <p><?= __('Please note, your booking is provisional until full payment or a valid Purchase Order number has been received.') ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                if ($has_playment_plan > 0 && $total > 0) {
                    ?>
                    <h3 class="checkout-heading"><?= __('Payment Plans') ?></h3>

                    <div class="billing-content">
                        <div class="billing-inner-content">
                            <?php
                            foreach ($tmp_cart_data as $tmp_item) {
                                if (@$tmp_item['details']['paymentoptions']) {

                                    ?>
                                    <h3><?=__('Plan options for $1', array('$1' => $tmp_item['details']['title'])) ?></h3>

                                    <div class="form-group gutters payment-options-selector">
                                        <div class="col-xs-4 col-sm-3">
                                            <?php
                                            $input_name = 'paymentoption['.$tmp_item['id'].']';
                                            $checked    = !empty($application_payment);
                                            $attributes = array(
                                                'class'       => 'paymentoption',
                                                'data-index'  => 0,
                                                'data-paynow' => $tmp_item['fee']
                                            );

                                            echo Form::ib_radio(__('Full Payment'), $input_name, 0, $checked, $attributes);
                                            ?>
                                        </div>

                                        <?php foreach ($tmp_item['details']['paymentoptions'] as $key => $paymentoption): ?>
                                            <div class="col-xs-4 col-sm-3">
                                                <?php
                                                if (@$paymentoption['interest_type'] == 'Custom') {
                                                    $tmp_item['details']['paymentoptions'][$key]['custom_payments'] = @json_decode($paymentoption['custom_payments'], true);
                                                } else {
                                                    if ($paymentoption['months'] == 0) {
                                                        continue;
                                                    }
                                                }
                                                $label      = __('$1 payments', array('$1' => (@$paymentoption['interest_type'] == 'Custom' ? count($tmp_item['details']['paymentoptions'][$key]['custom_payments']) : $paymentoption['months'])));
                                                $input_name = 'paymentoption['.$tmp_item['id'].']';
                                                $attributes = array(
                                                    'class'       => 'paymentoption',
                                                    'data-index'  => ($key + 1),
                                                    'data-paynow' => $paymentoption['deposit']
                                                );

                                                echo Form::ib_radio($label, $input_name, $paymentoption['id'], false, $attributes);
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="payment-options-tabs">
                                        <?php foreach ($tmp_item['details']['paymentoptions'] as $key => $paymentoption): ?>
                                            <?php
                                            if (@$paymentoption['interest_type'] != 'Custom')
                                            if ($paymentoption['months'] == 0) {
                                                continue;
                                            }
                                            $first_timeslot = null;
                                            if (isset($tmp_item['periods_attending'])) {
                                                $first_timeslot = $tmp_item['timeslot_details'][$tmp_item['periods_attending'][0]];
                                            }
                                            ?>
                                            <div class="payment-options-tab hidden" data-index="<?= $key + 1 ?>">
                                                <h4><?= __('Payment Schedule') ?></h4>

                                                <table class="table installments">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col"><?= __('No')       ?></th>
                                                            <th scope="col"><?= __('Title')    ?></th>
                                                            <th scope="col"><?= __('Due Date') ?></th>
                                                            <th scope="col"><?= __('Net')      ?></th>
                                                            <th scope="col" class="<?=($paymentoption['interest_rate'] > 0 || $paymentoption['interest_type'] == 'Custom') ? '' : 'hidden'?>"><?= __('Interest') ?></th>
                                                            <th scope="col"><?= __('Total')    ?></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        $cc_fee = (float)Settings::instance()->get('course_cc_booking_fee');
                                                        if (@$paymentoption['interest_type'] == 'Custom') {
                                                            $installments = array();
                                                            foreach ($paymentoption['custom_payments'] as $custom_payment){
                                                                $installments[] = array(
                                                                    'amount' => (float)$custom_payment['amount'],
                                                                    'interest' => (float)$custom_payment['interest'],
                                                                    'total' => (float)$custom_payment['total'],
                                                                    'due' => $custom_payment['due_date'],
                                                                );
                                                            }
                                                        } else {
                                                            $installments = Model_Kes_Payment::calculate_payment_plan(
                                                                $tmp_item['fee'] + $cc_fee, $paymentoption['deposit'], 0, $paymentoption['months'], 'month', 'Percent', $paymentoption['interest_rate'], date::today(),
                                                                ($paymentoption['start_after_first_timeslot'] && isset($first_timeslot['datetime_start']) ? date('Y-m-d', strtotime($first_timeslot['datetime_start'])) : null)
                                                            );
                                                        }
                                                        $paymentoption_total = 0;
                                                        $paymentoption_interest = 0;
                                                        $deposit = 0;
                                                        $payment_number = 0;
                                                        ?>

                                                        <?php foreach ($installments as $i => $installment): ?>
                                                            <?php
                                                            $paymentoption_total += $installment['total'];
                                                            $paymentoption_interest += $installment['interest'];
                                                            if ($installment['interest'] == 0 && $i == 0) {
                                                                $deposit = $installment['total'];
                                                            } else {
                                                                ++$payment_number;
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><?=$i + 1?></td>
                                                                <td><?=($installment['interest'] == 0 && ($installment['due'] == date::today() || $installment['due'] == ''))&& $i == 0 ? __('Deposit') : $payment_number . '. ' . __('Payment')?></td>
                                                                <td><?=$installment['due']?></td>
                                                                <td><?=$installment['amount']?></td>
                                                                <td  class="<?=($paymentoption['interest_rate'] > 0 || $paymentoption['interest_type'] == 'Custom') ? '' : 'hidden'?>"><?=$installment['interest']?></td>
                                                                <td><?=$installment['total']?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot data-total="<?=$paymentoption_total?>" data-interest="<?=$paymentoption_interest?>" data-deposit="<?=$deposit?>">
                                                        <tr>
                                                            <th colspan="<?=($paymentoption['interest_rate'] > 0 || $paymentoption['interest_type'] == 'Custom') ? 5 : 4?>" style="text-align: right">Total</th>
                                                            <td><?=$paymentoption_total?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <br clear="both" />
                    <?php
                } else {
                    foreach ($tmp_cart_data as $tmp_item) {
                        ?>
                        <input type="hidden" name="paymentoption[<?=$tmp_item['id']?>]" value="0" />
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (count($payment_methods) && Settings::instance()->get('checkout_billing_information_section')): ?>
    <?php if ($total > 0 || @$application_payment) { ?>
        <?php if($invoice_purchase_order_compatible && Settings::instance()->get('invoice_enable_lead_booker_for_primary_biller') === '1'): ?>
        <div class="purchase_order_primary_biller_details">
            <h3 class="checkout-heading"><?= __('Billing details') ?></h3>
            <div class="billing-content primary-biller-content">
                <div class="billing-inner-content">
                    <?php if(Settings::instance()->get('invoice_enable_lead_booker_for_primary_biller') === '1'): ?>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <?php
                                $label = __('Send invoice to me');
                                echo Form::ib_checkbox($label, 'invoice_other_person', null,
                                    false, array('class' => 'invoice_other_person'));
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <?php
                                $attributes = array(
                                    'id' => 'billing_first_name'
                                );
                                $value = $organisation ? $organisation->get_primary_biller()->get_first_name() : '';
                                echo Form::ib_input(__('First name'), 'billing_address[first_name]', $value,
                                    $attributes); ?>
                            </div>
                            <div class="col-sm-4">
                                <?php
                                $attributes = array(
                                    'id' => 'billing_last_name'
                                );
                                $value = $organisation ? $organisation->get_primary_biller()->get_last_name() : '';
                                echo Form::ib_input(__('Last name'), 'billing_address[last_name]', $value,
                                    $attributes); ?>
                            </div>
                            <div class="col-sm-4">
                                <?php
                                $attributes = array(
                                    'id' => 'billing_email'
                                );
                                echo Form::ib_input(__('Email'), 'billing_address[email]',
                                    $organisation ? $organisation->get_primary_biller()->get_email() : '',
                                    $attributes); ?>
                            </div>
                        </div>
                    <?php else: ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <h3 class="checkout-heading billing-address-header"><?= __(($invoice_purchase_order_compatible) ? 'Organisation billing address' : 'Billing address') ?></h3>
        <?php
        if (empty($billing_contact)) {
            $billing_contact = $contact_details;
        }
        $billing_readonly = false;
        if ($invoice_purchase_order_compatible) {
            $billing_readonly = Settings::instance()->get('bookings_billing_address_readonly') == 1;
        }

        // If the contact is part of an organisation, use the organisation billing address and make the fields readonly
        if (!$billing_readonly) {
            $billing_readonly = !$contact_details->can_edit_billing_address();
        }
        $billing_address = $contact_details->get_billing_address();
        ?>
        <div class="billing-content">
            <div class="billing-inner-content">
                <input type="hidden" name="billing_address[address_id]" value="<?= $billing_address->get_address_id() ?>" />

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $address_lines = (isset($checkoutDetails['address'])) ? explode("\n", trim($checkoutDetails['address'])) : array();

                        $value = isset($address_lines[0]) ? trim($address_lines[0]) : '';
                        $value = ($value == '') ? $billing_address->get_address1() : $value;
                        $attributes = array('autocomplete' => 'address-line1', 'class' => ($billing_readonly ? '' : 'validate[required]'), 'id' => 'checkout-address1', 'data-saveable' => true);
                        if($invoice_purchase_order_compatible) {
                            $attributes['data-individual-address1'] = $value;
                            $value = $organisation_contact ? $organisation_contact->billing_address->get_address1() : '';
                            $attributes['data-organisation-address1'] = $value;
                        }
                        if ($billing_readonly) {
                            $attributes['readonly'] = 'readonly';
                        }
                        echo Form::ib_input(__('Address line 1'), 'billing_address[address1]', $value, $attributes);
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?php
                        $value = isset($address_lines[1]) ? trim($address_lines[1]) : '';
                        $value = ($value == '') ? $billing_address->get_address2() : $value;
                        $attributes = array('autocomplete' => 'address-line2', 'id' => 'checkout-address2', 'data-saveable' => true);
                        if ($invoice_purchase_order_compatible) {
                            $attributes['data-individual-address2'] = $value;
                            $value = $organisation_contact ? $organisation_contact->billing_address->get_address2() : '';
                            $attributes['data-organisation-address2'] = $value;
                        }
                        if ($billing_readonly) {
                            $attributes['readonly'] = 'readonly';
                        }
                        echo Form::ib_input(__('Address line 2'), 'billing_address[address2]', $value, $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $value = isset($address_lines[2]) ? trim($address_lines[2]) : '';
                        $value = $value ?: $billing_address->get_address3();
                        $attributes = array('autocomplete' => 'address-line3', 'id' => 'checkout-address3', 'data-saveable' => true);
                        if ($invoice_purchase_order_compatible) {
                            $attributes['data-individual-address3'] = $value;
                            $value = $organisation_contact ? $organisation_contact->billing_address->get_address3() : '';
                            $attributes['data-organisation-address3'] = $value;
                        }
                        if ($billing_readonly) {
                            $attributes['readonly'] = 'readonly';
                        }
                        echo Form::ib_input(__('Address line 3'), 'billing_address[address3]', $value, $attributes);
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?php
                        $value = isset($checkoutDetails['city']) ? $checkoutDetails['city'] : '';
                        $value = ($value == '') ? $billing_address->get_town() : $value;
                        $attributes = array('autocomplete' => 'address-level2', 'class' => ($billing_readonly ? '' : 'validate[required]'), 'id' => 'checkout-town', 'data-saveable' => true);
                        if ($invoice_purchase_order_compatible) {
                            $attributes['data-individual-town'] = $value;
                            $value = $organisation_contact ? $organisation_contact->billing_address->get_town() : '';
                            $attributes['data-organisation-town'] = $value;
                        }
                        if ($billing_readonly) {
                            $attributes['readonly'] = 'readonly';
                        }
                        echo Form::ib_input(__('Town / City'), 'billing_address[town]', $value, $attributes);
                        ?>
                    </div>
                </div>

                <?php if (!isset($event_object)): ?>
                    <div class="form-group">

                    <?php if (in_array($custom_checkout, ['bc_language', 'sls'])) { ?>
                        <div class="col-sm-6">
                            <?php
                            $country_options = '<option value=""></option>'.html::optionsFromRows('code', 'name', Model_Residence::get_all_countries('plugin_courses_counties'), '');
                            echo Form::ib_select(__('Country'), 'billing_address[checkout-country]', $country_options);
                            ?>
                        </div>
                    <?php } else { ?>
                        <div class="col-sm-6">
                            <?php
                                $counties = !empty($counties) ? $counties : Model_Residence::get_all_counties('plugin_courses_counties');
                                $options  = array('' => '');
                                foreach ($counties as $county) {
                                    $options[$county['id']] = $county['name'];
                                }
                                $selected   = $billing_address->get_county();
                                $attributes = array('autocomplete' => 'address-level1', 'id' => 'checkout-county', 'data-saveable' => true);
                                if ($invoice_purchase_order_compatible) {
                                    $attributes['data-individual-address-county'] = $selected;
                                    $selected = ($organisation_contact && $organisation_contact->billing_address->get_county() != '0') ? $organisation_contact->billing_address->get_county() : '';
                                    $attributes['data-organisation-address-county'] = $selected;
                                }
                                if ($billing_readonly) {
                                    $attributes['disabled'] = 'disabled';
                                    echo '<input type="hidden" name="billing_address[county]" value="' . $selected . '" />';
                                }
                                echo Form::ib_select(__('County'), 'billing_address[county]', $options, $selected, $attributes);
                            ?>
                        </div>

                        <div class="col-sm-6">
                            <?php
                            $options = array();
                            foreach (Model_Residence::get_all_countries() as $country) {
                                $options[$country['code']] = $country['name'];
                            }
                            $selected = (isset($billing_contact) && !empty($billing_address->get_country())) ?
                                $billing_address->get_country() : 'IE';
                            $attributes = array('class' => 'ib-combobox', 'id' => 'checkout-country');
                            if ($invoice_purchase_order_compatible) {
                                $attributes['data-individual-address-country'] = $selected;
                                $selected = $organisation_contact ? $organisation_contact->billing_address->get_country() : 'IE';
                                $attributes['data-organisation-address-country'] = $selected;
                            }
                            if ($billing_readonly) {
                                $attributes['disabled'] = 'disabled';
                                echo '<input type="hidden" name="billing_address[country]" value="' . $selected . '" />';
                            }
                            echo Form::ib_select(__('Country'), 'billing_address[country]', $options, $selected,
                                $attributes);
                            ?>
                        </div>
                    <?php } ?>
                    </div>
                    <?php if (!in_array($custom_checkout, ['bc_language', 'sls'])) : ?>
                        <div class="form-group">
                            <div class="col-sm-6">
                            <?php
                                $selected = (isset($billing_contact) && !empty($billing_contact->billing_address->get_postcode())) ?
                                $billing_contact->billing_address->get_postcode() : '';
                                $attributes = array('autocomplete' => 'postal-code', 'id' => 'checkout-postcode');
                                if ($invoice_purchase_order_compatible) {
                                    $attributes['data-individual-address-postcode'] = $selected;
                                    $selected = $organisation_contact ? $organisation_contact->billing_address->get_postcode() : '';
                                    $attributes['data-organisation-address-postcode'] = $selected;
                                }
                                if ($billing_readonly) {
                                    $attributes['readonly'] = 'readonly';
                                }
                                echo Form::ib_input(__('Postcode'), 'billing_address[postcode]', $selected,
                                $attributes);
                                ?>
                            </div>
                        </div>

                        <?php endif;?>
                    <?php else: ?>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php
                            $value = isset($checkoutDetails['county']) ? trim($checkoutDetails['county']) : '';
                            $value = ($value == '' && isset($billing_contact->billing_address)) ? $billing_contact->billing_address->get_county() : $value;
                            $attributes = array('autocomplete' => 'address-level2', 'class' => ($billing_readonly ? '' : 'validate[required]'), 'id' => 'checkout-town', 'data-saveable' => true);
                            if ($invoice_purchase_order_compatible) {
                                $attributes['data-individual-town'] = $value;
                                $value = $organisation_contact ? $organisation_contact->billing_address->get_county() : '';
                                $attributes['data-organisation-town'] = $value;
                            }
                            $attributes = array('autocomplete' => 'address-level1', 'id' => 'checkout-county', 'data-saveable' => true);
                            if ($billing_readonly) {
                                $attributes['readonly'] = 'readonly';
                            }
                            echo Form::ib_input(__('State/County'), 'county', $value, $attributes);
                            ?>
                        </div>

                        <div class="col-sm-6">
                            <?php
                            $options    = '<option></option>'.html::optionsFromRows('id', 'name', Model_Event::getCountryMatrix(), $checkoutDetails['country_id']);
                            $attributes = array('autocomplete' => 'country-name', 'class' => ($billing_readonly ? '' : 'validate[required]'), 'id' => 'checkout-country', 'data-saveable' => true);
                            if ($billing_readonly) {
                                $attributes['readonly'] = 'readonly';
                            }
                            echo Form::ib_select(__('Country'), 'country_id', $options, null, $attributes);
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                        <?php
                        $value = isset($checkoutDetails['postcode']) ? trim($checkoutDetails['postcode']) : '';
                        $value = ($value == '' && isset($billing_contact->billing_address)) ? $billing_contact->billing_address->get_postcode() : $value;
                        $attributes = array('autocomplete' => 'address-level2', 'class' => ($billing_readonly ? '' : 'validate[required]'), 'id' => 'checkout-town', 'data-saveable' => true);
                        if ($invoice_purchase_order_compatible) {
                            $attributes['data-individual-town'] = $value;
                            $value = $organisation_contact ? $organisation_contact->billing_address->get_postcode() : '';
                            $attributes['data-organisation-town'] = $value;
                        }
                        $attributes = array('autocomplete' => 'postal-code', 'id' => 'checkout-postcode', 'data-saveable' => true);
                        if ($billing_readonly) {
                            $attributes['readonly'] = 'readonly';
                        }
                         ?>
                            <?= Form::ib_input(__('Postcode'), 'postcode', $value, $attributes); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php
                            $attributes = array('id' => 'checkout-comments', 'rows' => 3);
                            echo Form::ib_textarea(__('Comments'), 'comments', @$checkoutDetails['comments'], $attributes);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php } ?>


<?php endif; ?>

<?php if (Settings::instance()->get('captcha_enabled') == 1 && Settings::instance()->get('captcha_frontend_checkout_position') === 'billing_address'): ?>
    <div class="checkout-captcha-container mb-3">
        <?= Form::ib_captcha(2); ?>
    </div>
<?php endif; ?>

<?php if ( ! empty($privacy_policy_page) && ! empty($privacy_policy_page['id'])): ?>
    <h3 class="checkout-privacy-header"><?= htmlspecialchars($privacy_policy_page['title']) ?></h3>

    <div class="privacy-content">
        <div class="privacy-inner-content">
            <div class="term-privacy"><?= Ibhelpers::parse_page_content($privacy_policy_page['content']) ?></div>
        </div>
    </div>
<?php endif; ?>