<?php foreach($bookings as $key => $booking): ?>
	<tr data-schedule_id="<?=$booking['id'];?>" data-pre_pay="<?=($booking['prepay'] ? 'true' : 'false');?>" data-amount="<?=$booking['fee'];?>">
		<td><?=$booking['details']['category'];?></td>
		<td><a href="/admin/courses/edit_schedule/?id=<?= $booking['id'] ?>" target="_blank"><?=$booking['name'];?></a></td>
		<td><?=($booking['prepay'] ? 'Prepay' : 'PAYG');?></td>
		<td class="classes">30</td>
		<td class="attended"></td>
		<td class="starts"><?=date('d M Y', strtotime($booking['details']['start_date']));?></td>
		<td class="fee"><?=$booking['fee'];?></td>
		<td class="discount">discount</td>
		<td class="nextpayment">next payment</td>
		<td><?=strtotime($booking['details']['start_date']) < time() ? 'Now' : 'Next class'?></td>
		<td>
			<button type="button" class="btn btn-default" data-toggle="modal" data-target="#booking-discount-modal">View Discount</button>
			<button type="button" class="btn btn-default" data-toggle="modal" data-target="#booking-discount-modal">Add Discount</button>
		</td>
	</tr>
<?php endforeach; ?>