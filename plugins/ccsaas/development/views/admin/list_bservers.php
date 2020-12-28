<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>
<?php
$has_edit = (in_array(Settings::instance()->get('ccsaas_mode'), array(Model_Ccsaas::CENTRAL, Model_Ccsaas::SELF))) && Auth::instance()->has_access('ccsaas_edit');
?>
<div class="">
    <?php
    if ($has_edit) {
    ?>
    <a class="btn" href="/admin/ccsaas/edit_bserver"><?=__('Create new server')?></a><br />
    <?php
    }
    ?>
    <table id="ccsaas_bservers" class="table">
        <thead>
            <tr>
                <th><?=__('Host')?></th>
                <th><?=__('IP')?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($bservers as $bserver) {
        ?>
            <tr>
                <td><?=$bserver->host?></td>
                <td><?=$bserver->ip4?></td>
                <td>
                <?php
                if ($has_edit) {
                ?>
                    <a href="/admin/ccsaas/edit_bserver?id=<?=$bserver->id?>" class="btn"><?=__('edit')?></a>
                <?php
                }
                ?>
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