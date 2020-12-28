<form id="transactions-list-filter">
    <label><?=('Status')?></label>
    <select name="status">
        <?php
        echo html::optionsFromArray(
            array(
                'local' => ('Local Only Transactions'),
                'remote' => ('Remote Only Transactions'),
                'synced' => ('Synced Transactions')
            ),
            @$status ?: 'synced'
        );
        ?>
    </select>
    <br />
    <button type="submit" name="list" value="list"><?=__('List')?></button>
</form>
<form method="post" action="/admin/remoteaccounting/sync_transactions">
<table id="remote_accounting_transactions" class="table">
    <thead>
        <tr>
            <th><?=__('Name')?></th>
            <th><?=__('Transaction ID')?></th>
            <th><?=__('Total')?></th>
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
    if ($transactions) {
        $date_format = Settings::instance()->get('date_format') ?: 'd-m-Y H:i';
        foreach ($transactions as $transaction) {
    ?>
        <tr>
            <td><?=$transaction['name'] ?: 'Remote Contact Id:' . $transaction['remote_contact_id']?></td>
            <td><?=$transaction['id']?></td>
            <td><?=$transaction['total']?></td>
            <td><?=$transaction['remote_id']?></td>
            <td>
                <?php
                if (@$transaction['remote_id'] && @$transaction['id']) {
                    echo __('Synced');
                } else if (@$transaction['remote_id'] && !$transaction['id']) {
                    echo __('Remote Only');
                } else {
                    echo __('Local Only');
                }
                ?>
            </td>
            <td><?=$transaction['synced'] ? date($date_format, strtotime($transaction['synced'])) : ''?></td>
            <?php if ($status != 'synced') { ?>
            <td><input type="checkbox" name="transactions[<?=$status == 'local' ? 'id' : 'remote_id'?>][]" value="<?=$status == 'local' ? $transaction['id'] : $transaction['remote_id']?>" /> </td>
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