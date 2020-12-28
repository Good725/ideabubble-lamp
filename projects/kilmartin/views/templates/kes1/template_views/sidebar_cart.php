<div class="item-summary-head">
    <h4>Your Booking</h4>
</div>

<?php
$custom_checkout        = Settings::instance()->get('checkout_customization');

$has_course_bookings    = (!empty($prepay_bookings) || !empty($payg_bookings));
$has_event_bookings     = (isset($event_object) && $event_object->id);
$is_application_payment = !empty($application_payment);

$has_bookings           = ($has_course_bookings || $has_event_bookings || $is_application_payment);
$is_interview           = ($custom_checkout == 'bcfe');
$course_code            = isset($_GET['coursecode']) ? urldecode($_GET['coursecode']) : '';
$selected_course        = Model_Courses::get_course_title_from_code($course_code);
?>

<div class="booking-cart-empty<?= $has_bookings || ($custom_checkout == 'bcfe' && !empty($selected_course) || $custom_checkout == 'sls') ? ' hidden' : '' ?>" id="booking-cart-empty">
    <div class="booking-cart-icon">
        <span class="fa fa-shopping-cart" aria-hidden="true"></span>
    </div>
    <p>You currently have no<br/>bookings selected</p>
</div>

<div class="prepay-box" id="checkout-sidebar-items">
    <input type="hidden" id="checkout-cart-deposit_text" value="<?= htmlspecialchars(Settings::instance()->get('checkout_deposit_text')) ?>" />

    <?php
    $sub_total   = 0;
    $discount    = 0;
    $zone_fee    = 0;
    $booking_fee = 0;
    $has_amendable = false;
    $amend_fee = 0;
    $payment_method = 'cc';

    if (!empty($cart)) {
        foreach ($cart as $cart_item) {
            if ($cart_item['prepay'] || $cart_item['type'] == 'subtotal') {
                $discount += $cart_item['discount'];
            }
            if (!empty($course_amend_fee_percent) && isset($cart_item['details']['amendable']) && $cart_item['details']['amendable'] && $cart_item['details']['payment_type'] == 1) {
                $amend_fee += round(($course_amend_fee_percent / 100) * $cart_item['total'], 2);
            }
        }
    }
    ?>

    <?php if ($custom_checkout == 'bcfe' || $custom_checkout == 'sls'): ?>
        <div<?= $selected_course ? '' : ' class="hidden"' ?> id="interview_application_container">
            <h3><?= __('Course') ?></h3>

            <ul>
                <li id="checkout-sidebar-selected_course"><?= $selected_course ? htmlentities($course_code.' - '.$selected_course) : '' ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <div class="prepay_container<?= (empty($prepay_bookings) && @$application_payment == false) ? ' hidden' : '' ?>" id="prepay_container">
        <?php if(Settings::instance()->get('cart_prepay_heading_enabled') == 1):?><h5><?= __('Prepay') ?></h5><?php endif?>

        <ul>
            <?php
            if ( ! empty($prepay_bookings)) {
                $added_schedules = array();
                $check_event_count = array();
            foreach ($prepay_bookings as $schedule_and_day => $schedule_events) {
                foreach ($schedule_events as $event_id => $booking_item) {
                    $check_event_count[$booking_item['schedule_id']][$event_id] = $event_id;
                }
            }
                foreach ($prepay_bookings as $schedule_and_day => $schedule_events) {
                    $schedule_events_count = count($check_event_count[$booking_item['schedule_id']]);
                    foreach ($schedule_events as $event_id => $booking_item)
                    {
                        $already_added = in_array($booking_item['schedule_id'], $added_schedules);

                        if ( ! $already_added) {
                            $added_schedules[] = $booking_item['schedule_id'];
                        }

                        // If pay-per-timeslot, do this once per timeslot. Otherwise, do this once in total.
                        if ( ! $already_added || $booking_item['schedule']['fee_per'] == 'Timeslot')
                        {
                            if ($schedule_events_count == 1 && @$booking_item['schedule']['trial_timeslot_free_booking'] == 1) {
                                $sub_total += 0;
                            } else if (@$booking_item['schedule']['charge_per_delegate']) {
                                $number_of_delegates = !empty($cart_session_info['number_of_delegates']) ? $cart_session_info['number_of_delegates'] : 1;
                                $sub_total += (float)$booking_item['fee'] * $number_of_delegates;
                            } else {
                                $sub_total += (float)$booking_item['fee'];
                            }
                            $schedule_events[$event_id] = $booking_item;
                        }
                    }
                    include 'snippets/checkout_item.php';
                }
            }

            if (@$application && $application_payment) {
                foreach ($application['courses'] as $course) {
                    $sub_total += $course['fulltime_price'];
            ?>
                    <li class="checkout-item">
                        <h6 class="checkout-item-title"><?= $course['course'] ?></h6>

                        <span class="right">&euro;<span class="checkout-item-fee"><?= number_format($course['fulltime_price'], 2) ?></span></span>

                    </li>

                    <?php
                }
            }
            ?>
        </ul>
        <?php
        if (!empty($discounts)) {
            foreach ($discounts as $schedule_id => $sdiscounts) {
                foreach ($sdiscounts as $i => $discount_detail) {
        ?>
        <input type="hidden" name="discounts[<?=$schedule_id?>][<?=$i?>][id]" value="<?=$discount_detail['id']?>" />
        <input type="hidden" name="discounts[<?=$schedule_id?>][<?=$i?>][amount]" value="<?=$discount_detail['amount']?>" />
        <input type="hidden" name="discounts[<?=$schedule_id?>][<?=$i?>][code]" value="<?=$discount_detail['code']?>" />
        <input type="hidden" name="discounts[<?=$schedule_id?>][<?=$i?>][ignore]" value="<?=$discount_detail['ignore']?>" />
        <?php
                }
            }
        }
        ?>
    </div>

    <div class="pay_as_you_go_container<?= (empty($payg_bookings)) ? ' hidden' : '' ?>" id="pay_as_you_go_container">
        <h5><?= __('Pay as you go') ?></h5>

        <p><?= __(Settings::instance()->get('course_payg_booking_alert')) ?></p>

        <ul>
            <?php
            if ( ! empty($payg_bookings)) {
                $added_schedules = array();
                foreach ($payg_bookings as $schedule_and_day => $schedule_events) {
                    foreach ($schedule_events as $event_id => $booking_item) {
                        $already_added = in_array($booking_item['schedule_id'], $added_schedules);

                        if (!$already_added) {
                            $added_schedules[] = $booking_item['schedule_id'];
                            if ($contact) {
                                $trial_booked = Model_KES_Bookings::check_existing_booking($contact->get_id(), $booking_item['schedule_id']);
                            } else {
                                $trial_booked = null;
                            }
                            if (@$booking_item['schedule']['deposit'] > 0 && ($booking_item['schedule']['trial_timeslot_free_booking'] != 1 || $trial_booked == null)) {
                                $sub_total += (float)$booking_item['schedule']['deposit'];
                            }
                        }
                    }
                }
                foreach ($payg_bookings as $schedule_and_day => $schedule_events) {

                    //include 'snippets/checkout_item.php';
                }
                //ob_clean();header('content-type: text/plain');print_r($payg_bookings);exit;
                $booking_fee += (float)Settings::instance()->get('course_payg_booking_fee');
            }
            ?>
        </ul>
    </div>

    <?php if (isset($event_object) && isset($order)): ?>
        <?php
        $total = $order['total'];
        $sub_total = $order['subtotal'];
        ?>
        <ul>
            <?php foreach ($order['items'] as $item_index => $item): ?>
                <?php foreach ($item['dates'] as $date_index => $date_id): ?>
                    <?php
                    $starts = null;
                    foreach ($event['dates'] as $date_details) {
                        if ($date_details['id'] == $date_id) {
                            $starts = $date_details['starts'];
                        }
                    }
                    ?>

                    <?php foreach ($event['ticket_types'] as $ticketType): ?>
                        <?php if ($ticketType['id'] == $item['ticket_type_id']): ?>
                            <li class="checkout-item">
                                <div class="row gutters">
                                    <div class="col-xs-11">
                                        <a href="<?= $event_object->get_url() ?>">
                                            <h5 class="checkout-item-title"><?= $event_object->name ?></h5>
                                            <h6><?= $ticketType['name']  ?></h6>
                                        </a>
                                    </div>

                                    <?php // Temporarily hidden, until this is wired up. ?>
                                    <div class="col-xs-1 hidden">
                                        <button type="button" class="button--plain checkout-item-remove btn-close" title="<?= __('Remove from cart') ?>">
                                            <span class="sr-only"><?= __('Remove from cart') ?></span>
                                            <span class="icon_close" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="row gutters">
                                    <div class="col-xs-7 col-sm">
                                        <div class="checkout-item-date"><?= ($event['one_ticket_for_all_dates'] == 0 ? date(' F j, g:i a', strtotime($starts)) : '') ?></div>
                                        <span class="checkout-item-count">
                                            <?php
                                            if ($ticketType['is_group_booking']) {
                                                // echo __('$1 of $2 person group ticket', array('$1' => 1, '$2' => $ticketType['sleep_capacity']));
                                                echo __('Ticket Price');
                                            } elseif ($item['quantity'] == 1) {
                                                echo __('1 ticket');
                                            } else {
                                                echo __('$1 tickets', array('$1' => $item['quantity']));
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <div class="col-xs-5">
                                        <span class="checkout-item-fee-wrapper">
                                             <?php if ($ticketType['type'] == 'Donation'): ?>
                                                 <label class="input-with-icon">
                                                     <span class="input-icon">&euro;</span>
                                                     <input type="text" class="item_donation" name="item[<?=$item_index?>][donation]" value="<?=$item['donation'] ? $item['donation'] : '0.00'?>" data-old_value="<?=$item['donation'] ? $item['donation'] : '0.00'?>" />
                                                 </label>
                                             <?php else: ?>
                                                 <?php
                                                 $amount              = ($item['total'] + $item['discount']) * $item['quantity'];
                                                 $amount_formatted    = '&euro;'.number_format($amount, ($amount == floor($amount) ? 0 : 2));

                                                 $number_of_people    = $ticketType['is_group_booking'] ? $ticketType['sleep_capacity'] : 1;
                                                 $per_person_amount   = round($price_breakdown['total'] / $number_of_people, 2);
                                                 $pp_amount_formatted = '&euro;'.number_format($per_person_amount, ($per_person_amount == floor($per_person_amount) ? 0 : 2));
                                                 ?>

                                                 <?php if ($number_of_people > 1): ?>
                                                     <strong><?= __('$1 pp',    array('$1' => $pp_amount_formatted)) ?></strong><br />
                                                     <small><?=  __('$1 total', array('$1' => $price_breakdown['total']))    ?></small>
                                                 <?php else: ?>
                                                     <?= $amount_formatted ?>
                                                 <?php endif; ?>
                                             <?php endif; ?>
                                        </span>
                                    </div>

                                    <div class="col-xs-1 hidden checkout-item-timeslots-expand" data-toggle="collapse" aria-expanded="true">
                                        <button type="button" class="button--plain">
                                            <span class="fa fa-angle-down"></span>
                                        </button>
                                    </div>

                                </div>
                                <?php $line_total = $item['quantity'] * ($item['total'] + $item['discount']); ?>
                                <input type="hidden" class="item_total"  name="item[<?=$item_index?>][total]"            value="<?= $currency.number_format($line_total, 2) ?>" data-single-base="<?= $ticketType['price'] ?>" data-single-total="<?=($item['total'] + $item['discount'])?>" data-currency="<?=$currency?>" />
                                <input type="hidden" class="ticket_type" name="item[<?= $item_index ?>][ticket_type_id]" value="<?= $item['ticket_type_id'] ?>" />
                                <input type="hidden" class="qty"         name="item[<?= $item_index ?>][quantity]"       value="<?= $item['quantity'] ?>" data-min="<?=$ticketType['min_per_order']?>" data-max="<?=$ticketType['max_per_order']?>" />
                                <?php if ($event['one_ticket_for_all_dates'] == 0): ?>
                                    <input type="hidden" class="dt" name="item[<?= $item_index ?>][dates][]" value="<?= $date_id ?>" />
                                <?php else: ?>
                                    <?php foreach ($event['dates'] as $dtDetails): ?>
                                        <input type="hidden" class="dt" name="item[<?= $item_index ?>][dates][]" value="<?= $dtDetails['id'] ?>" />
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </li>
                       <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

        </ul>
    <?php endif; ?>

    <div class="hidden" id="checkout-item-template">
        <ul>
            <?php
            $booking_item = null;
            $schedule_events = null;
            include 'snippets/checkout_item.php';
            ?>
        </ul>
    </div>
</div>

<ul style="display: none;">
<li class='discountItemPlaceholder template' style="display: none" data-schedule-id="" data-discount-id="" data-discount="">
    <p>
        <img src="/assets/kes1/img/dis-img.png" onerror="this.remove()" alt="">
        <span class="left title"></span>
        <span class="right nowrap">-€<strong class="amount"></strong></span>
    </p>
</li>
</ul>
<?php

if ($payment_method == 'cc' && $sub_total > 0) {
    $booking_fee += (float)Settings::instance()->get('course_cc_booking_fee');
}
if ($payment_method == 'sms' && $sub_total > 0) {
    $booking_fee += (float)Settings::instance()->get('course_sms_booking_fee');
}

?>