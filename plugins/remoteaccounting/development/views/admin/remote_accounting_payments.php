<form id="payments-list-filter">
    <label><?=('Status')?></label>
    <select name="status">
        <?php
        echo html::optionsFromArray(
            array(
                'local' => ('Local Only Payments'),
                'remote' => ('Remote Only Payments'),
                'synced' => ('Synced Payments')
            ),
            @$status ?: 'synced'
        );
        ?>
    </select>
    <br />
    <button type="submit" name="list" value="list"><?=__('List')?></button>
</form>
<form method="post" action="/admin/remoteaccounting/sync_payments">
<table id="remote_accounting_payments" class="table">
    <thead>
        <tr>
            <th><?=__('Name')?></th>
            <th><?=__('Payment ID')?></th>
            <th><?=__('Method')?></th>
            <th><?=__('Amount')?></th>
            <th><?=__('Remote Id')?></th>
            <th><?=__('Status')?></th>
            <th><?=__('Sync Date')?></th>
            <?php if ($status != 'synced') { ?>
                <th><?=__('Select')?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($payments) {
        $date_format = Settings::instance()->get('date_format') ?: 'd-m-Y H:i';
        foreach ($payments as $payment) {
    ?>
        <tr>
            <td><?=$payment['name'] ?: 'Remote Contact Id:' . $payment['remote_contact_id']?></td>
            <td><?=$payment['id']?></td>
            <td><?=$payment['type']?></td>
            <td><?=$payment['amount']?></td>
            <td><?=$payment['remote_id']?></td>
            <td>
                <?php
                if (@$payment['remote_id'] && @$payment['id']) {
                    echo __('Synced');
                } else if (@$payment['remote_id'] && !$payment['id']) {
                    echo __('Remote Only');
                } else {
                    echo __('Local Only');
                }
                ?>
            </td>
            <td><?=$payment['synced'] ? date($date_format, strtotime($payment['synced'])) : ''?></td>
            <?php if ($status != 'synced') { ?>
            <td><input type="checkbox" name="payments[<?=$status == 'local' ? 'id' : 'remote_id'?>][]" value="<?=$status == 'local' ? $payment['id'] : $payment['remote_id']?>" /> </td>
            <?php } ?>
        </tr>
    <?php
        }
    }
    ?>
    </tbody>
    <tfoot>
    <?php if ((@$status ?: 'synced') == 'synced') { ?>
        <tr>
            <th colspan="7">
                <button type="submit" name="clear" value="CLEAR"><?=__('Clear Sync Data')?></button>
            </th>
        </tr>
    <?php } ?>
    <?php if ($status != 'synced') { ?>
    <tr>
        <th colspan="7">

                <?php
                if ($status == 'synced') {
                    $direction = 'BOTH';
                } else if ($status == 'local') {
                    $direction = 'REMOTE';
                } else {
                    $direction = 'LOCAL';
                }
                ?>
                <input type="hidden" name="direction" value="<?=$direction?>" />
                <button type="submit" name="sync"><?=__('Sync')?></button>
        </th>
    </tr>
    <?php } ?>
    </tfoot>
</table>
</form>