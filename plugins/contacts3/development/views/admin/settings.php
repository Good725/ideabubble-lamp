<?php // Currently this page depreciated as we now access contacts settings from /admin/settings?group=Contacts ?>
<form method="post" name="contacts3_settings" id="contacts3_settings">
<div id="roles_preferences">
    <?php
    $pindex = 0;
    foreach ($roles as $role) {
    ?>
        <fieldset>
            <legend><?= $role['name'] ?> <a class="view">View</a></legend>

            <table class="table" style="display: none;">
                <thead>
                <tr><th>Feature</th><th>Allowed</th></tr>
                </thead>
                <tbody>
                <?php
                $groups = array(
                    'notification' => 'Notifications',
                    'special' => 'Special',
                    'family_permission' => 'Family',
                    'contact' => 'Contact',
                );

                foreach ($groups as $group => $glabel) {
                    $yes = false;
                    foreach ($settings as $setting) {
                        if ($setting['role_id'] == $role['id'] && $setting['group'] == $group && $setting['preference'] == 'all') {
                            $yes = $setting['allowed'] == 1;
                            break;
                        }
                    }
                ?>
                <tr>
                    <td><?= $glabel ?></td>
                    <td>
                        <div class="btn-group" data-toggle="buttons">
                            <input type="hidden" name="permission[<?=$pindex?>][role_id]" value="<?=$role['id']?>" />
                            <input type="hidden" name="permission[<?=$pindex?>][group]" value="<?=$group?>" />
                            <input type="hidden" name="permission[<?=$pindex?>][preference]" value="all" />
                            <label class="btn <?= $yes ? 'active' : '' ?>"><input type="radio"
                                                                                  value="1" <?= $yes ? 'checked="checked"' : '' ?>
                                                                                  name="permission[<?=$pindex?>][allowed]">Yes</label>
                            <label class="btn <?= $yes ? '' : 'active' ?>"><input
                                    type="radio" <?= $yes ? '' : 'checked="checked"' ?> value="0"
                                    name="permission[<?=$pindex?>][allowed]">No</label>
                        </div>
                    </td>
                </tr>
                    <?php
                    ++$pindex;
                    foreach ($preferences as $preference) {
                        if ($group != $preference['group']) continue;
                        $yes = false;
                        foreach ($settings as $setting) {
                            if ($setting['role_id'] == $role['id'] && $setting['group'] == $preference['group'] && $setting['preference'] == $preference['stub']) {
                                $yes = $setting['allowed'] == 1;
                                break;
                            }
                        }
                        ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $glabel . ' -> ' . $preference['label'] ?></td>
                    <td>
                        <div class="btn-group" data-toggle="buttons">
                            <input type="hidden" name="permission[<?=$pindex?>][role_id]" value="<?=$role['id']?>" />
                            <input type="hidden" name="permission[<?=$pindex?>][group]" value="<?=$group?>" />
                            <input type="hidden" name="permission[<?=$pindex?>][preference]" value="<?=$preference['stub']?>" />
                            <label class="btn <?= $yes ? 'active' : '' ?>"><input type="radio"
                                                                                  value="1" <?= $yes ? 'checked="checked"' : '' ?>
                                                                                  name="permission[<?=$pindex?>][allowed]">Yes</label>
                            <label class="btn <?= $yes ? '' : 'active' ?>"><input
                                    type="radio" <?= $yes ? '' : 'checked="checked"' ?> value="0"
                                    name="permission[<?=$pindex?>][allowed]">No</label>
                        </div>
                    </td>
                </tr>
                <?php
                        ++$pindex;
                    }
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <br />
        <?php
        ++$pindex;
    }
    ?>
</div>
    <button class="btn btn-primary" type="submit" name="action" value="save">Save</button>
</form>

<script>
    $("#roles_preferences a.view").on("click", function(){
        var $table = $(this).parent().next('table');
        if ($table.css('display') == 'none') {
            $table.css('display', '');
            $(this).html('Hide');
        } else {
            $table.css('display', 'none');
            $(this).html('View');
        }
    });
    /*var permissions = {};
    $("#resources_table input").on("change", function(){
        if (!permissions["_"+$(this).data("resource_id")]) {
            permissions["_"+$(this).data("resource_id")] = {};
        }
        permissions["_"+$(this).data("resource_id")]["_"+$(this).data("role_id")] = this.value;
    });
    $("#contacts3_settings").on("submit", function(){
        $.post(
            "/admin/contacts3/settings",
            {
                action: "save",
                permissions: JSON.stringify(permissions)
            },
            function (response) {
                alert("Permissions updated");
            }
        );
        return false;
    });*/
</script>