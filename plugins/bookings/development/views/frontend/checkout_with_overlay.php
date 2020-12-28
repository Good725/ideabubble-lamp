<?php
$breadcrumb_title = '
    <span class="hidden--your_booking">'.__('Checkout').'</span>
    <span class="hidden--checkout">'.__('Your booking').'</span>';

$site_template = ORM::factory('Engine_Template')->where('stub', '=', Settings::instance()->get('template_folder_path'))->find_undeleted();

if (trim($site_template->header)) {
    eval('?>'.$site_template->header);
} else {
    include Kohana::find_file('template_views', 'header');
}

if (empty($contact) && isset($logged_contact)) {
    $contact = $logged_contact;
}

$prepay_bookings = isset($prepay_bookings) ? $prepay_bookings : array();
$payg_bookings   = isset($payg_bookings)   ? $payg_bookings   : array();
$all_bookings    = $prepay_bookings + $payg_bookings;
?>

<?php if (isset($page_object) && $page_object->content): ?>
    <div class="row page-content">
        <?= Ibhelpers::parse_page_content($page_object->content) ?>
    </div>
<?php endif; ?>

<?php
$custom_checkout        = Settings::instance()->get('checkout_customization');
$payments_store_card = (bool) Settings::instance()->get('payments_store_card');
$payments_recurring_payments = (bool) Settings::instance()->get('payments_recurring_payments');
$realex_enabled         = (bool) Settings::instance()->get('enable_realex');
$stripe_enabled         = (Settings::instance()->get('stripe_enabled') == 'TRUE');
$mobile_payment_enabled = (bool) Settings::instance()->get('enable_mobile_payments');
$payments_enabled       = ($stripe_enabled || $realex_enabled || $mobile_payment_enabled);
$currency_symbol        = isset($currency_symbol) ? $currency_symbol : '&euro;';
$checkoutDetails        = isset($checkoutDetails) ? $checkoutDetails : array();
$course_code            = isset($_GET['coursecode']) ? urldecode($_GET['coursecode']) : '';
$course_name            = isset($_GET['title'])      ? urldecode($_GET['title'])      : '';
$course_level           = isset($_GET['level'])      ? urldecode($_GET['level'])      : '';
?>

<style>
    <?php
    /*
     * The checkout is split into two sections on mobile.
     * Their visibility is toggled as you click "Continue" on the back arrow.
     */
    ?>
    @media screen and (max-width: 767px) {
        body:not(.checkout-your_booking) .hidden--checkout {
            display: none !important;
        }

        body.checkout-your_booking .hidden--your_booking,
        body.checkout-your_booking .banner-section {
            display: none !important;
        }
    }
</style>


<script>


	jQuery(document).ready(function () {
        $('.datepicker').datetimepicker({timepicker:false, format:'d-m-Y'});
        $('.timepicker').each(function() {
            var step = $(this).data('step') || 60;
            $(this).datetimepicker({datepicker:false, format:'H:i', step: step});
        });
        var checkout_posted = false;
		$('.checkout-complete_booking').on('click', function()
        {
            if (checkout_posted) {
                return false;
            }
            var $form = $('#booking-checkout-form');

            if ($form.validationEngine('validate'))
            {
                var promise = null;
                <?php if ($stripe_enabled): ?>
                    <?php
                    if (@$stripe_popup) {
                    ?>
                    if (!$.isFunction("getStripeToken")) {
                        console.error('getStripeToken function not defined, make sure engine is updated');
                        promise = $.when();
                    } else {
                        promise = $.when(getStripeToken());
                    }
                    <?php
                    } else {
                    ?>
                    window.disableScreenDiv.autoHide = false;
                    window.disableScreenDiv.style.visibility = "visible";
                    $.post(
                        "/frontend/payments/stripe_create_pi",
                        {
                            amount: $("input[name=amount]").val(),
                            currency: "EUR",
                            order_id: ""
                            <?php
                            if ($franchisee_account['stripe_auth']['stripe_user_id']) {
                            echo ",\n" . 'destination:"' . $franchisee_account['stripe_auth']['stripe_user_id'] . '"';
                            }
                            ?>
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
                                                name: $("#checkout-first_name, #checkout-student_first_name").val() + " " + $("#checkout-last_name, #checkout-student_last_name").val()
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
                                        if (result.paymentIntent) {
                                            post_checkout();
                                        }
                                    }
                                });
                            } else {
                                var $clone = $('#checkout-error_message-template').clone();
                                $clone.removeClass('hidden').find('.checkout-error_message-text').html('Error processing payment. If this error continues, please contact the administration.');
                                $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                            }
                        }
                    );
                    <?php
                    }
                    ?>
                <?php else: ?>
                    promise = $.when();
                <?php endif ?>


                if (promise) {
                    promise.done(function () {
                        post_checkout();
                    });
                }

                function post_checkout()
                {
                    $("[name=payment_method]").val($("#payment-methods > li.ui-state-active").data("payment_method"));

                    checkout_posted = true;
                    $("#checkout-student-tabs-contents .tab-pane.hidden").find("input, select, textarea").prop("disabled", true);

                    $.ajax({
                        url    : '/frontend/contacts3/ajax_submit_checkout/',
                        method : 'post',
                        data   : $form.serialize()
                    }).done(function(data)
                        {
                            data = JSON.parse(data);
                            if (data.success)
                            {
                                if (typeof ga == 'function') {
                                    // Send analytics data to Google before redirecting
                                    data.google_analytics.hitCallback = function() {
                                        window.location = data.redirect;
                                    };

                                    ga('event', 'purchase', data.google_analytics);

                                    setTimeout(function() {
                                        window.location = data.redirect;
                                    }, 1000);
                                } else {
                                      window.location = data.redirect;
                                }
                            }
                            else
                            {
                                if ($("#checkout-student-tabs li.active").data("student_id") == "new") {
                                    if (data.student_id) {
                                        $("#student_tab_new [name=student_id]").val(data.student_id);
                                    }
                                }
                                if (data.guardian_id) {
                                    $("[name=guardian_id]").val(data.guardian_id);
                                }
                                checkout_posted = false;

                                if (typeof grecaptcha != 'undefined')
                                {
                                    // Same CAPTCHA cannot be submitted twice
                                    grecaptcha.reset();
                                }

                                if (data.error_message)
                                {
                                    var $clone = $('#checkout-error_message-template').clone();
                                    $clone.removeClass('hidden').find('.checkout-error_message-text').html(data.error_message);
                                    $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                                }
                            }
                        })
                        .fail(function()
                        {
                            checkout_posted = false;
                        });
                }
            }
        });
    });
</script>
<div class="container">
<div class="row" id="checkout-error_messages">
    <div class="alert alert-danger popup_box checkout-error_message hidden" id="checkout-error_message-template">
        <button type="button" class="close-btn button--plain">&times;</button>
        <div class="checkout-error_message-text"></div>
    </div>
</div>

<?php if (!in_array($custom_checkout, ['bcfe', 'sls'])): ?>
    <div class="row">
        <?php
        if (!empty($all_bookings)) {
            $item = array_values($all_bookings)[0];
            $item = array_values($item)[0];
            $schedule = new Model_Course_Schedule($item['schedule_id']);

            $checkout_progress_event = [
                'course'      => $schedule->course->title,
                'course_id'   => $schedule->course->id,
                'schedule_id' => $schedule->id
            ];
        }

        include Kohana::find_file('views', 'checkout_progress');
        ?>
    </div>
