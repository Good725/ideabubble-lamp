<div class="col-sm-12">
    <div id="transaction_form_alert"></div>
    <div class="row-fluid header list_notes_alert <?= (isset($alert)) ? 'alert' : '' ?> alert-warning">
        <?= (isset($alert)) ? $alert : '' ?>
    </div>

    <table class="col-sm-12 table table-striped transactions dataTable">
        <thead>
        <tr>
            <th scope="col">#ID</th>
            <th scope="col">Payer</th>
            <th scope="col">Type</th>
            <th scope="col">Amount</th>
            <th scope="col">Outstanding</th>
            <th scope="col">Status</th>
            <th scope="col">Updated</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $transaction) { ?>
            <tr data-id="<?=$transaction['id']?>">
                <td><?=$transaction['id']?></td>
                <td><?=$transaction['contact'] . $transaction['family'] . $transaction['username']?></td>
                <td><?=$transaction['transaction_type'] == 'Business' && $transaction['reason'] ? $transaction['reason'] : $transaction['transaction_type']?></td>
                <td><?=money_format('%.2n', $transaction['total'])?></td>
                <td><?=money_format('%.2n', $transaction['outstanding'])?></td>
                <td><?=$transaction['status']?></td>
                <td><?=IBHelpers::relative_time_with_tooltip($transaction['updated'])?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
