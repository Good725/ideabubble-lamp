<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>
<?php
$has_edit = (in_array(Settings::instance()->get('ccsaas_mode'), array(Model_Ccsaas::CENTRAL, Model_Ccsaas::SELF))) && Auth::instance()->has_access('ccsaas_edit');
?>
<div class="">
    <?php
    if ($has_edit) {
    ?>
    <a class="btn" href="/admin/ccsaas/edit"><?=__('Create new website')?></a><br />
    <?php
    }
    ?>
    <table id="ccsaas_hosts" class="table">
        <thead>
            <tr>
                <th><?=__('Hostname')?></th>
                <th><?=__('Starts')?></th>
                <th><?=__('Expires')?></th>
                <th><?=__('Date Created')?></th>
                <th><?=__('Date Modified')?></th>
                <th><?=__('Action')?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($websites as $website) {
        ?>
            <tr>
                <td><?=$website->hostname?></td>
                <td><?=$website->starts?></td>
                <td><?=$website->expires?></td>
                <td><?=$website->date_created?></td>
                <td><?=$website->date_modified?></td>
                <td>
                <?php
                if ($has_edit) {
                ?>
                    <a href="/admin/ccsaas/edit?id=<?=$website->id?>" class="btn"><?=__('edit')?></a>
                <?php
                } else {
                ?>
                    <a href="/admin/ccsaas/view?id=<?=$website->id?>" class="btn"><?=__('view')?></a>
                <?php
                }
                ?>
                    <br />
                    <a class="btn" target="_blank" href="http://<?=$website->hostname?>"><?=__('open webpage')?></a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
        <tfoot>

        </tfoot>
    </table>
</div>