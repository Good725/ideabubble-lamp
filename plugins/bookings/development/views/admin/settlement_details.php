<?php
/**
 * List the contacts booking under the contacts and family details in the Contacts view
 */
?>

<div class="row-fluid header list_notes_alert">
	<?= (isset($alert)) ? $alert : '' ?>
</div>
<?php if ( ! empty($settlement_data)): ?>
<fieldset>
<legend>Settlement</legend>
<table class="table">
	<tr><th>Settlement Id</th><td><?=$settlement_data['settlement'][0]['id']?></td></tr>
	<tr><th>Amount</th><td><?=$settlement_data['settlement'][0]['amount']?></td></tr>
	<tr><th>Date</th><td><?=$settlement_data['settlement'][0]['date_created']?></td></tr>
</table>
</fieldset>
<br />
<fieldset>
<legend>Payments</legend>
<table class="table dataTable settlement_payments_table">
	<thead>
		<tr>
			<th scope="col">Payment ID</th>
			<th scope="col">Amount</th>
			<th scope="col">Rental</th>
			<th scope="col">Income</th>
			<th scope="col">Date</th>
			<th scope="col">Booking Id</th>
			<th scope="col">Schedule Id</th>
			<th scope="col">Schedule</th>
			<th scope="col">Trainer Id</th>
			<th scope="col">Trainer</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($settlement_data['details'] as $payment):
		?>
		<tr>
			<td><?=$payment['payment_id'];?></td>
			<td><?=$payment['amount'];?></td>
			<td><?=$payment['rental'];?></td>
			<td><?=$payment['income'];?></td>
			<td><?=$payment['created'];?></td>
			<td><?=$payment['booking_id'];?></td>
			<td><?=$payment['schedule_id'];?></td>
			<td><?=$payment['schedule'];?></td>
			<td><?=$payment['trainer_id'];?></td>
			<td><?=$payment['trainer'];?></td>
		</tr>
		<?php
		endforeach;
        ?>
	</tbody>
</table>
</fieldset>
<br />
<fieldset>
<legend>Trainer Stats</legend>
<table class="table dataTable settlement_trainer_stats_table">
	<thead>
		<tr>
			<th scope="col">Trainer Id</th>
			<th scope="col">Trainer</th>
			<th scope="col">Amount</th>
			<th scope="col">Rental</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($settlement_data['stats']['trainers'] as $trainer_id => $stat):
		?>
		<tr>
			<td><?=$trainer_id;?></td>
			<td><?=$stat['trainer'];?></td>
			<td><?=$stat['amount'];?></td>
			<td><?=$stat['rental'];?></td>
		</tr>
		<?php
		endforeach;
        ?>
	</tbody>
</table>
</fieldset>
<br />
<fieldset>
<legend>Schedule Stats</legend>
<table class="table dataTable settlement_schedule_stats_table">
	<thead>
		<tr>
			<th scope="col">Schedule Id</th>
			<th scope="col">Schedule</th>
			<th scope="col">Amount</th>
			<th scope="col">Rental</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($settlement_data['stats']['schedules'] as $schedule_id => $stat):
		?>
		<tr>
			<td><?=$schedule_id;?></td>
			<td><?=$stat['schedule'];?></td>
			<td><?=$stat['amount'];?></td>
			<td><?=$stat['rental'];?></td>
		</tr>
		<?php
		endforeach;
        ?>
	</tbody>
</table>
</fieldset>
<?php else: ?>
<p>There are no such settlement.</p>
<?php endif; ?>
