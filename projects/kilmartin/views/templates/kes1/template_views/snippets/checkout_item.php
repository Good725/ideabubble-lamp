<?php
$item_fee       = 0;
$item_qty       = 0;
$course_title   = '';
$date_formatted = '';
$date           = false;
if (!isset($has_amendable)) {
    $has_amendable = false;
}
$event_ids      = '';
$schedule_id    = '';
$schedule_time  = '';
$payg           = false;
if ( ! empty($schedule_events))
{
    $schedule_events_count = count($schedule_events);
    $schedule_duration = '';
    $schedule_location = '';
    foreach ($schedule_events as $event_id => $item)
    {

        if (@$item['trial_timeslot_free_booking'] == 1 && $schedule_events_count == 1) {
            $item_fee = 0;
        } else if ($item['schedule']['fee_per'] == 'Schedule') {
            $item_fee = $item['fee'];
        } else if ($item['schedule']['fee_per'] == 'Day') {
            $item_fee = Model_Schedules::calculate_fee_for_schedule($item['schedule_id'], array_keys($schedule_events));
        } else {
            $item_fee += $item['fee'];
        }
        $schedule_duration = Model_Schedules::get_duration(@$item['schedule_id']);
        $item_qty      += 1;
        if(@$item['schedule']['plocation']) {
            $schedule_location = $item['schedule']['plocation'];
        } elseif(@$item['schedule']['location']) {
            $schedule_location = $item['schedule']['location'];
        }
        // These should be the same for all items. We just want to set them if there is at least one.
        $course_title   = @$item['course_title'];
        $date_formatted = @$item['date_formatted'];
        $schedule_id    = $item['schedule_id'];
        if (!empty($item['schedule']['timeslots'])) {
            $timeslot = reset($item['schedule']['timeslots']);
            $timeslot_start = date('H:i', strtotime($timeslot['datetime_start']));
            $timeslot_end = date('H:i', strtotime($timeslot['datetime_end']));
            $schedule_time = $timeslot_start . ' - ' . $timeslot_end;
        } else {
            $schedule_start = date('H:i', strtotime($item['schedule']['start_date']));
            $schedule_end = date('H:i', strtotime($item['schedule']['end_date']));
            $schedule_time = $schedule_start . ' - ' . $schedule_end;
        }
        if (@$item['schedule']['amendable'] == 1 && $item['schedule']['payment_type'] == 1) { // prepay
            $has_amendable = true;
        }

        // 2 = 'Pay as you go' (This association only exists by hardcode. That should be changed.)
        if ($item['schedule']['payment_type'] == 2) {
            $payg = true;
            $fee_text = $item['schedule']['fee_amount'].' <small>per&nbsp;class</small>';
        }
    }
    $event_ids = htmlentities(json_encode(array_keys($schedule_events)));
}
?>
<li class="checkout-item"
    data-schedule-id="<?= $schedule_id ?>"
    data-event-id="<?= $event_ids ?>"
    data-count="<?= $item_qty ?>"
    >
    <div class="row gutters">
        <div class="col-xs-11">
            <h5 class="checkout-item-title"><?= $course_title ?></h5>
        </div>

        <div class="col-xs-1">
            <button type="button" class="button--plain checkout-item-remove btn-close" title="<?= __('Remove from cart') ?>">
                <span class="sr-only"><?= __('Remove from cart') ?></span>
                <span class="icon_close" aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <div class="row gutters">
        <div class="col-xs-7">
            <span class="checkout-item-date"><?= $date_formatted ?></span>
            <span class="checkout-item-count checkout-item-info"></span>

        </div>

        <div class="col-xs-5">
            <span class="checkout-item-fee-wrapper">&euro;<span class="checkout-item-fee"><?= isset($fee_text) ? $fee_text : number_format($item_fee, 2) ?></span></span>
        </div>

        <div class="col-xs-1 checkout-item-timeslots-expand hidden" data-toggle="collapse" aria-expanded="true">
            <button type="button" class="button--plain">
                <span class="fa fa-angle-down"></span>
            </button>
        </div>
    </div>
    <?php if(Settings::instance()->get('duration_in_checkout')):?>
    <div class="row gutters">
        <div class="col-xs-12 text-left">
            <span class="checkout-item-duration checkout-item-info">
                <?php
                if (!empty($schedule_duration)) {
                    $schedule_duration == 1 ? '1 day' : $schedule_duration . ' days';
                }
                ?>
            </span>
                <span class="checkout-item-time checkout-item-info">
                    <?php if(!empty($schedule_time)):?>
                        | <?= $schedule_time ?> <?php endif;?>
                    </span>
            <br/>
            <span class="checkout-item-location checkout-item-info"><?=$schedule_location?>
        </div>
    </div>
    <?php endif?>

    <?php if ( ! empty($schedule_events)): ?>
        <?php foreach ($schedule_events as $event_id => $item): ?>
            <input class="checkout-item-input" type="hidden" name="booking_items[<?= $item['schedule_id'] ?>][<?= $event_id ?>][attending]" value="1" />
        <?php endforeach; ?>
    <?php else: ?>
        <input class="checkout-item-input" type="hidden" name="booking_items[][][attending]" value="1" disabled="disabled" />
    <?php endif; ?>
</li>
