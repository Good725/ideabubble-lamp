<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>

<div>
<form method="post">
    <h1>Order Details</h1>

	<dl class="dl-horizontal">
		<dt><?= __('Created') ?></dt>
		<dd><?= date('H:i j F Y', strtotime($order['created'])) ?></dd>

		<dt><?= __('Last Updated') ?></dt>
		<dd><?= date('H:i j F Y', strtotime($order['updated'])) ?></dd>

        <dt><?= __('Currency') ?></dt>
        <dd><?= $order['currency'] ?></dd>

		<dt><?= __('Total') ?></dt>
		<dd><?= $currencies[$order['currency']]['symbol'] . $order['total'] ?></dd>

		<dt><?= __('Total Received') ?></dt>
		<dd><?= $currencies[$order['currency']]['symbol'] . $order['total_paid'] ?></dd>

		<dt><?= __('Commission') ?></dt>
		<dd><?= $order['commission_type'] == 'Fixed' ? $currencies[$order['currency']]['symbol'] . $order['commission_amount'] : $order['commission_amount'].'%' ?> + <?=$currencies[$order['currency']]['symbol'] . $order['commission_fixed_charge_amount'] . ' => ' . $order['commission_total']?></dd>

		<dt><label for="view-order-status">Status</label></dt>
		<dd>
			<div style="margin: 0 -15px;">
				<div class="col-sm-4">
					<select class="form-control" name="status" id="view-order-status">
						<option value="">Change Status</option>
						<?=html::optionsFromArray(array('PROCESSING' => 'Processing', 'PAID' => 'Paid', 'CANCELLED' => 'Cancelled'), $order['status'])?>
					</select>
				</div>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="status_reason" placeholder="Reason for change" value="<?=html::chars($order['status_reason'])?>" />
				</div>
				<div class="col-sm-4">
					<button class="btn btn-default" type="submit" name="action" value="status">Change Status</button>
				</div>
			</div>
		</dd>
	</dl>

    <br clear="both" />

    <h2>Billing</h2>

	<dl class="dl-horizontal">
		<dt>Name</dt>
		<dd><?= $order['firstname'].' '.$order['lastname'] ?></dd>

		<dt>Email</dt>
		<dd><?= $order['email'] ?></dd>

		<dt>Address 1</dt>
		<dd><?= $order['address_1'] ?></dd>

		<dt>Address 2</dt>
		<dd><?= $order['address_2'] ?></dd>

		<dt>City</dt>
		<dd><?= $order['city'] ?></dd>

		<dt>Country</dt>
		<dd><?= $order['country_id'] ?></dd>

		<dt>County</dt>
		<dd><?= $order['county_id'] ?></dd>

		<dt>Email</dt>
		<dd><?= $order['email'] ?></dd>

		<dt>IP</dt>
		<dd><?= long2ip($order['ip4']) ?></dd>
	</dl>

    <h2>Items</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Type</th><th>Event</th><th>Ticket</th><th>Quantity</th><th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($order['items'] as $item) { ?>
            <tr>
                <td><?=$item['type']?></td>
                <td><?=$item['event']?></td>
                <td><?=$item['name']?></td>
                <td><?=$item['quantity']?></td>
                <td><?=$item['total'] ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>



    <h2>Payments</h2>
    <table class="table">
        <thead>
            <tr>

				<th scope="col"><?= __('ID') ?></th>
				<th scope="col"><?= __('Customer') ?></th>
				<th scope="col"><?= __('Type') ?></th>
				<th scope="col"><?= __('Amount') ?></th>
				<th scope="col"><?= __('Gateway Fee')?></th>
				<th scope="col"><?= __('Status') ?></th>
				<th scope="col"><?= __('Note') ?></th>
				<th scope="col"><?= __('Updated') ?></th>
				<?php if ($full_access): ?>
					<th scope="col"><?= __('Actions') ?></th>
				<?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($order['payments'] as $payment) { ?>
            <tr>
                <td><?=$payment['id']?></td>
				<td><?=trim($order['firstname'].' '.$order['lastname'])?></td>
				<td><?=$payment['paymentgw']?></td>
                <td><?=$payment['amount']?></td>
				<td><?=$payment['paymentgw_fee']?></td>
                <td><?=$payment['status']?></td>
                <td><?=$payment['status_reason']?></td>
                <td><?=IbHelpers::relative_time_with_tooltip($payment['updated'])?></td>

				<?php if ($full_access): ?>
					<td>
					<?php if ($payment['status'] == 'PAID' && in_array($payment['paymentgw'], array('stripe', 'realex'))) { ?>
						<a class="btn payment-void" data-id="<?=$payment['id']?>" data-toggle="modal" data-target="#payment-void-modal">Refund</a>
					<?php } ?>
					</td>
				<?php endif; ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>

	<h2>Payment Plan / Group Payment</h2>
	<table class="table">
		<thead>
		<tr>

			<th scope="col"><?= __('ID') ?></th>
            <th scope="col"><?= __('Name') ?></th>
			<th scope="col"><?= __('Email') ?></th>
			<th scope="col"><?= __('Amount') ?></th>
			<th scope="col"><?= __('Due Date') ?></th>
			<th scope="col"><?= __('Status') ?></th>
			<?php if ($full_access): ?>
				<th scope="col"><?= __('Actions') ?></th>
			<?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($order['partialpayments'] as $payment) { ?>
			<tr>
				<td><?=$payment['id']?></td>
                <td><?=$payment['payer_email'] ? $payment['payer_name'] : $order['firstname'].' '.$order['lastname']?></td>
				<td><?=$payment['payer_email'] ?: $order['email']?></td>
				<td><?=$payment['payment_amount']?></td>
                <td <?=$payment['due_date'] && strtotime($payment['due_date']) <= time() ? 'style="color:red;"' : ''?>><?=$payment['due_date'] ? $payment['due_date'] : 'Deposit'?></td>
                <td><?=$payment['status']?></td>
				<td>
					<a>
					<?php if ($payment['payment_id'] == null) { ?>
						<a class="btn" href="/checkout.html?partial_id=<?=$payment['id']?>&url_hash=<?=$payment['url_hash']?>" /><?=__('Pay Now')?></a>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

    <?php if (false) { ?>
    <!-- hidden as requested. not removed inc ase this will be needed again -->
    <h2>Tickets</h2>
    <table class="table table-striped table-condensed" id="list-tickets-table">
        <thead>
        <tr>
			<th scope="col"><?= __('ID') ?></th>
			<th scope="col"><?= __('Type') ?></th>
			<th scope="col"><?= __('Event') ?></th>
			<th scope="col"><?= __('Customer') ?></th>
			<th scope="col"><?= __('Price') ?></th>
			<th scope="col"><?= __('Attended') ?></th>
			<th scope="col"><?= __('Time Attended') ?></th>
			<th scope="col"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($order['tickets'] as $ticket) {
            ?>
            <tr>
                <td><a href="/admin/events/ticket_details/<?=$ticket['id']?>"><?=$ticket['id']?></a></td>
				<td><a href="/admin/events/ticket_details/<?=$ticket['id']?>"><?=$ticket['ticket']. ' (' .$ticket['type'].')'?></a></td>
				<td><a href="/admin/events/ticket_details/<?=$ticket['id']?>"><?=$ticket['event']?></a></td>
				<td><a href="/admin/events/ticket_details/<?=$ticket['id']?>"><?=$ticket['buyer_order_firstname'] . ' ' . $ticket['buyer_order_lastname']?></a></td>
				<td><a href="/admin/events/ticket_details/<?=$ticket['id']?>"><?= (($ticket['currency'] == 'EUR') ? '&euro;' : $ticket['currency']).$ticket['price'] ?></a></td>
				<td></td>
				<td></td>
				<td>
                    <a href="/admin/events/ticket/<?= $ticket['order_id'] ?>?action=download"><?= __('Download Ticket') ?></a>
                </td>
			</tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php } ?>
</form>
</div>

<?php if ($full_access): ?>
	<div class="modal fade" id="payment-void-modal" tabindex="-1" role="dialog" aria-labelledby="payment-void-modal-label">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form method="post" action="/admin/events/payment_refund">
					<input type="hidden" name="id" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="edit-event-discount-delete-modal-label"><?= __('Confirm Refund') ?></h4>
					</div>
					<div class="modal-body clearfix">
						<p><?= __('Are you sure you want to refund this payment? It can not be reverted!') ?></p>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?=__('Reason')?></label>
							<div class="col-sm-4 form-group">
								<select name="reason" class="form-control">
                                    <option value=""></option>
                                    <option value="duplicate">Duplicate</option>
                                    <option value="fraudulent">Fraudulent</option>
                                    <option value="requested_by_customer">Requested by customer</option>
                                    <option value="event_canceled">Event cancelled</option>
                                    <option value="event_postponed">Event postponed</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?=__('Amount')?></label>
							<div class="col-sm-4 form-group">
								<input type="text" class="form-control" name="amount" value="<?=$order['total'] - $order['commission_total']?>" />
							</div>
						</div>

						<p>     </p>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger" id="payment-void-modal-btn" data-row-index="" disabled><?= __('Refund') ?><span class="timer"></span> </button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
	var enableRefundInterval = null;
	var enableRefundCounter = 0;
	$(".payment-void").on("click", function(){
		var paymentId = $(this).data("id");
		$("#payment-void-modal input[name=id]").val(paymentId);
		$("#payment-void-modal-btn").prop("disabled", true);
		if (enableRefundInterval) {
			clearInterval(enableRefundInterval);
		}
		enableRefundCounter = 5;
		$("#payment-void-modal-btn .timer").html(" " + enableRefundCounter + " ");
		enableRefundInterval = setInterval(function(){
			--enableRefundCounter;
			if (enableRefundCounter <= 0) {
				$("#payment-void-modal-btn").prop("disabled", false);
				clearInterval(enableRefundInterval);
				$("#payment-void-modal-btn .timer").html(" ");
			} else {
				$("#payment-void-modal-btn .timer").html(" " + enableRefundCounter + " ");
			}
		}, 1000);
	});
	</script>
<?php endif; ?>
