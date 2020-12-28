<?php
$form_data = Kohana::sanitize($_GET);
$realex_enabled = (bool) Settings::instance()->get('enable_realex');
$mobile_payment_enabled = (bool) Settings::instance()->get('enable_mobile_payments');
$stripe_enabled         = (Settings::instance()->get('stripe_enabled') == 'TRUE');
$payments_enabled = ($realex_enabled || $mobile_payment_enabled || $stripe_enabled);
$booking_items = array();
$event = null;
if (isset($form_data['eid'])) {
    $event            = Model_Schedules::get_event_details($form_data['eid']);
    $schedule         = Model_Schedules::get_one_for_details($event['schedule_id']);
    $booking_events[] = $event;
}
$franchisee_account = null;
if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) {
    if (is_numeric($schedule['owned_by'])) {
        $franchisee_account = Model_Event::accountDetailsLoad($schedule['owned_by']);
    }
}
$sub_total   = 0;
$discount    = 0;
$zone_fee    = 0;
$booking_fee = 0;

$breadcrumb_title = '
    <span id="breadcrumb-title-checkout">'.__('Checkout').'</span>
    <span class="hidden" id="breadcrumb-title-your_booking">'.__('Your booking').'</span>';
include 'template_views/header.php';
?>

<script>
    jQuery(document).ready(function () {
        var checkout_posted = false;
        function book_and_pay_with_cart()
        {
            var $form = $('#booking-checkout-form');
            var data = $form.serialize();
            $.post(
                '/frontend/courses/ajax_book_and_pay_with_cart/',
                data,
                function(data)
                {
                    if (data.status == 'success') {
                        window.location = data.redirect;
                    } else {
                        console.log(data);
                        var $clone = $('#checkout-error_message-template').clone();
                        $clone.removeClass('hidden').find('.checkout-error_message-text').html(data.message);
                        $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                    }
                }, 'json'
            ).fail(
                function()
                {
                    console.log('Backend payment error');
                    var $clone = $('#checkout-error_message-template').clone();
                    $clone.removeClass('hidden').find('.checkout-error_message-text').html('Error processing payment. If this error continues, please contact the administration.');
                    $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                }
            );
        }

        $('.checkout-complete_booking').on('click', function()
        {
            if (checkout_posted) {
                return false;
            }
            var $form = $('#booking-checkout-form');

            if ($form.validationEngine('validate')) {
                var promise;
                <?php if ($stripe_enabled): ?>
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
                                        book_and_pay_with_cart();
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
                <?php else: ?>
                promise = $.when();
                promise.done(book_and_pay_with_cart);
                <?php endif ?>
            }
        });
    });
