<?php include_once 'cart_common.php'; ?>
<div class="cart-menu">
    <div class="prepay_container">
        <h4>Pre-pay</h4>

        <ul>
            <?php
            $sub_total = 0;
            if ( ! empty($prepay_bookings)) {
                $added_schedules = array();
                foreach ($prepay_bookings as $schedule_and_day => $schedule_events) {
                    foreach ($schedule_events as $event_id => $booking_item) {
                        $already_added = in_array($booking_item['schedule_id'], $added_schedules);

                        if ( ! $already_added) {
                            $added_schedules[] = $booking_item['schedule_id'];
                        }

                        // If pay-per-timeslot, do this once per timeslot. Otherwise, do this once in total.
                        if ( ! $already_added || $booking_item['schedule']['fee_per'] == 'Timeslot') {
                            $sub_total += $booking_item['fee'];
                            $schedule_events[$event_id] = $booking_item;
                        }
                    }

                    include 'snippets/checkout_item.php';
                }
            }
            ?>
        </ul>
    </div>

    <div class="pay_as_you_go_container pay_as_you_go_container-mobile">
        <h4>PAYG</h4>

        <ul>
            <?php
            if ( ! empty($payg_bookings)) {
                foreach ($payg_bookings as $schedule_and_day => $schedule_events) {
                    include 'snippets/checkout_item.php';
                }
                $booking_fee = isset($booking_fee) ? $booking_fee : 0;
                $booking_fee += (float)Settings::instance()->get('course_payg_booking_fee');
            }
            ?>
        </ul>
    </div>

    <div class="discounts_container" id="cart-discounts_container">

    </div>

    <div class="header-cart-breakdown">
        <div class="form-group row gutters clearfix">
            <div class="col-xs-8"><?= __('Subtotal') ?></div>
            <div class="col-xs-4 text-right">
                &euro;<span class="cart-subtotal"></span>
            </div>
        </div>

        <div class="form-group row gutters clearfix cart-discount-total-wrapper">
            <div class="col-xs-8"><?= __('Discount') ?></div>
            <div class="col-xs-4 text-right">
                &euro;<span class="cart-discount"></span>
            </div>
        </div>

        <?php
        $payg_fee = (float)Settings::instance()->get('course_payg_booking_fee');
        ?>
        <div class="form-group row gutters clearfix cart-payg_fee <?= $payg_fee > 0 && !empty($payg_bookings) ? '' : 'hidden'?>">
            <div class="col-xs-8"><?= __('PAYG fee') ?></div>
            <div class="col-xs-4 text-right">
                <span class="cart-payg_fee" data-amount="<?= $payg_fee ?>">&euro;<?= number_format($payg_fee, 2) ?></span>
            </div>
        </div>

        <?php $cc_fee = (float)Settings::instance()->get('course_cc_booking_fee'); ?>
        <div class="form-group row gutters clearfix cart-cc_fee <?=$payment_method != 'cc' ? 'hidden' : ''?>">
            <div class="col-xs-8"><?= __('Card fee') ?></div>
            <div class="col-xs-4 text-right">
                <span class="cart-cc_fee" data-amount="<?= $cc_fee ?>">&euro;<?= number_format($cc_fee, 2) ?></span>
            </div>
        </div>

        <?php $sms_fee = (float)Settings::instance()->get('course_sms_booking_fee'); ?>
        <div class="form-group row gutters clearfix cart-sms_fee <?=$payment_method != 'sms' ? 'hidden' : ''?>">
            <div class="col-xs-8"><?= __('SMS fee') ?></div>
            <div class="col-xs-4 text-right">
                <span class="cart-sms_fee" data-amount="<?= $sms_fee ?>">&euro;<?= number_format($sms_fee, 2) ?></span>
            </div>
        </div>

        <div class="form-group row gutters clearfix header-cart-breakdown-interest hidden">
            <div class="col-xs-8"><?= __('Interest') ?></div>
            <div class="col-xs-4 text-right">
                <span class="interest">&euro;</span>
            </div>
        </div>



        <div class="form-group row gutters clearfix header-cart-breakdown-total">
            <div class="col-xs-8"><strong><?= __('TOTAL') ?></strong></div>
            <div class="col-xs-4 text-right">
                &euro;<strong class="cart-total"></strong>
            </div>
        </div>

        <div class="form-group row gutters clearfix header-cart-breakdown-deposit hidden">
            <div class="col-xs-8"><strong><?= __('DEPOSIT DUE TOTAL') ?></strong></div>
            <div class="col-xs-4 text-right">
                <strong class="deposit">&euro;</strong>
            </div>
        </div>
    </div>
</div>