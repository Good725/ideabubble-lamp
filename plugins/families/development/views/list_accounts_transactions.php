<div class="col-sm-12">
	<div id="transaction_form_alert"></div>
	<div class="row-fluid header list_notes_alert <?= (isset($alert)) ? 'alert' : '' ?> alert-warning">
		<?= (isset($alert)) ? $alert : '' ?>
		<?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>
	</div>
	<?php if ( ! empty($transactions)): ?>
		<table class="table table-striped dataTable contact_transaction_table">
			<thead>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Booking</th>
					<th scope="col">Student</th>
					<th scope="col">Schedule</th>
					<th scope="col">Type</th>
					<th scope="col">Total</th>
					<th scope="col">Outstanding</th>
					<th scope="col">Status</th>
					<th scope="col">Updated</th>
					<th scope="col">Edit</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($transactions as $key=>$transaction): ?>
					<tr class="transaction-row"
						data-transaction_id="<?=$transaction['id'];?>"
						data-transaction_balance="<?= $transaction['outstanding']; ?>"
						data-modified_by_id="<?= $transaction['modified_by_id'] ?>"
						data-multiple_transaction="<?= $transaction['multiple'] ;?>"
						>
						<td><?= $transaction['id']; ?></td>
						<td><?= $transaction['booking_id']>0?$transaction['booking_id']:'N/A'; ?></td>
						<td><?= $transaction['first_name'].' '.$transaction['last_name']; ?></td>
						<td><?= $transaction['schedule']; ?></td>
						<td><?= $transaction['type']; ?></td>
						<td><?= money_format('%.2n', $transaction['total']); ?></td>
						<td><?= money_format('%.2n', $transaction['outstanding']);?></td>
						<td><?= $transaction['status_label']?></td>
						<td><?= IBHelpers::relative_time_with_tooltip($transaction['updated']); ?></td>
						<td class="edit_transaction"><span class="icon-pencil"></span></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	<?php else: ?>
		<p>There are no transactions.</p>
	<?php endif; ?>
	<div class="payments_data"></div>

	<div class="modal_boxes">
		<div id="delete_modal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header danger">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3></h3>
					</div>
					<div class="modal-body form-horizontal">
						<div class="alert-area"></div>
						Do you really want to delete the record?
					</div>
					<div class="modal-footer">
						<a href="#" id="delete_modal_btn" class="btn btn-danger">Delete</a>
						<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
			<input type="hidden" id="modal_delete_id">
			<input type="hidden" id="modal_controller">
		</div>

		<div id="alert_modal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3>Transaction hasn't selected</h3>
					</div>
					<div class="modal-body form-horizontal">
						<div class="alert-area"></div>
						Please select a transaction.
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Ok</a>
					</div>
				</div>
			</div>
		</div>

		<div id="no_primary_alert_modal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3>No Primary Contact Set</h3>
					</div>
					<div class="modal-body form-horizontal">
						<div class="alert-area"></div>
						<p>This Family doesn't have a primary contact.</p>
						<p>Please Select a primary contact before you can proceed to making a payment.</p>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Ok</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