</script>
<div class="checkout-wrapper">
    <div class="row" id="checkout-error_messages">
        <div class="alert alert-warning popup_box checkout-error_message hidden" id="checkout-error_message-template">
            <button type="button" class="close-btn button--plain">&times;</button>
            <div class="checkout-error_message-text"></div>
        </div>
    </div>

    <div class="row">
        <?php
        $current_step = 'checkout';
        $checkout_progress_event = !empty($booking_events) ? $booking_events[0]: null;
        include 'views/checkout_progress.php';
        ?>

        <form class="clearfix" id="booking-checkout-form">
            <?php
            $sub_total = $event['fee_amount'];
            $discount = 0;
            $discounts = Model_CourseBookings::get_available_discounts(null, array(array('id' => $event['schedule_id'], 'fee' => $event['fee_amount'], 'discount' => 0, 'prepay' => 1)));
            if (isset($discounts[0])) {
                if ($discounts[0]['discount'] > 0) {
                    $discount = $discounts[0]['discount'];
                }
            }
            $total = $sub_total - $discount;
            ?>
            <input type="hidden" name="subject"       value="New booking" />
            <input type="hidden" name="business_name" value="" />
            <input type="hidden" name="redirect"      value="payment.html" />
            <input type="hidden" name="event"         value="post_contactForm" />
            <input type="hidden" name="trigger"       value="booking2" />
            <input type="hidden" name="event_id"      value="<?= isset($event['id'])          ? $event['id']          : '' ?>" id="checkout-event_id" />
            <input type="hidden" name="price"         value="<?= isset($event['fee_amount'])  ? $event['fee_amount']  : '' ?>" />
            <input type="hidden" name="schedule_id"   value="<?= isset($event['schedule_id']) ? $event['schedule_id'] : '' ?>" id="checkout-schedule_id" />
            <input type="hidden" name="schedule"      value="<?= isset($event['schedule'])    ? $event['schedule']    : '' ?>" />
            <input type="hidden" name="training"      value="<?= isset($event['schedule'])    ? $event['schedule']    : '' ?>" />

            <input type="hidden" name="title"         value="Course Booking" />
            <input type="hidden" name="subtotal"      value="<?= isset($sub_total)            ? $sub_total  : '' ?>" />
            <input type="hidden" name="discount"      value="<?= isset($discount)             ? $discount  : '' ?>" />
            <input type="hidden" name="amount"        value="<?= isset($total)                ? $total  : '' ?>" />
            <input type="hidden" name="ids"           value="<?= isset($event['id'])          ? $event['id']          : '' ?>" />
            <input type="hidden" name="custom"        value="" />

            <input type="hidden" name="return_url"    value="<?= Model_Payments::get_thank_you_page(false) ?>" />
            <input type="hidden" name="cancel_url"    value="<?= URL::site() ?>" />

            <input type="hidden" name="payment_method" value="cc" />

            <?php // If course bookings are enabled, a separate cart appears. See header_cart.php ?>
            <div class="right-section<?= !empty($course_bookings_enabled) ? ' hidden--mobile' : '' ?>" id="right-section">
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
                    <div class="item-summary-head">
                        <h4>Your Booking</h4>
                    </div>

                    <div class="prepay-box" id="checkout-sidebar-items">
                        <input type="hidden" id="checkout-cart-deposit_text" value="<?= htmlspecialchars(Settings::instance()->get('checkout_deposit_text')) ?>" />

                        <div class="checkout-items">
                            <?php if (!empty($booking_events)): ?>
                                <?php foreach ($booking_events as $booking_event): ?>
                                    <div class="checkout-item">
                                        <h4 class="clearfix"><?= $booking_event['course'] ?> <span class="right">&euro;<?= number_format($booking_event['fee_amount'], 2) ?></span></h4>
                                        <div><?= $booking_event['location'] ?></div>
                                        <div><?= date('l jS F', strtotime($booking_event['datetime_start'])) ?>, 1 session</div>
                                        <div><?= date('H:i a', strtotime($booking_event['datetime_start'])) ?><?= ($booking_event['datetime_start'] != $booking_event['datetime_end']) ? ' &ndash; '.date('H:i a', strtotime($booking_event['datetime_end'])) : '' ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="button" class="button button--book<?= empty($count_seat_options) ? ' hidden' : '' ?>" data-toggle="slidein" data-target="#checkout-zone-selector">Select zones for your booking</button>

                    <?php if (Settings::instance()->get('course_checkout_coupons')): ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="checkout-coupon_code">Coupon code</label>
                                <?= Form::ib_input(null, 'coupon_code', null, ['id' => 'checkout-coupon_code']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="total-pay<?= (!$has_amendable) ? ' hidden' : ''?>">
                        <?php
                        $label = __('Amendable tier $1% extra. $2 Amend your booking up to 48 hours before the beginning of the course', array(
                            '$1' => $course_amend_fee_percent,
                            '$2' => '<br />'
                        ));
                        $attributes = array('id' => 'checkout-amendable_tier');
                        if (!$has_amendable) {
                            $attributes['disabled'] = 'disabled';
                        }
                        echo Form::ib_checkbox($label, 'amendable', '1', false, $attributes);
                        ?>
                    </div>

                    <div class="total-pay">
                        <?php $total = $sub_total - $discount + $zone_fee + $booking_fee; ?>

                        <ul class="checkout-breakdown" id="checkout-breakdown">
                            <li<?= $discount ? '' : ' class="hidden"' ?>>
                                <?= __('Discount') ?>

                                <span class="right">
                                    &minus;&euro;<span id="checkout-breakdown-discount" data-amount="-<?= $discount ?>"><?= number_format($discount, 2) ?></span>
                                </span>
                            </li>

                            <li<?= $zone_fee ? '' : ' class="hidden"' ?>>
                                <?= __('Zone fee') ?>

                                <span class="right">
                                    &euro;<span id="checkout-breakdown-zone_fee" data-amount="<?= $zone_fee ?>"><?= number_format($zone_fee, 2) ?></span>
                                </span>
                            </li>

                            <?php if ( ! empty($payg_bookings)): ?>
                                <li>
                                    <?= __('PAYG fee') ?>

                                    <span class="right">
                                    &euro;<span class="checkout-breakdown-booking_fee" data-amount="<?= $payg_fee ?>"><?= number_format($payg_fee, 2) ?></span>
                                    </span>
                                </li>
                            <?php endif; ?>

                            <?php $cc_fee = (float)Settings::instance()->get('course_cc_booking_fee'); ?>

                            <?php if ($cc_fee): ?>
                                <li class="booking_fee cc <?=$payment_method != 'cc' ? 'hidden' : ''?>">
                                    <?= __('Card fee') ?>

                                    <span class="right">
                                    &euro;<span class="checkout-breakdown-booking_fee" data-amount="<?= $cc_fee ?>"><?= number_format($cc_fee, 2) ?></span>
                                    </span>
                                </li>
                            <?php endif; ?>

                            <?php $sms_fee = (float)Settings::instance()->get('course_sms_booking_fee'); ?>
                            <li  class="booking_fee sms <?=$payment_method != 'sms' ? 'hidden' : ''?>">
                                <?= __('SMS fee') ?>

                                <span class="right">
                                    &euro;<span class="checkout-breakdown-booking_fee" data-amount="<?= $sms_fee ?>"><?= number_format($sms_fee, 2) ?></span>
                                </span>
                            </li>

                            <li class="amend-fee" style="display: none" data-amend-fee="<?= $amend_fee ?>" data-amount="<?=$amend_fee?>">
                                <?= __('Amend Fee') ?>

                                <span class="right">
                                    &euro;<span id="checkout-breakdown-amend_fee"><?= number_format($amend_fee, 2) ?></span>
                                </span>
                            </li>


                            <li<?= ($sub_total == $total) ? ' class="hidden"' : '' ?>>
                                <?= __('Sub total') ?>

                                <span class="right">
                                    &euro;<span id="checkout-breakdown-subtotal" data-amount="<?= $sub_total ?>"><?= number_format($sub_total, 2) ?></span>
                                </span>
                            </li>

                            <li  class="booking_fee interest hidden">
                                <?= __('Interest') ?>

                                <span class="right">
                                    &euro;<span class="checkout-breakdown-booking_fee" data-amount=""></span>
                                </span>
                            </li>

                            <li class="sub-total">
                                <?= __('Total') ?>

                                <span class="right">
                                    &euro;<span id="checkout-breakdown-total" data-total="<?= number_format($total, 2) ?>" data-amend-total="<?= number_format($total + $amend_fee, 2) ?>"><?= number_format($total, 2) ?></span>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="purchase-packages hidden"></div>

                    <div class="hidden--mobile hidden--tablet">
                        <?php include 'template_views/checkout_complete_booking.php'; ?>
                    </div>
                </div>
            </div>

            <div class="left-section contact--left">
                <?php
                include 'template_views/checkout_contact_details.php';

                $realex_enabled = (Settings::instance()->get('enable_realex') AND Settings::instance()->get('realex_username') != '');
                $stripe_enabled = (Settings::instance()->get('stripe_enabled') == 'TRUE');
                $counties = Model_Cities::get_counties();
                if (!empty($payments_enabled) && $total > 0) include 'template_views/checkout_pay_with.php';
                ?>
            </div><?php // contact left side end ?>


            <?php // If course bookings are enabled, a separate complete booking appears on mobile. See header_cart.php ?>
            <div class="hidden--desktop<?= !empty($course_bookings_enabled) ? ' hidden--mobile' : '' ?>">
                <?php include 'template_views/checkout_complete_booking.php'; ?>
            </div>

        </form>

    </div><?php // row end- ?>
</div>

<!-- popup hover jquery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<!-- tab jquery -->
<script src="<?= URL::get_engine_assets_base() ?>js/jquery-ui.js"></script>


<?php include 'views/footer.php'; ?>

