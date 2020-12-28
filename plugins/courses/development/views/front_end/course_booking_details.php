<?php
$schedule = ( ! empty($booking['has_schedules']) AND ! empty($booking['has_schedules'][0])AND ! empty($booking['has_schedules'][0]['schedule'])) ? $booking['has_schedules'][0]['schedule'] : array();
$timeslot = ( ! empty($booking['has_schedules']) AND ! empty($booking['has_schedules'][0])AND ! empty($booking['has_schedules'][0]['has_timeslots'])) ? $booking['has_schedules'][0]['has_timeslots'][0] : array();
?>

<?php if ($schedule): ?>
	<table class="info_table" style="width: auto;">
		<tbody>
			<tr>
				<th scope="row">Course</th>
				<td><a href="/course-details.html/?id=<?= $schedule['course_id'] ?>"><?= $schedule['course'] ?></a></td>
			</tr>

			<tr>
				<th scope="row">Date</th>
				<td><?= date('F j Y', strtotime($timeslot['datetime_start'])) ?></td>
			</tr>

			<tr>
				<th scope="row">Time</th>
				<td><?= date('H:i', strtotime($timeslot['datetime_start'])) ?></td>
			</tr>

			<tr>
				<th scope="row">Venue</th>
				<td><?= $schedule['location'] ?></td>
			</tr>
		</tbody>
	</table>
<?php endif; ?>