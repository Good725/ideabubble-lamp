<?php
$item_fee       = 0;
$item_qty       = 0;
$course_title   = '';
$date_formatted = '';
if (!isset($has_amendable)) {
    $has_amendable = false;
}
$event_ids      = '';
$schedule_id    = '';
$payg           = false;
if ( ! empty($schedule_events))
{
    foreach ($schedule_events as $event_id => $item)
    {
        if ($item['schedule']['fee_per'] == 'Schedule') {
            $item_fee = $item['fee'];
        } else if ($item['schedule']['fee_per'] == 'Day') {
            $item_fee = Model_Schedules::calculate_fee_for_schedule($item['schedule_id'], array_keys($schedule_events));
        } else {
            $item_fee += $item['fee'];
        }
        $item_qty      += 1;

        // These should be the same for all items. We just want to set them if there is at least one.
        $course_title   = $item['event']['course'];
        $date_formatted = $item['event']['date_formatted'];
        $schedule_id    = $item['event']['schedule_id'];

        if (@$item['schedule']['amendable'] == 1 && $item['schedule']['payment_type'] == 1) { // prepay
            $has_amendable = true;
        }

        // 2 = 'Pay as you go' (This association only exists by hardcode. That should be changed.)
        if ($item['schedule']['booking_type'] == 'Subscription') {
            $payg = true;
            $fee_text = $item['schedule']['fee_amount'].' <small>per&nbsp;' . $item['schedule']['fee_per'] . '</small>';
        } else {
            if ($item['schedule']['payment_type'] == 2) {
                $payg = true;
                $fee_text = $item['schedule']['fee_amount'] . ' <small>per&nbsp;class</small>';
            }
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
        <div class="col-xs-7">
            <h5 class="checkout-item-title"><?= $course_title ?></h5>
            <span class="checkout-item-date"><?= $date_formatted ?></span>
            <?php /*
            <span class="checkout-item-count"><?= $item_qty ?> <?= ($item_qty == 1) ? 'session' : 'sessions' ?></span>
            */ ?>
            <span>Ticket price</span>
        </div>

        <div class="col-xs-4">
            <span class="checkout-item-fee-wrapper">&euro;<span class="checkout-item-fee"><?= isset($fee_text) ? $fee_text : number_format($item_fee, 2) ?></span></span>
        </div>

        <div class="col-xs-1">
            <button type="button" class="button--plain checkout-item-remove" title="<?= __('Remove from cart') ?>">
                <span class="sr-only"><?= __('Remove from cart') ?></span>
                <span class="icon_close" aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <?php if ( ! empty($schedule_events)): ?>
        <?php foreach ($schedule_events as $event_id => $item): ?>
            <input class="checkout-item-input" type="hidden" name="booking_items[<?= $item['schedule_id'] ?>][<?= $event_id ?>][attending]" value="1" />
        <?php endforeach; ?>
    <?php else: ?>
        <input class="checkout-item-input" type="hidden" name="booking_items[][][attending]" value="1" disabled="disabled" />
    <?php endif; ?>
</li>
