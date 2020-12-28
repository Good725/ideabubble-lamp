<form id="contacts-list-filter">
    <label><?=('Status')?></label>
    <select name="status">
        <?php
        echo html::optionsFromArray(
            array(
                'local' => ('Local Only Contacts'),
                'remote' => ('Remote Only Contacts'),
                'synced' => ('Synced Contacts')
            ),
            @$status ?: 'synced'
        );
        ?>
    </select>
    <br />
    <button type="submit" name="list" value="list"><?=__('List')?></button>
</form>
<form method="post" action="/admin/remoteaccounting/sync_contacts">
<table id="remote_accounting_contacts" class="table">
    <thead>
        <tr>
            <th><?=__('Name')?></th>
            <th><?=__('ID')?></th>
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
    if ($contacts) {
        $date_format = Settings::instance()->get('date_format');
        foreach ($contacts as $i => $contact) {
    ?>
        <tr>
            <td><?=$contact['name']?></td>
            <td><?=$contact['id']?></td>
            <td><?=$contact['remote_id']?></td>
            <td>
                <?php
                if (@$contact['remote_id'] && @$contact['id']) {
                    echo __('Synced');
                } else if (@$contact['remote_id'] && !$contact['id']) {
                    echo __('Remote Only');
                } else {
                    echo __('Local Only');
                }
                ?>
            </td>
            <td><?=$contact['synced'] ? date($date_format, strtotime($contact['synced'])) : ''?></td>
            <?php if ($status != 'synced') { ?>
            <td><input type="checkbox" name="contacts[<?=$status == 'local' ? 'id' : 'remote_id'?>][]" value="<?=$status == 'local' ? $contact['id'] : $contact['remote_id']?>" /> </td>
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
            <th colspan="6">
                <button type="submit" name="clear" value="CLEAR"><?=__('Clear Sync Data')?></button>
            </th>
        </tr>
    <?php } ?>
    <?php if ($status != 'synced') { ?>
    <tr>
        <th colspan="6">

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