<?php endif; ?>

<div class="row">
    <?php if (!empty($checkout_error)): ?>
        <p><?= $checkout_error ?></p>
    <?php else: ?>
        <script>
            if (typeof fbq === 'function') {
                fbq('track', 'InitiateCheckout');
            }
        </script>

            <input type="hidden" id="checkout-country_json" value="<?= htmlentities(json_encode(Model_Event::getCountryMatrix())) ?>" />

        <form class="clearfix checkout-form" id="booking-checkout-form" data-checkout-type="<?= Settings::instance()->get('checkout_customization'); ?>" method="post">
            <input type="hidden" name="payment_method" value="<?=!empty($realex_enabled) || !empty($stripe_enabled) ? 'cc' : ($cash_payment_enabled ? 'cash'  : '')?>" />

            <?php if (isset($event_object) && isset($order)): ?>
                <script>
                    window.event_items = <?=json_encode($order['items']) ?>;
                </script>
                <input type="hidden" name="ticket_id" id="ticket_id" value="<?= $order['ticket_id'] ?>" />
                <input type="hidden" name="event_id"     value="<?= $order['event_id']  ?>" />
                <input type="hidden" name="currency"     value="<?= $order['currency']  ?>" />
                <input type="hidden" name="total"        value="<?= $order['total']     ?>" />
                <input type="hidden" name="saveCheckout" value="0" id="saveCheckout" />

                <input type="hidden" id="checkout-success-redirect" value="<?= isset($success_redirect) ? $success_redirect : '/thanks-for-shopping-with-us.html' ?>" />
                <input type="hidden" name="skip_duplicate_test" value="0"  id="skip_duplicate_test"/>
                <?php if (@$partial_payment) { ?>
                    <input type="hidden" name="partial_payment_id" id="partial_payment_id" value="<?= $partial_payment['partial_payment']['id'] ?>" />
                <?php } ?>
            <?php endif; ?>

            <?php if (in_array($custom_checkout, ['bcfe'])): ?>
                <input type="hidden" name="is_interview" value="1" id="checkout-is_interview_booking" />
                <input type="hidden" name="course_title" value="<?= htmlentities($course_name) ?>" />
            <?php endif; ?>

            <div class="right-section hidden--checkout" id="right-section">
                <?php if (!empty($countdown_seconds)): ?>
                    <?php
                    $timer_hours   = floor($countdown_seconds / 3600);
                    $timer_minutes = floor(($countdown_seconds % 3600) / 60);
                    $timer_seconds = $countdown_seconds % 60;
                    ?>

                    <div class="form-group">
                        <div class="button button--full checkout-countdown"
                             id="checkout-countdown"
                             data-time="<?= $countdown_seconds ?>"
                             data-redirect="<?= isset($countdown_redirect) ? $countdown_redirect: '/' ?>"
                            >
                            <div<?= ($timer_hours <= 0) ? ' class="hidden"' : '' ?>>
                                <span class="checkout-countdown-label">Hour</span>
                                <span class="checkout-countdown-figure" id="checkout-countdown-hours"><?= ($timer_hours < 10 ? '0' : '').$timer_hours ?></span>
                            </div>

                            <div>
                                <span class="checkout-countdown-label">Min</span>
                                <span class="checkout-countdown-figure" id="checkout-countdown-minutes"><?= ($timer_minutes < 10 ? '0' : '').$timer_minutes ?></span>
                            </div>

                            <div>
                                <span class="checkout-countdown-label">Sec</span>
                                <span class="checkout-countdown-figure" id="checkout-countdown-seconds"><?= ($timer_seconds < 10 ? '0' : '').$timer_seconds ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="checkout-right-sect gray-box">
                    <?php $course_amend_fee_percent = Settings::instance()->get('course_amend_fee_percent'); ?>

                    <?php include Kohana::find_file('template_views', 'sidebar_cart'); ?>

                    <button type="button" class="button button--book<?= empty($count_seat_options) ? ' hidden' : '' ?>" data-toggle="slidein" data-target="#checkout-zone-selector">Select zones for your booking</button>

                    <div class="total-pay" style="<?=!$has_amendable ? 'display:none;' : ''?>">
                        <?php
                        $label = __('Amendable tier $1 extra.', array('$1' => $course_amend_fee_percent.'%'))
                            .'<br />'
                            .__('Amend your booking up to 48 hours before the beginning of the course');

                        $attributes = array('id' => 'checkout-amendable_tier');
                        if (!$has_amendable) {
                            $attributes['disabled'] = 'disabled';
                        }
                        echo Form::ib_checkbox($label, 'amendable', '1', false, $attributes);
                        ?>
                    </div>

                    <div class="total-pay<?= in_array($custom_checkout, ['bcfe', 'sls']) ? ' hidden' : '' ?>">
                        <?php $total = $sub_total - $discount + $zone_fee + $booking_fee; ?>

                        <?php
                        /*
                         * $price_breakdown should be set at controller level
                         * Older plugins do not use this and have their calculations scattered throughout the views
                         * They should be updated in future.
                         * For now, the next few lines are used to normalise data between plugins that use $price_breakdown and those that do not.
                         */
                        $currency_symbol = isset($price_breakdown['currency_symbol']) ? $price_breakdown['currency_symbol'] : $currency_symbol;
                        $discount        = isset($price_breakdown['discount'])        ? $price_breakdown['discount']        : $discount;
                        $sub_total        = isset($price_breakdown['subtotal'])        ? $price_breakdown['subtotal']        : $sub_total;
                        $zone_fee        = isset($price_breakdown['zone_fee'])        ? $price_breakdown['zone_fee']        : $zone_fee;
                        $booking_fees    = isset($price_breakdown['commission'])      ? $price_breakdown['commission']      : null;
                        $vat             = isset($price_breakdown['vat'])             ? $price_breakdown['vat']             : null;
                        $total           = isset($price_breakdown['total'])           ? $price_breakdown['total']           : $total;
                        if (!@$partial_payment && @$paymentplan) {
                            $sub_total = $deposit_breakdown['payment_amount'];
                            $booking_fees = $deposit_breakdown['fee'];
                            $vat = $deposit_breakdown['vat'];
                            $total = $deposit_breakdown['total'];
                        }
                        ?>

                        <?php if (!@$partial_payment && @$paymentplan) { ?>
                        <div>
                            <div class="form-row"></div>

                            <div class="form-row">
                                <label for="use_payment_plan_yes">
                                    <input type="radio" name="use_payment_plan" id="use_payment_plan_yes" value="<?=$event['id']?>" checked="checked" />
                                    Pay in installments
                                </label>
                            </div>

                            <input type="hidden" name="paymentplan_id" value="<?=$paymentplan[0]['tickettype_id']?>" />

                            <div class="form-row">
                                <label for="use_payment_plan_no">
                                    <input type="radio" name="use_payment_plan" id="use_payment_plan_no" value="0" />
                                    Pay full
                                </label>
                            </div>
                        </div>
                        <ul class="checkout-breakdown payment_plan_no hidden" id="checkout-breakdown-fullpayment">
                            <li>
                                <?= __('Subtotal') ?>

                                <span class="right">
                                    <?= $currency_symbol ?><span id="checkout-breakdown-subtotal" data-amount="<?= $fullpayment_price_breakdown['subtotal'] ?>"><?= number_format($fullpayment_price_breakdown['subtotal'], 2) ?></span>
                                </span>
                            </li>

                            <li<?= empty($booking_fees) ? ' style="display: none;"' : '' ?>>
                                <?= __('Booking fees') ?>
                                <span class="right">
                                    <?= $currency_symbol ?><span class="checkout-breakdown-booking_fee" data-amount="<?= $fullpayment_price_breakdown['commission'] ?>"><?= number_format($fullpayment_price_breakdown['commission'], 2) ?></span>
                                </span>
                            </li>

                            <li<?= empty($vat) ? ' style="display: none;"' : '' ?>>
                                <?= __('VAT') ?>
                                <span class="right">
                                    <?= $currency_symbol ?><span id="checkout-breakdown-vat" data-amount="<?= $fullpayment_price_breakdown['vat'] ?>"><?= number_format($fullpayment_price_breakdown['vat'], 2) ?></span>
                                </span>
                            </li>

                            <li class="group-total">
                                <strong>
                                    <?= __('Total') ?>
                                    <span class="right">
                                                &euro;<span
                                            id="checkout-group_booking-group_total"><?= $fullpayment_price_breakdown['total'] ?></span>
                                         </span>
                                </strong>
                            </li>
                        </ul>
                        <?php } ?>

                        <ul class="checkout-breakdown payment_plan_yes" id="checkout-breakdown">
                            <?php if (@$partial_payment == false) { ?>
                            <li>
                                <?= __('Subtotal') ?>

                                <?php

                                // Should setup a cleaner way of fetching this number
                                $number_of_delegates = !empty($cart_session_info['number_of_delegates']) ? $cart_session_info['number_of_delegates'] : 1;

                                if (!empty($_POST['number_of_delegates'])) {
                                    $number_of_delegates = $_POST['number_of_delegates'];
                                }
                                $quantity = (!empty($schedule) && $schedule->charge_per_delegate == 0) ? 1 : $number_of_delegates;

                                $sub_total = $sub_total;
                                ?>

                                <span class="right">
                                    <?= $currency_symbol ?><span id="checkout-breakdown-subtotal" data-amount="<?= $sub_total ?>"><?= number_format($sub_total, 2) ?></span>
                                </span>
                            </li>
                            <?php } ?>

                            <?php if (@$partial_payment) { ?>
                            <li>
                                <?= __('Partial Payment') ?>

                                <span class="right">
                                &euro;<span id="checkout-breakdown-total" data-total="<?= number_format($partial_payment['partial_payment']['payment_amount'], 2) ?>" data-amend-total="<?= number_format($partial_payment['partial_payment']['total'], 2) ?>"><?= number_format($partial_payment['partial_payment']['total'], 2) ?></span>
                                <input type="hidden" name="amount" value="<?= $partial_payment['partial_payment']['payment_amount'] ?>" id="checkout-breakdown-total-field" />
                            </span>
                            </li>
                            <?php } else { ?>
                                <li<?= $discount ? '' : ' class="hidden"' ?>>
                                <?php if ( ! empty($order['discount_type']) && ! empty($order['discount_type_amount'])): ?>
                                    <?= ($order['discount_type'] == 'Fixed') ? $currency_symbol.$order['discount_type_amount'] : ($order['discount_type_amount'] + 0).'%' ?> Discount
                                <?php else: ?>
                                    <?= __('Discount') ?>
                                <?php endif; ?>

                                <span class="right">
                                    &minus;<?= $currency_symbol ?><span id="checkout-breakdown-discount" data-amount="-<?=$discount?>"><?= number_format($discount, 2) ?></span>
                                </span>
                            </li>

                            <li<?= $zone_fee ? '' : ' class="hidden"' ?>>
                                <?= __('Zone fee') ?>

                                <span class="right">
                                    <?= $currency_symbol ?><span id="checkout-breakdown-zone_fee" data-amount="<?=$zone_fee?>"><?= number_format($zone_fee, 2) ?></span>
                                </span>
                            </li>

                            <?php
                            $payg_fee = 0;
                            if ( ! empty($payg_bookings)) {
                                $payg_fee = (float)Settings::instance()->get('course_payg_booking_fee');
                            ?>
                            <li class="checkout-payg_fee-wrapper<?= $payg_fee == 0 ? ' hidden' : '' ?>">
                                <?= __('PAYG fee') ?>

                                <span class="right">
                                    <?= $currency_symbol ?><span class="checkout-breakdown-booking_fee" data-amount="<?=$payg_fee?>"><?= number_format($payg_fee, 2) ?></span>
                                </span>
                            </li>
                            <?php
                            }
                            ?>

                            <?php
                            $cc_fee = (float)Settings::instance()->get('course_cc_booking_fee');
                            if ($sub_total == 0 && $payg_fee == 0) {
                                $cc_fee = 0;
                            }
                            ?>
                            <?php if ($cc_fee): ?>
                                <li class="booking_fee cc <?=$payment_method != 'cc' || $cc_fee == 0 ? 'hidden' : ''?>">
                                    <?= __('Card fee') ?>

                                    <span class="right">
                                        <?= $currency_symbol ?><span class="checkout-breakdown-booking_fee" data-amount="<?=$cc_fee?>"><?= number_format($cc_fee, 2) ?></span>
                                    </span>
                                </li>
                            <?php endif; ?>

                            <?php
                            $sms_fee = (float)Settings::instance()->get('course_sms_booking_fee');
                            if ($sub_total == 0 && $payg_fee == 0) {
                                $sms_fee = 0;
                            }
                            ?>
                            <li  class="booking_fee sms <?=$payment_method != 'sms' || $sms_fee == 0 ? 'hidden' : ''?>">
                                <?= __('SMS fee') ?>

                                <span class="right">
                                    <?= $currency_symbol ?><span class="checkout-breakdown-booking_fee" data-amount="<?=$sms_fee?>"><?= number_format($sms_fee, 2) ?></span>
                                </span>
                            </li>

                            <li class="amend-fee" style="display: none" data-amend-fee="<?=$amend_fee?>" data-amount="<?=$amend_fee?>">
                                <?= __('Amend Fee') ?>

                                <span class="right">
                                    &euro;<span id="checkout-breakdown-amend_fee"><?= number_format($amend_fee, 2) ?></span>
                                </span>
                            </li>

                            <li<?= empty($booking_fees) ? ' style="display: none;"' : '' ?>>
                                <?= __('Booking fees') ?>
                                <span class="right">
                                    <?= $currency_symbol ?><span class="checkout-breakdown-booking_fee" data-amount="<?= $booking_fees ?>"><?= number_format($booking_fees, 2) ?></span>
                                </span>
                            </li>

                            <li  class="booking_fee interest hidden">
                                <?= __('Interest') ?>
                                <span class="right">
                                &euro;<span class="checkout-breakdown-booking_fee" data-amount=""></span>
                                </span>
                            </li>

                            <li<?= empty($vat) ? ' style="display: none;"' : '' ?>>
                                <?= __('VAT') ?>
                                <span class="right">
                                    <?= $currency_symbol ?><span id="checkout-breakdown-vat" data-amount="<?= $vat ?>"><?= number_format($vat, 2) ?></span>
                                </span>
                            </li>

                                <?php if (Settings::instance()->get('course_checkout_coupons')): ?>
                                    <li>
                                        <div class="checkout-coupon-wrapper">
                                            <div>
                                                <?= Form::ib_input('Coupon code', 'coupon_code', null, ['id' => 'checkout-coupon_code']); ?>
                                            </div>

                                            <div>
                                                <button type="button" id="checkout-apply_coupon_code" class="button form-btn">Apply</button>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>

                                <li class="sub-total">
                                    <?php
                                    if (!@$partial_payment && @$paymentplan) {
                                        echo __('Total today');
                                    }
                                    elseif (!empty($payg_bookings)) {
                                        echo __('Total due now');
                                    }
                                    else {
                                        echo __('Total');
                                    }
                                    ?>
                                <span class="right">

                                    <?php $total = $total * $quantity; ?>

                                    <?= $currency_symbol ?><span id="checkout-breakdown-total" data-total="<?= number_format($total, 2) ?>" data-amend-total="<?= number_format($total + $amend_fee, 2) ?>"><?= number_format($total, 2) ?></span>
                                    <input type="hidden" name="amount" value="<?= number_format($total,2)?>" id="checkout-breakdown-total-field" />
                                </span>
                            </li>
                            <?php if ((!empty($partial_payment) || !empty($paymentplan)) && isset($price_breakdown)): ?>
                             <li class="group-total">
                                     <strong>
                                            <?= __('Group total') ?>
                                            <span class="right">
                                                    &euro;<span
                                                        id="checkout-group_booking-group_total"><?= $price_breakdown['total'] ?></span>
                                             </span>
                                      </strong>
                                </li>
                            <?php endif; ?>
                            <?php } ?>
                            <li  class="booking_fee deposit_due_now hidden">
                                <?= __('Deposit Due Total') ?>
                                <span class="right">
                            &euro;<span class="checkout-breakdown-booking_fee" data-amount=""></span>
                            </span>
                            </li>
                        </ul>
                    </div>

                    <?php if (@$partial_payment || @$paymentplan) { ?>
                    <div class="panel <?=@$partial_payment || @$paymentplan ? '' : 'hidden'?> payment_plan_yes">
                        <div class="panel-heading item-summary-head">
                            <button
                                type="button"
                                class="button--plain right"
                                data-hide_toggle="#checkout-group_booking_info"
                                data-show_text="<?= htmlspecialchars('<span class="fa fa-angle-down"></span>'.__('show')) ?>"
                                data-hide_text="<?= htmlspecialchars('<span class="fa fa-angle-up"></span>&nbsp;'.__('hide')) ?>"
                            >
                                <span class="fa fa-angle-down"></span>&nbsp;<?= __('show') ?>
                            </button>

                            <div><?= __('Group booking info') ?></div>
                        </div>

                        <div class="panel-body hidden" id="checkout-group_booking_info">
                            <div class="total-pay">
                                <ul>
                                    <li>
                                        <?= __('Subtotal') ?>
                                        <span class="right">
                                            &euro;<span id="checkout-group_booking-subtotal"><?= number_format($price_breakdown['subtotal'], 2) ?></span>
                                        </span>
                                    </li>

                                    <li>
                                        <?= __('Booking fees') ?>
                                        <span class="right">
                                            &euro;<span id="checkout-group_booking-booking_fees"><?= number_format($price_breakdown['commission'], 2) ?></span>
                                        </span>
                                    </li>

                                    <li>
                                        <?= __('VAT') ?>
                                        <span class="right">
                                        &euro;<span id="checkout-group_booking-vat"><?= number_format($price_breakdown['vat'], 2) ?></span>
                                    </span>
                                    </li>

                                    <li>
                                        <strong>
                                            <?= __('Group total') ?>
                                            <span class="right">
                                                &euro;<span id="checkout-group_booking-group_total"><?= number_format($price_breakdown['total'], 2) ?></span>
                                            </span>
                                        </strong>
                                    </li>
                                </ul>
                            </div>

                            <div class="total-pay">
                                <h4><?= __('Payment plan') ?></h4>

                                <?php if (@$paymentplan) { ?>


                                <?php } ?>
                                <ul>
                                    <?php
                                    $today = date('Y-m-d');
                                    $outstanding = 0;
                                    ?>

                                    <?php
                                    if (@$partial_payment || $paymentplan) {
                                        if (@$partial_payment['paymentplan']) {
                                            $paymentplan = @$partial_payment['paymentplan'];
                                        }
                                        foreach ($paymentplan as $i => $payment):
                                    ?>
                                        <?php
                                        if (!$payment['payment_id']) {
                                            $outstanding += $payment['total'];
                                        }
                                        ?>
                                        <?php $due_today = (date('Y-m-d', strtotime($payment['due_date'])) == $today); ?>

                                        <li<?= ($payment['payment_id'] || !$due_today) ? ' class="text-disabled"' : '' ?> id="paymentplan_id_<?=$payment['id']?>">
                                            <div>
                                                <?= $payment['title'] . ($payment['payer_name'] ? ' - ' . $payment['payer_name'] : '') ?>

                                                <?php ob_start(); ?>
                                                    <?= Model_Event::currency_symbol($order['currency']) ?><?php
                                                    ?><span class="checkout-group_booking-payment-amount"><?= number_format($payment['total'], 2) ?></span>
                                                <?php $amount_html = ob_get_clean(); ?>

                                                <div class="right">
                                                    <?= ($payment['payment_id']) ? '<s>'.$amount_html.'</s>' : $amount_html; ?>
                                                </div>
                                            </div>

                                            <div class="checkout-group_booking-payment-due text-right">
                                                <?php $number_of_payments = $payments ? count($payments) : 0 ?>
                                                <small<?= ($i == $number_of_payments - 1) ? ' style="border-bottom: 1px solid;"' : '' ?>>
                                                    &nbsp;
                                                    <?php if ($payment['payment_id']): ?>
                                                        <?= __('Paid. Thank you.') ?>
                                                    <?php else: ?>
                                                        <?php ob_start(); ?>
                                                        <span title="<?= date('jS F Y', strtotime($payment['due_date'])) ?>">
                                                            <?= ($due_today || $payment['due_date'] == null || $payment['due_date'] == '0000-00-00 00:00:00') ? __('today') : date('j/n/Y', strtotime($payment['due_date'])) ?>
                                                        </span>
                                                        <?php $due_date_text = ob_get_clean(); ?>

                                                        <?= __('due $1', array('$1' => $due_date_text)) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php } ?>

                                    <li>
                                        <span class="text-uppercase"><?= __('Outstanding') ?></span>

                                        <span class="right">
                                            <?=Model_Event::currency_symbol($order['currency'])?><span class="checkout-group_booking-outstanding"><?= number_format($outstanding, 2) ?></span>
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <div class="total-pay checkout-group_booking-pay_more hidden"><?php // Not ready yet. Hidden for UT-3810.  ?>
                                <p><?= __('Would you like to pay more?') ?></p>

                                <div class="form-group gutters">
                                    <div class="col-xs-6">
                                        <?= Form::ib_input(__('Enter amount'), 'paymore_amount') ?>
                                    </div>

                                    <div class="col-xs-6">
                                        <button type="button" class="button button--continue button--full" id="paymore_update">
                                            <?= __('Update cart') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="purchase-packages hidden"></div>

                    <?php if (!empty($brochure) || !empty($payments_enabled)): ?>
                        <?php if (!in_array($custom_checkout, ['bcfe'])): ?>
                            <div class="terms-txt page-content">
                                <?php if (@$partial_payment || @$paymentplan): // replace with boolean for group booking ?>
                                    <div class="form-row gutters">
                                        <div class="col-xs-2">
                                            <?php
                                            $attributes = array('class' => 'validate[required]', 'id' => 'checkout-group_booking-terms_and_conditions');
                                            echo Form::ib_checkbox(null, 'group_booking_terms_and_conditions', null, null, $attributes);
                                            ?>
                                        </div>

                                        <div class="col-xs-10">
                                            <label for="checkout-group_booking-terms_and_conditions">
                                                <?= __('I understand that I am booking an entire house/camper van site for the amount of people shown. I also understand that the total group price must be paid in full before my tickets are 100% confirmed. Failure to meet my next instalment or subsequent instalments will result in my tickets being cancelled without a refund.') ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="form-row gutters">
                                    <div class="col-xs-2">
                                        <?php
                                        $attributes = array('class' => 'validate[required]', 'id' => 'checkout-terms_and_conditions');
                                        echo Form::ib_checkbox(null, 'terms_and_conditions', null, null, $attributes);
                                        ?>
                                    </div>
                                    <div class="col-xs-10">
                                        <label for="checkout-terms_and_conditions">
                                            <?php
                                            $terms_and_conditions_text = trim(Model_Localisation::get_ctag_translation(Settings::instance()->get('checkout_terms_and_conditions')));

                                            if ($terms_and_conditions_text) {
                                                echo $terms_and_conditions_text;
                                            } else {
                                                echo '<p>'.__('I understand that fees are non-refundable and non-transferable.').'<br />'.__('By clicking ‘Complete booking’ $1 you agree to the $2.',
                                                    array(
                                                        '$1' => '<br />',
                                                        '$2' => '<a href="/terms-and-conditions.html" target="_blank">'.__('terms and conditions').'</a>'
                                                    )).'</p>';
                                            }
                                            ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php ob_start(); ?>
                            <?php
                            $free_trial = isset($schedule) && $schedule->trial_timeslot_free_booking == 1;
                            $full_time  = isset($schedule) && $schedule->course->is_fulltime == 'YES';

                            if ($total <= 0 && $free_trial) {
                                $book_text = 'Free trial';
                            } elseif ($full_time) {
                                $book_text = 'Apply';
                            } else {
                                $book_text = 'Complete booking';
                            }
                            ?>
                            <button
                                type="button"
                                data-book_text="<?= htmlspecialchars($book_text) ?>"
                                data-sales_quote_text="<?= htmlspecialchars(__('Send me a sales quote')) ?>"
                                class="button button--continue btn-primary <?= empty($event_object) ? 'btn--full checkout-complete_booking' : '' ?>"
                                id="continue_chkout_btn"
                                <?= !empty($event_object) ? 'name="action" value="buy"' : '' ?>
                            >
                                <?= htmlspecialchars($book_text) ?>
                            </button>
                        <?php $submit_button = ob_get_clean(); ?>

                        <?php if (Settings::instance()->get('captcha_enabled') == 1 && Settings::instance()->get('captcha_frontend_checkout_position') === 'cart_section'): ?>
                            <div class="checkout-captcha-container" style="transform: scale(0.70); transform-origin: 15px 15px;">
                                <?= Form::ib_captcha(2); ?>
                            </div>
                        <?php endif; ?>

                        <div class="button-action">
                            <?= $submit_button ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="left-section contact--left hidden--your_booking">
                <?php if (in_array($custom_checkout, ['bcfe', 'sls'])): ?>
                    <div class="theme-form">
                        <h3 class="checkout-heading contact-details-heading">
                            <span class="fa fa-book"></span>
                            <?= __('Choose your course') ?>
                        </h3>

                        <div class="theme-form-content">
                            <div class="theme-form-inner-content">
                                <div class="form-group">
                                    <?php if ($custom_checkout == 'bcfe'): ?>
                                        <div class="col-sm-6">
                                            <?php
                                            $options = Model_Courses::get_course_list_code(['publish' => 1]);
                                            $options = ['' => '-- Please select --'] + $options;
                                            $attributes = ['class' => 'checkout-course-selector', 'id' => 'checkout-course'];
                                            echo Form::ib_select(null, 'course_code', $options, $course_code, $attributes);
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($custom_checkout == 'sls'): ?>
                                        <div class="col-sm-6">
                                            <?php
                                            $categories = Model_Categories::get_categories_without_parent();
                                            $options = HTML::optionsFromRows('id', 'category', $categories, null, ['value' => '', 'label' => '']);
                                            $attributes = ['id' => 'checkout-course-category'];
                                            echo Form::ib_select(__('Category'), 'category_id', $options, null, $attributes);
                                            ?>
                                        </div>

                                        <div class="col-sm-6">
                                            <?php
                                            $courses = Model_Courses::get_all_published(['publish' => 1]);
                                            $options = '<option></option>';
                                            foreach ($courses as $course) {
                                                $options .= '<option value="'.$course['id'].'" data-category_id="'.$course['category_id'].'">'.$course['title'].'</option>';
                                            }
                                            $attributes = ['class' => 'checkout-course-selector', 'id' => 'checkout-course'];
                                            echo Form::ib_select(__('Course'), 'course_id', $options, $course_code, $attributes);
                                            ?>
                                        </div>

                                        <script>
                                            $('#checkout-course-category').on('change', function() {
                                                var category_id = this.value;
                                                var $courses = $('#checkout-course');
                                                $courses.find('option[data-category_id]').toggleClass('hidden', (category_id === '') ? false : true);
                                                $courses.find('option[data-category_id="'+category_id+'"]').removeClass('hidden');
                                                if ($courses.find(':selected').hasClass('hidden') || !$courses.val()) {
                                                    $courses.val($courses.find('option[data-category_id="' + category_id + '"]').first().val()).change();
                                                }
                                            });
                                        </script>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($brochure) || (!empty($payments_enabled))): ?>
                    <?php
                    include Kohana::find_file('template_views', 'checkout_contact_details');
                    if (!empty($payments_enabled)) {
                        include Kohana::find_file('template_views', 'checkout_pay_with');
                    }
                    ?>
                    <div class="hidden--mobile hidden--tablet hidden--desktop pb-4"><?php // tablet only ?>
                        <p><?= __(
                                'By clicking ‘Complete booking’ $1 you agree to the $2.',
                                array(
                                    '$1' => '',
                                    '$2' => '<a href="/terms-and-conditions.html" target="_blank">'.__('terms and conditions').'</a>'
                                )
                            ) ?></p>

                        <button
                            type="button"
                            class="button button--continue btn-primary checkout-complete_booking"
                            data-book_text="<?= htmlspecialchars($total > 0 || !empty($payg_bookings) ? 'Complete booking' : ((!empty($prepay_bookings)) ? 'Free trial' : 'Apply')) ?>"
                            data-sales_quote_text="<?= htmlspecialchars(__('Send me a sales quote')) ?>"
                        ><?= __('Complete Booking') ?></button>
                    </div>

                    <div class="hidden--tablet hidden--desktop"><?php // mobile only ?>
                        <button type="button" class="button button--continue button--full" id="checkout-continue"><?= __('Continue') ?></button>
                    </div>

                <?php else: ?>
                    <p><?= __('No payment providers have currently been set up.') ?></p>
                    <p><?= __('Please $1 for more information', array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')) ?></p>
                <?php endif; ?>
            </div><?php // contact left side end ?>

            <div class="slidein" id="checkout-zone-selector">
                <div class="slidein-content">
                    <div class="slidein-header">
                        <h2><?= __('Please select your zone') ?></h2>
                    </div>

                    <div class="slidein-body">
                        <label class="select">
                            <select class="form-input" id="seating-selector-select_schedule">
                                <?php $i = 0; ?>
                                <?php foreach ($all_bookings as $schedule_id_and_time => $booking_events): ?>
                                    <?php foreach ($booking_events as $booking): ?>
                                        <?php if (!empty($booking['zones'])): ?>
                                            <option value="<?= $i ?>">
                                                <?= $booking['schedule']['name'] ?> -
                                                <?= $booking['schedule']['location'] ?> -
                                                <?= $booking['date_formatted'] ?> -
                                                <?= date('H:i', strtotime($booking['event']['datetime_start'])) ?>
                                            </option>

                                            <?php $i++; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <?php $i = 0; ?>

                        <?php foreach ($all_bookings as $schedule_id_and_time => $booking_events): ?>
                            <?php foreach ($booking_events as $event_id => $booking): ?>
                                <?php if (!empty($booking['zones'])): ?>
                                    <div class="seating-selector<?= ($i == 0) ? '' : ' hidden' ?>" data-booking="<?= $i ?>">
                                        <div class="row gutters">
                                            <div class="seating-selector-map">
                                                <div class="text-center">
                                                    <p><span class="fa fa-user" style="font-size: 3em;"></span></p>

                                                    <p><?= __('Teacher') ?></p>
                                                </div>

                                                <div class="seating-selector-map-body">
                                                    <?php foreach ($booking['zones'] as $zone): ?>
                                                        <div class="seating-selector-row" data-row_id="<?= $zone['row_id'] ?>" data-zone_id="<?= $zone['zone_id'] ?>">
                                                            <label class="seating-selector-option">
                                                                <input
                                                                    type="radio"
                                                                    class="sr-only seating-selector-option-radio"
                                                                    name="booking_items[<?= $booking['schedule_id'] ?>][<?= $event_id ?>][seat_row_id]"
                                                                    value="<?=          $zone['row_id']             ?>"
                                                                    data-row_id="<?=    $zone['row_id']             ?>"

                                                                    <?php if (Auth::instance()->has_access('courses_bookings_see_seating_numbers')): ?>
                                                                        data-total="<?=     $zone['seats']['total']     ?>"
                                                                        data-booked="<?=    $zone['seats']['booked']    ?>"
                                                                        data-available="<?= $zone['seats']['available'] ?>"
                                                                    <?php endif; ?>

                                                                    data-currency="&euro;"
                                                                    data-price="<?=     number_format($zone['price'], 2) ?>"
                                                                    <?= $zone['seats']['available'] > 0 ? '' : 'disabled="disabled"' ?>
                                                                    />

                                                                <span class="button button--book seating-selector-zone-button inverse">
                                                                    <?= $zone['zone_name'] ?>
                                                                    <?= $zone['seats']['available'] > 0 ? '' : '('.__('Full').')' ?>
                                                                </span>

                                                                <?php if ($zone['seats']['available'] > 0): ?>
                                                                    <span class="seating-selector-option-hover">
                                                                        <?= __('$1 additional', array('$1' => '&euro;'.number_format($zone['price'], 2))) ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <div class="seating-selector-row">
                                                    <label class="seating-selector-option">
                                                        <input
                                                            type="radio"
                                                            class="seating-selector-option-radio sr-only"
                                                            name="booking_items[<?= $booking['schedule_id'] ?>][<?= $event_id ?>][seat_row_id]"
                                                            value=""
                                                            />
                                                        <span class="seating-selector-checkbox-helper"></span>

                                                        <span class="seating-selector-option-name"><?= __('I don\'t mind where I sit') ?></span>
                                                    </label>
                                                </div>

                                                <?php if ($count_seat_options > 1): ?>
                                                    <div class="seating-selector-footer">
                                                        <button
                                                            type="button"
                                                            class="button button--send inverse seating-selector-prev"
                                                            <?= $i == 0 ? ' disabled="disabled"' : '' ?>>
                                                            <?= __('Previous') ?></button>
                                                        <button
                                                            type="button"
                                                            class="button button--send inverse seating-selector-next"
                                                            <?= $i == $count_seat_options - 1 ? ' disabled="disabled"' : '' ?>
                                                            ><?= __('Next') ?></button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php $i++; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="slidein-footer">
                        <button type="button" class="button button--book" data-dismiss="slidein"><?= __('Done') ?></button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div><?php // .row ?>
</div><?php // .container ?>
<?php
$last_search_parameters = Session::instance()->get('last_search_params');
$guest_redirect = '/available-results.html'.($last_search_parameters != null ? http_build_query($last_search_parameters) : '');
include Kohana::find_file('template_views', 'login_overlay');

echo View::factory('front_end/snippets/modal')
    ->set('id',    'checkout_save_details_modal')
    ->set('width', '500px')
    ->set('body',   '<p>'.__('Would you like to save your updated contact details for speedy checkout next time?').'</p>')
    ->set('footer', '<div class="modal-buttons">
        <button type="button" class="button button--continue" data-saveCheckout="1">'.__('Yes').'</button>
        <button type="button" class="button button--continue inverse" data-saveCheckout="0">'.__('No').'</button>
    </div>')
;

echo View::factory('front_end/snippets/modal')
    ->set('id',    'checkout_duplicate_modal')
    ->set('width', '500px')
    ->set('body',   '<p class="duplicate_item"></p><p>'.__('Do you want to continue').'</p>')
    ->set('footer', '<div class="modal-buttons">
        <button type="button" class="button button--continue yes" data-saveCheckout="1">'.__('Yes').'</button>
        <button type="button" class="button button--continue inverse no" data-saveCheckout="0">'.__('No').'</button>
    </div>')
;

echo View::factory('front_end/snippets/modal')
    ->set('id',    'checkout_error_modal')
    ->set('width', '500px')
    ->set('title',  '<h3>'.__('Checkout error').'</h3>')
    ->set('body',   '<div id="checkout-error_modal-message"></div>')
    ->set('footer', '<button type="button" class="button cancel" data-close>'.__('Review').'</button>')
;


?>
<div class="ajax_loader hidden" id="checkout-ajax_loader"></div>

<!-- popup hover jquery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<!-- tab jquery -->
<script src="<?= URL::get_engine_assets_base() ?>js/jquery-ui.js"></script>
<?php if (!empty($event_object)): ?>
    <script src="<?= URL::get_engine_plugin_assets_base('events') ?>js/buy_ticket.js?ts=<?= filemtime(ENGINEPATH.'plugins/events/development/assets/js/buy_ticket.js') ?>"></script>
<?php endif; ?>

<?php if (isset($page_object) && $page_object->footer): ?>
    <div class="row page-content">
        <?= Ibhelpers::parse_page_content($page_object->footer) ?>
    </div>
<?php endif; ?>

<?php
if (trim($site_template->footer)) {
    eval('?>'.$site_template->footer);
} else {
    include Kohana::find_file('views', 'footer');
}
?>

<script>
    function force_change(selector, value) {
        var sortBySelect = document.querySelector(selector);
        sortBySelect.value = value;
        sortBySelect.dispatchEvent(new Event("change"));
    }
    $(document).on('ready', function() {
        $('input[name=amendable]').on('change', function() {
            var $total = $('#checkout-breakdown-total');

            if (this.checked) {
                $total.html($total.data('amend-total'));
                $('li.amend-fee').css('display', 'block');
            } else {
                $total.html($total.data('total'));
                $('li.amend-fee').css('display', 'none');
            }
        });
        var selects = $('select');
        $.each(selects, function(key, select){
            console.log($(select).attr('readonly'));
            if ($(select).attr('readonly')) {
                $(select).closest('.form-input').addClass('readonly');
                var existing_options = $(select).find('option');
                $.each(existing_options, function(key, existing_option){
                    if (!$(existing_option).is(':selected')) {
                        $(existing_option).attr('disabled', 'disabled');
                    }
                })
            }
        });
    });

    $('#org-rep-delegate-confirmation').on('change', function () {
        var delegate_box = $(".delegate_box").first();
        var delegates = $(".delegate_box").length;
        if (this.checked) {
            if ($('#checkout-first_name').length > 0) {
                delegate_box.find("#checkout-student_first_name_0").addClass("form-input").prop('readonly', true).val($('#checkout-first_name').val()).change();
                delegate_box.find("#checkout-student_last_name_0").addClass("form-input").prop('readonly', true).val($('#checkout-last_name').val()).change();
                $.when(
                    delegate_box.find("#checkout-student_email_0").addClass("form-input").prop('readonly', true).val($('input[name="email"]').val()).change()).
                then(function(){
                    $('input[name=mobile_dial_code]').val($('#checkout-mobile-code').val());
                    delegate_box.find("#checkout-student_mobile-international_code_0").addClass("form-input").prop('readonly', true).val('').change();
                });
                delegate_box.find("#checkout-student_mobile-international_code_0").addClass("form-input").prop('readonly', true).val($('#checkout-mobile-international_code').val()).change();
                var $select = $('#checkout-mobile-international_code');
                    var $new_select = delegate_box.find("#checkout-student_mobile-international_code_0");
                    $new_select.attr('readonly', true);
                    console.log($new_select.parents('.form-input'));
                    $new_select.parents('.form-input').addClass('readonly');
                    var existing_options = $new_select.find('option');
                    $.each(existing_options, function(key, existing_option){
                        if (!$(existing_option).is(':selected')) {
                            $(existing_option).attr('disabled', 'disabled');
                        }
                    });
                $('input[name=mobile_dial_code]').val($('#checkout-mobile-code').val());
                delegate_box.find("#checkout-student_mobile-code_0").addClass("form-input").prop('readonly', true).val($('#checkout-mobile-code').val()).change();
                delegate_box.find("#checkout-mobile-number_0").addClass("form-input").prop('readonly', true).val($('#checkout-mobile-number').val()).change();
                delegate_box.find("#checkout-student_first_name_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-student_last_name_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-student_email_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-student_mobile-code_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-mobile-number_0").parents('.form-input').addClass('readonly');


            } else {
                delegate_box.find("#checkout-student_first_name_0").addClass("form-input").prop('readonly', true).val($('#checkout-first_name_').val()).change();
                delegate_box.find("#checkout-student_last_name_0").addClass("form-input").prop('readonly', true).val($('#checkout-last_name_').val()).change();
                delegate_box.find("#checkout-student_email_0").addClass("form-input").prop('readonly', true).val($('input[name="email[]"]').val()).change();
                delegate_box.find("#checkout-student_mobile-international_code_0").addClass("form-input").prop('readonly', true).val($('#checkout-mobile-international_code_').val()).change();
                var $select = $('#checkout-mobile-international_code_');
                if ($select.attr('readonly')) {
                    var $new_select = delegate_box.find("#checkout-student_mobile-international_code_0");
                    $new_select.attr('readonly', true);
                    console.log($new_select.parents('.form-input'));
                    $new_select.parents('.form-input').addClass('readonly');
                    var existing_options = $new_select.find('option');
                    $.each(existing_options, function(key, existing_option){
                        if (!$(existing_option).is(':selected')) {
                            $(existing_option).attr('disabled', 'disabled');
                        }
                    })
                }
                if ($('#checkout-mobile-code_').length > 0 ) {
                    $('input[name=mobile_dial_code]').val($('#checkout-mobile-code_').val());
                } else {
                    $('input[name=mobile_dial_code]').val($('#checkout-mobile-code').val());
                }
                delegate_box.find("#checkout-student_mobile-code_0").addClass("form-input").prop('readonly', true).val($('#checkout-mobile-code_').val()).change();
                delegate_box.find("#checkout-mobile-number_0").addClass("form-input").prop('readonly', true).val($('#checkout-mobile-number_').val()).change();
                delegate_box.find("#checkout-student_first_name_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-student_last_name_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-student_email_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-student_mobile-code_0").parents('.form-input').addClass('readonly');
                delegate_box.find("#checkout-mobile-number_0").parents('.form-input').addClass('readonly');
            }
           } else {
            delegate_box.find("#checkout-student_first_name_0").removeClass("form-input").prop('readonly', false).val('').change();
            delegate_box.find("#checkout-student_last_name_0").removeClass("form-input").prop('readonly', false).val('').change();
            delegate_box.find("#checkout-student_email_0").removeClass("form-input").prop('readonly', false).val('').change();
            $('input[name=mobile_dial_code]').val('');
            delegate_box.find('select').find('option').removeAttr('disabled');
            delegate_box.find("#checkout-student_mobile-international_code_0").removeClass("form-input").prop('readonly', false).val('353').change();
            delegate_box.find("#checkout-student_mobile-code_0").removeClass("form-input").prop('readonly', false).val('').change();
            delegate_box.find("#checkout-mobile-number_0").removeClass("form-input").prop('readonly', false).val('').change();
            delegate_box.find("#checkout-student_first_name_0").parents('.form-input').removeClass('readonly');
            delegate_box.find("#checkout-student_last_name_0").parents('.form-input').removeClass('readonly');
            delegate_box.find("#checkout-student_email_0").parents('.form-input').removeClass('readonly');
            delegate_box.find("#checkout-student_mobile-international_code_0").parents('.form-input').removeClass('readonly');
            delegate_box.find("#checkout-student_mobile-code_0").parents('.form-input').removeClass('readonly');
            delegate_box.find("#checkout-mobile-number_0").parents('.form-input').removeClass('readonly');
        }
    });

    $(document).on('change', '.checkout-mobile-international_code ', function(){
        var country_code = $(this).val();
        var country_code_id = $(this).attr('id');
        if (country_code) {
            $.ajax({
                url:'/frontend/contacts3/ajax_get_dial_codes',
                data:{
                    country_code : country_code
                },
                type: 'POST',
                dataType:'json'
            }).done(function(data){
                var input = '';
                var select = '';
                var checkout_number = '';
                var student_number = '';
                if (data.length == 0) {
                    $('#'+country_code_id).closest('.form-group').find('.checkout-mobile-code').closest('.form-select').remove();
                    $('#'+country_code_id).closest('.form-group').find('.checkout-mobile-code').closest('.form-input').remove();
                    if (country_code_id.includes('student')) {
                         student_number = country_code_id.split('_').slice(-1)[0];
                        if (student_number == '' || student_number == undefined) {
                            student_number = '';
                        }
                        input =
                            '   <label class="form-input form-input--text form-input--pseudo form-input--active">' +
                            '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                            '        <input type="text" id="checkout-student_mobile-code_' + student_number + '" ' +
                            'name="student_mobile_code[]" value="" class="checkout-mobile-code validate[required]" ' +
                            'placeholder="Code: *">' +
                            '    </label>';
                    } else {
                         checkout_number = country_code_id.split('_').slice(-1)[0];
                        if (checkout_number == '' || checkout_number == undefined) {
                            checkout_number = '';
                        }
                         input =
                                '   <label class="form-input form-input--text form-input--pseudo form-input--active">' +
                                '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                                '        <input type="text" id="checkout-mobile-code_' + checkout_number + '" ' +
                                'name="mobile_code[]" value="" class="checkout-mobile-code validate[required]" ' +
                                'placeholder="Code: *">' +
                                '    </label>';
                    }

                    $('#' + country_code_id).closest('.form-group').find('.checkout-mobile-code-wrapper').append(input);
                    var code_selected = $('input[name=mobile_dial_code]').val();
                    if (code_selected) {

                        var $code_new_el =  $('#'+country_code_id).closest('.form-group').find('input.checkout-mobile-code');

                        if ($code_new_el.length == 0) {
                            $code_new_el = $('#'+country_code_id).closest('.form-group').find('select.checkout-mobile-code');
                        }


                        $code_new_el.val(code_selected);
                        $code_new_el.prop('readonly', true);
                        if ($code_new_el.is('select')) {
                            var new_code_options = $code_new_el.find('option');
                            $.each(new_code_options, function(key, new_code_option){
                                if (!$(new_code_option).attr('value') != code_selected) {
                                    $(new_code_option).attr('disabled', true);
                                }
                            });
                        } else {
                            $code_new_el.find('readonly', true);
                            $code_new_el.closest('.form-input').addClass('readonly');
                        }
                    }
                } else {
                    console.log(country_code_id);
                    if (!$('#'+country_code_id).closest('.form-group').find('.checkout-mobile-code').is("select")) {
                        $('#' + country_code_id).closest('.form-group').find('.checkout-mobile-code').closest('.form-select').remove();
                        $('#' + country_code_id).closest('.form-group').find('.checkout-mobile-code').closest('.form-input').remove();
                        if (country_code_id.includes('student')) {
                             student_number = country_code_id.split('_').slice(-1)[0];
                            if (student_number == '' || student_number == undefined) {
                                student_number = '';
                            }
                                 select = '<label class="form-select">' +
                                    '        <span class="form-input form-input--select form-input--pseudo">' +
                                    '            <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                                    '            <select id="checkout-student_mobile-code_'+ student_number +'" ' +
                                    '   name="student_mobile_code[]" class="checkout-mobile-code validate[required]" ' +
                                    'readonly="">' +
                                    '<option value="" selected="selected"></option>' +
                                    '</select>       ' +
                                    ' </span>' +
                                    '    </label>';
                        } else {
                             checkout_number = country_code_id.split('_').slice(-1)[0];
                            if (checkout_number == '' && checkout_number == undefined) {
                                checkout_number = '';
                            }
                                 select  = ' <label class="form-select">'+
                                    '   <span class="form-input form-input--select form-input--pseudo">' +
                                    '  <span class="form-input--pseudo-label">Code</span>' +
                                    '   <select id="checkout-mobile-code_'+ checkout_number +'" name="mobile_code[]" ' +
                                    'class="checkout-mobile-code " >' +
                                    '   <option value="" selected="selected"></option>'+
                                    '                        </select>        </span>' +
                                    '                        </label>';
                        }
                        $('#' + country_code_id).closest('.form-group').find('.checkout-mobile-code-wrapper').append(select);

                    }
                    $('#'+country_code_id).closest('.form-group').find('.checkout-mobile-code').find('option').remove();
                    $('#'+country_code_id).closest('.form-group').find('.checkout-mobile-code').append('<option value=""></option>');
                    $.each(data, function(key, code){
                        var option = '<option value="' + code.dial_code+'">'+code.dial_code+'</option>';
                        $('#'+country_code_id).closest('.form-group').find('.checkout-mobile-code').append(option);
                    });
                    var code_selected = $('input[name=mobile_dial_code]').val();
                    if (code_selected) {

                        var $code_new_el =  $('#'+country_code_id).closest('.form-group').find('input.checkout-mobile-code');
                        if ($code_new_el.length == 0) {
                            $code_new_el = $('#'+country_code_id).closest('.form-group').find('select.checkout-mobile-code');
                        }
                        $code_new_el.val(code_selected);
                        $code_new_el.prop('readonly', true);
                        if ($code_new_el.is('select')) {
                            var new_code_options = $code_new_el.find('option');
                            $.each(new_code_options, function(key, new_code_option){
                                if ($(new_code_option).attr('value') != code_selected) {
                                    $(new_code_option).attr('disabled', true);
                                }
                            });
                        } else {
                            $code_new_el.find('readonly', true);
                            $code_new_el.closest('.form-input').addClass('readonly');
                        }
                        $('input[name=mobile_dial_code]').val('');
                    }
                }
            });
        }
    });
    
    function update_billing_details(checked) {
        if (checked) {
            $('#billing_first_name').addClass("form-input").prop('readonly', true).val($('#checkout-first_name').val());
            $('#billing_last_name').addClass("form-input").prop('readonly', true).val($('#checkout-last_name').val());
            $('#billing_email').addClass("form-input").prop('readonly', true).val($('input[name="email"]').val());
        } else {
            $('#billing_first_name').removeClass("form-input").prop('readonly', false).val('');
            $('#billing_last_name').removeClass("form-input").prop('readonly', false).val('');
            $('#billing_email').removeClass("form-input").prop('readonly', false).val('');
        }
    }
    if($('.invoice_other_person').length > 0 && $("input[id^=checkout-email]").first().val() === $("#billing_email").val()) {
        $('.invoice_other_person').prop("checked", true);
        update_billing_details(true);
    }
    
    $('.invoice_other_person').on('change', function () {
        update_billing_details(this.checked);
    });
    
    // After the user clicks "Continue", show the "your booking" portion of the checkout
    $('#checkout-continue').on('click', function() {
        $('body').addClass('checkout-your_booking');
        window.scrollTo(0, 0);
    });

    // If the user clicks the back arrow, return to the checkout (contact and payment details)
    $('.mobile-breadcrumbs-prev').on('click', function(ev) {
        var $body = $('body');
        if ($body.hasClass('checkout-your_booking')) {
            ev.preventDefault();
            $body.removeClass('checkout-your_booking');
        }
    });

    $('#checkout-course_code').on('change', function() {
        var selected_course = $(this).val() ? $(this).find(':selected').text() : '';

        $('#checkout-sidebar-selected_course').text(selected_course);

        $('#interview_application_container').toggleClass('hidden', !$(this).val());
        check_cart();
    });

</script>
