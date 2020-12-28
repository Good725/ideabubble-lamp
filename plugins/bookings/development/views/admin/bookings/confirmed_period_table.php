<?php foreach ($periods as $item): ?>
	<tr data-schedule_id="<?= $item['schedule_id'] ?>" data-period_id="<?= $item['id'] ?>" data-datetime_start="<?=$item['datetime_start']?>" data-fee_per="<?=$item['fee_per']?>" data-fee="<?=$item['fee_amount']?>">
		<td><a href="/admin/courses/edit_course/?id=<?= $item['course_id'] ?>" target="_blank"><?= $item['title'] ?></a></td>
		<td><a href="/admin/courses/edit_schedule/?id=<?= $item['schedule_id'] ?>" target="_blank"><?= $item['name'] ?></a></td>
		<td><?= date('D', strtotime($item['datetime_start'])) ?></td>
		<td><?= date('M j', strtotime($item['datetime_start'])) ?></td>
		<td><?= date('H:i', strtotime($item['datetime_start'])).' - '.date('H:i',strtotime($item['datetime_end'])) ?></td>
		<td><i class="icon-<?= $item['attend'] == 1 ? 'ok' : 'remove' ?>" data-period_id="<?= $item['id'] ?>"></i></td>
		<td><input class="form-control confirmed_period_booking_note" type="text" value="<?= $item['note'] ?>" /></td>
	</tr>
<?php endforeach; ?>