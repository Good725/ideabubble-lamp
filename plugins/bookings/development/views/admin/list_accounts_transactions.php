<h2>Transactions</h2>
<div id="transaction_form_alert"></div>
<div class="row-fluid header list_notes_alert <?= (isset($alert)) ? 'alert' : '' ?> alert-warning">
    <?= (isset($alert)) ? $alert : '' ?>
</div>
<?php if ( ! empty($transactions)): ?>
    <table class="table table-striped dataTable contact_transaction_table">
        <thead>
			<tr>
				<th scope="col">Transaction ID</th>
				<th scope="col">Booking #</th>
<!--                --><?php //if($transactions[0]['family']): ?>
                    <th scope="col">Student Name</th>
<!--                --><?php //endif; ?>
				<th scope="col">Schedule</th>
				<th scope="col">Type</th>
				<th scope="col">Status</th>
				<th scope="col">Amount</th>
				<th scope="col">Fee</th>
				<th scope="col">Discount</th>
				<th scope="col">Total</th>
				<th scope="col">Outstanding</th>
				<!--<th scope="col">Created</th>-->
				<th scope="col">Updated</th>
<!--				<th scope="col">Editor</th>-->
				<th scope="col">Edit</th>
			</tr>
			</thead>
        <tbody>
			<?php
			$date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
			foreach($transactions as $key => $transaction):
			?>
				<?php
				$discount = '<ul>';
				foreach ($transaction['discounts'] as $tx_discount) {
					$discount .= '<li>' . $tx_discount['title'] . ': ' . money_format('%.2n', $tx_discount['amount'] ) . '</li>';
				}
				$discount .= '</ul>';
				?>
				<tr class="transaction-row"
					data-booking_id="<?=$transaction['booking_id']?>"
					data-transaction_id="<?=$transaction['id'];?>"
					data-transaction_balance="<?= $transaction['outstanding']; ?>"
					data-modified_by_id="<?= $transaction['modified_by_id'] ?>"
                    data-multiple_transaction="<?= $transaction['multiple'] ;?>"
					>
					<td><?= $transaction['id'] . ($transaction['creator_role'] == 'Parent/Guardian' ? '<br /><span class="online" style="font-size:10px;">Online</span>' : '')?></td>
					<td><?= $transaction['booking_id']>0?$transaction['booking_id']:'N/A'; ?></td>
<!--                    --><?php //if($transaction['family']): ?>
                        <td><?= $transaction['first_name'].' '.$transaction['last_name']; ?></td>
<!--                    --><?php //endif; ?>
					<td><?= $transaction['schedule']; ?></td>
					<td><?= $transaction['recurring_payments_enabled'] ? __('Subscription Automatic Payment') : $transaction['type']; ?></td>
					<td><?= $transaction['status_label']?></td>
					<td><?= money_format('%.2n', $transaction['amount']); ?></td>
                    <td><?php
                        if ($transaction['pp_interest_total'] > 0 ){
                            echo money_format('%.2n', $transaction['fee'] + $transaction['pp_interest_total']);
                        } else {
                            echo money_format('%.2n', $transaction['fee']);
                        }
                        ?></td>
					<td><a class="popinit" data-placement="bottom" rel="popover" data-html="true" data-content="<?=html::chars($discount)?>"><?= money_format('%.2n', $transaction['discount']); ?></a></td>
					<td><?= money_format('%.2n', $transaction['total']); ?></td>
					<td><?php
						if ($transaction['pp_interest_outstanding'] > 0 ){
                            echo money_format('%.2n', $transaction['outstanding'] + $transaction['pp_interest_outstanding']);
                        } else {
							echo money_format('%.2n', $transaction['outstanding']);
						}
						?></td>
					<!--                <td>--><?//= $transaction['created']; ?><!--</td>-->
					<td><?= date($date_format, strtotime($transaction['updated'])); ?></td>
<!--					<td>--><?//= $transaction['modified_by_name'].' '.$transaction['modified_by_surname'] ?><!--</td>-->
					<td class="edit_transaction">Edit</td>
				</tr>
			<?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>
    <p>There are no transactions.</p>
<?php endif; ?>
<div class="payments_data"></div>

<div class="modal_boxes">

    <?php
        echo View::factory('admin/snippets/transaction_modal_form')->set('accounts', $transactions);
//        echo View::factory('admin/snippets/payment_modal_form')->set('payment_statuses', $payment_statuses);
    ?>

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
