<p>Your order has been successfully processed.</p>

<p>Booking reference: <?= isset($booking['id']) ? $booking['id'] : (isset($booking['booking_id']) ? $booking['booking_id'] : '') ?></p>

<h2>Your Details</h2>

<?php
$phone         = trim(( ! empty($booking['phone']))         ? $booking['phone']         : (isset($data->phone)         ? $data->phone         : ''));
$mobile        = trim(( ! empty($booking['mobile']))        ? $booking['mobile']        : (isset($data->mobile)        ? $data->mobile        : ''));
$mobile_code   = trim(( ! empty($booking['mobile_code']))   ? $booking['mobile_code']   : (isset($data->mobile_code)   ? $data->mobile_code   : ''));
$mobile_number = trim(( ! empty($booking['mobile_number'])) ? $booking['mobile_number'] : (isset($data->mobile_number) ? $data->mobile_number : ''));

$mobile = $mobile ? $mobile : trim($mobile_code.' '.$mobile_number);

if (isset($booking['payer'])) {
    $phone  = trim($booking['payer']['phone'])  ? $booking['payer']['phone']  : $phone;
    $mobile = trim($booking['payer']['mobile']) ? $booking['payer']['mobile'] : $mobile;
    $email  = $booking['payer']['email'];
    if (!isset($booking['email'])) {
        $booking['email'] = $booking['payer']['email'];
    }
    if (!isset($booking['address'])) {
        $booking['address'] = $booking['payer']['address1'] . ' ' . $booking['payer']['address2'] . ' ' . DB::select('*')->from('plugin_courses_counties')->where('id', '=', $booking['payer']['address3'])->execute()->get('name') . ' ' . $booking['payer']['address4'];
    }
}
?>

<p>
    Name: <?= (isset($booking['first_name']) AND $booking['first_name'] != '') ? $booking['first_name'] : (isset($data->first_name) ? $data->first_name : '') ?>
	<?= (isset($booking['last_name'])  AND $booking['last_name'] != '') ? $booking['last_name'] : (isset($data->last_name) ? $data->last_name : '') ?> <br />

    <?php if ($phone): ?>
        Phone: <?= $phone ?><br/>
    <?php endif; ?>

    <?php if ($mobile): ?>
        Mobile: <?= $mobile ?><br/>
    <?php endif; ?>

    Email: <?= (isset($booking['email']) AND $booking['email'] != '') ? $booking['email'] : (isset($data->email) ? $data->email : '') ?><br />

	<?php if (isset($booking['address']) AND $booking['address'] != ''): ?>
		Address: <?= nl2br($booking['address']) ?>
	<?php elseif (isset($data->address1) AND $data->address1 != ''): ?>
		Address: <?=
		$data->address1
		?><?=
		isset($data->address1) ? ', '.$data->address1 : ''
		?><?=
		isset($data->address2) ? ', '.$data->address2 : ''
		?><?=
		isset($data->town) ? ', '.$data->town : ''
		?><?=
		isset($data->county) ? ', '.$data->county : ''
		?><?=
		isset($data->country) ? ', '.$data->country : ''
		?>
	<?php endif; ?>
</p>

<?php if (isset($booking_items)): ?>
    <h2>Booking Information</h2>
<?php else: ?>
    <h2>Order details</h2>
<?php endif; ?>

<?php if ( ! empty($student)): ?>
    Student: <?= $student->get_first_name().' '.$student->get_last_name() ?>
<?php endif; ?>

<?php if (isset($booking_items)): ?>
    <table style="margin-left: -.5em;">
        <thead class="table">
            <tr>
                <th scope="col" style="padding:.5em;">Schedule</th>
                <th scope="col" style="padding:.5em;">Course</th>
                <th scope="col" style="padding:.5em;">Location</th>
                <th scope="col" style="padding:.5em;">Zone</th>
                <th scope="col" style="padding:.5em;">Start Day</th>
                <th scope="col" style="padding:.5em;">Start Time</th>
                <th scope="col" style="padding:.5em;">Price</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($booking_items as $booking_item): ?>
                <tr>
                    <td style="padding:.5em;"><?= $booking_item['schedule_name'] ?></td>
                    <td style="padding:.5em;"><?= $booking_item['course_title'] ?></td>
                    <td style="padding:.5em;"><?= (( ! empty($booking_item['parent_location'])) ? $booking_item['parent_location'].': ' : '').$booking_item['location'] ?></td>
                    <td style="padding:.5em;"><?= $booking_item['zone_name'] ?></td>
                    <td style="padding:.5em;"><?= date('j F Y', strtotime($booking_item['datetime_start'])) ?></td>
                    <td style="padding:.5em;"><?= date('H:i', strtotime($booking_item['datetime_start'])) ?></td>
                    <td style="padding:.5em;"><?= $booking_item['fee_amount'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>
        Course: <?=   isset($course['title'])   ? $course['title']      : '' ?><br />
        Schedule: <?= isset($schedule['name']) ? $schedule['name']    : '' ?><br />
        Venue: <?= isset($schedule['location']) ? $schedule['location'] : '' ?><br />
        <?php if ((is_array($data) AND isset($data['item_name'])) OR (is_object($data) AND isset($data->item_name))): ?>
            Event: <?= is_array($data) ? $data['item_name'] : $data->item_name ?><br/>
        <?php else: ?>
            Event: <?= isset($course['title']) ? $course['title'] : '' ?>, <?= isset($schedule['location']) ? $schedule['location'] : '' ?>, <?= $schedule_event != null ? date('H:i j F Y', strtotime($schedule_event->datetime_start)) . ' - ' . date('H:i j F Y', strtotime($schedule_event->datetime_end)) : (isset($schedule['start_date']) ? date('H:i j F Y', strtotime($schedule['start_date'])) : '') ?>
            <br/>
        <?php endif; ?>
    </p>

    <?= isset($booking['summary']) ? $booking['summary'] : ''?>
<?php endif; ?>

<?php if (isset($data->discount)) { ?>
Subtotal: <b>&euro;<?= number_format($data->subtotal, 2) ?></b><br />
Discount: <b>-&euro;<?= number_format($data->discount, 2) ?></b><br />
<?php } ?>
TOTAL: <b>&euro;<?= (is_array($data) AND isset($data['mc_gross'])) ? $data['mc_gross'] : (isset($data->amount) ? $data->amount : (isset($data->mc_gross) ? $data->mc_gross : '')) ?></b><br /><br />

Comments: <?= isset($booking['comments']) ? $booking['comments'] : (isset($data->comments) ? $data->comments : '') ?><br /><br />

<p>Thank you for your custom.</p>

<p>Kind regards,</p>

<p><a href="<?= URL::base() ?>"><?= URL::base() ?></a></p>

<h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <a href="<?= URL::base() ?>"><?= URL::base() ?></a></h5>
