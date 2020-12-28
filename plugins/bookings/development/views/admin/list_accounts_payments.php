<h2>Payments</h2>
<div class="row-fluid header list_notes_alert <?= (isset($alert)) ? 'alert' : '' ?> alert-warning">
    <?= (isset($alert)) ? $alert : '' ?>
</div>
<?php if ( ! empty($payments) OR ! empty($transactions)): ?>
    <?php if ( ! empty($payments)): ?>
    <table class="table table-striped dataTable contact_payment_table">
        <thead>
        <tr>
            <th scope="col">Payment ID</th>
            <th scope="col">Type</th>
            <th scope="col">Transaction ID</th>
            <th scope="col">Amount</th>
            <th scope="col">Fee</th>
            <th scope="col">Total</th>
            <th scope="col">Currency</th>
            <th scope="col">Status</th>
            <th scope="col">Due Date</th>
            <th scope="col">Settlement</th>
            <th scope="col">Note</th>
            <th scope="col">Updated</th>
<!--            <th scope="col">Edited By</th>-->
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($payments as $key=>$payment):
            ?>
            <tr class="payment_row" data-payment_id="<?=$payment['id'] ?>">
                <td><?=$payment['id'];?></td>
                <td><?=$payment['type'];?></td>
                <td><?=$payment['transaction_id'];?></td>
                <td><?=money_format('%.2n', $payment['amount']);?></td>
                <td><?=money_format('%.2n', $payment['bank_fee']);?></td>
                <td><?=money_format('%.2n', $payment['amount'] + $payment['bank_fee']);?></td>
                <td><?=$payment['currency'];?></td>
                <td><?=$payment['status'] . ($payment['journaled_payment_id'] ? ' Payment ID #' . $payment['journaled_payment_id'] : '')?></td>
                <td><?=@$payment['due_date']?></td>
                <td><?=$payment['settlement_id']?></td>
                <td>
                    <span class="popinit" data-original-title="Payment Notes" data-placement="left" rel="popover" data-content="<?=(@$payment['payment_plan_id'] && !@$payment['payment_id'] && $payment['failed_auto_payment_attempts'] > 0 ? sprintf(__(' payment failed %s times'), @$payment['failed_auto_payment_attempts']) : '') . $payment['note'];?>">
                        <i class="icon-book"></i>
                    </span>
                </td>
                <td><?=$payment['created'];?></td>
<!--                <td>--><?//= $payment['modified_by_name'].' '.$payment['modified_by_surname'] ?><!--</td>-->
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
    <?php endif; ?>



    <?php if ( ! empty($transactions)): ?>
    <h2>Transaction History</h2>
    <table class="table table-striped dataTable contact_transaction_history_table">
        <thead>
        <tr>
            <th scope="col">History ID</th>
            <th scope="col">Booking #</th>
            <th scope="col">Schedule</th>
            <th scope="col">Type</th>
            <th scope="col">Total</th>
            <th scope="col">Updated</th>
<!--            <th scope="col">Editor</th>-->
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($transactions as $key=>$transaction):
            ?>
            <tr>
                <td><?=$transaction['id'];?></td>
                <td><?=$transaction['booking_id'];?></td>
                <td><?=$transaction['schedule'];?></td>
                <td><?=$transaction['type'];?></td>
                <td><?=money_format('%.2n', $transaction['total']);?></td>
                <td><?=$transaction['updated']?></td>
<!--                <td>--><?//= $transaction['modified_by_name'].' '.$transaction['modified_by_surname'] ?><!--</td>-->
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
    <?php endif; ?>

<?php else: ?>
    <p>There are no payments.</p>
<?php endif; ?>
