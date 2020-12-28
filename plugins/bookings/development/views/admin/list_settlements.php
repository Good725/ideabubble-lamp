<?php
/**
 * List the contacts booking under the contacts and family details in the Contacts view
 */
?>

<div class="row-fluid header list_notes_alert">
	<?= (isset($alert)) ? $alert : '' ?>
</div>
<?php if ( ! empty($settlements)): ?>
<table class="table dataTable settlements_table">
	<thead>
		<tr>
			<th scope="col">Settlement ID</th>
			<th scope="col">Date</th>
			<th scope="col">Amount</th>
			<th scope="col">View</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($settlements as $settlement):
		?>
		<tr data-settlement_id="<?= $settlement['id']  ?>">
			<td><?=$settlement['id'];?></td>
			<td><?=$settlement['date_created'];?></td>
			<td><?=$settlement['amount'];?></td>
			<td><a href="/admin/bookings/settlement_details?id=<?=$settlement['id']?>">details</a></td>
		</tr>
		<?php
		endforeach;
        ?>
	</tbody>
</table>
<?php else: ?>
<p>There are no settlements.</p>
<?php endif; ?>
