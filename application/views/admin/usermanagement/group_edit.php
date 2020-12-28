<?php
if (!isset($role)) {
    $role = array('id' => 'new', 'role' => '', 'description' => '');
}
?>
<div id="group-edit-modal" class="modal fade" style="overflow-y: hidden; bottom: unset;">
    <form id="group-edit" name="group-edit" method="post" action="/admin/usermanagement/group/<?=@$role['id']?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3><span>Edit</span> Group <?=@$role['role']?></h3>
            </div>
            <div class="modal-body form-horizontal">
                    <input type="hidden" name="id" value="<?=@$role['id']?>" />

                    <ul class="nav nav-tabs nav-tabs-contact">
                        <li class="active"><a href="#details-tab" data-toggle="tab">Details</a></li>
                        <li><a href="#permissions-tab" data-toggle="tab">Permissions</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="details-tab">
                            <fieldset>
                                <div class="form-group">
                                    <div class="col-sm-2"><label class="control-label" for="role">Name *</label></div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-input" name="role" id="role" value="<?=@$role['role']?>" placeholder="Group Name" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2"><label class="control-label" for="description">Description</label></div>
                                    <div class="col-sm-10"><textarea class="form-input" name="description" id="description" placeholder="Description"><?=@$role['description']?></textarea></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2"><label class="control-label" for="allow_frontend_register">Allow Frontend Register</label></div>
                                    <div class="col-sm-10"><input type="checkbox" name="allow_frontend_register" id="allow_frontend_register" <?=@$role['allow_frontend_register'] ? 'checked="checked"'  : ''?> value="1" /></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2"><label class="control-label" for="allow_api_register">Allow API Register</label></div>
                                    <div class="col-sm-10"><input type="checkbox" name="allow_api_register" id="allow_api_register" <?=@$role['allow_api_register'] ? 'checked="checked"'  : ''?> value="1" /></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2"><label class="control-label" for="allow_frontend_login">Allow Frontend Login</label></div>
                                    <div class="col-sm-10"><input type="checkbox" name="allow_frontend_login" id="allow_frontend_login" <?=@$role['allow_frontend_login'] ? 'checked="checked"'  : ''?> value="1" /></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2"><label class="control-label" for="allow_api_login">Allow API Login</label></div>
                                    <div class="col-sm-10"><input type="checkbox" name="allow_api_login" id="allow_api_login" <?=@$role['allow_api_login'] ? 'checked="checked"'  : ''?> value="1" /></div>
                                </div>
                                <?php if (@$dashboards) { ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="edit_profile_default_dashboard">Default Dashboard</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="edit_profile_default_dashboard" name="default_dashboard_id">
                                            <option value="-1" <?= @$role['default_dashboard_id'] == -1 ? ' selected="selected"' : '' ?>>Use Main Dashboard</option>
                                            <option value="0" <?= @$role['default_dashboard_id'] == 0 ? ' selected="selected"' : '' ?>>Use Role Dashboard</option>
                                            <?php foreach ($dashboards as $dashboard): ?>
                                                <option value="<?= $dashboard['id'] ?>"<?= $dashboard['id'] == @$role['default_dashboard_id'] ? ' selected="selected"' : '' ?>><?= $dashboard['title'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                            </fieldset>
                        </div>
                        <div class="tab-pane" id="permissions-tab">
                            <div style="max-height: 600px; overflow: auto;">
                            <?php
                            foreach ($controllers as $controller) {
                                $cyes = Model_Roles::has_permission($role['id'], $controller->id);
                                ?>
                                <fieldset>
                                    <legend>
                                        <?= $controller->name ?>

                                        <span style="font-size: 12px;">
                                            <input type="hidden" name="resource[<?= $controller->id; ?>]" value="0"/>
                                            <?= Form::ib_checkbox_switch(null, 'resource['.$controller->id.']', '1', !!$cyes, ['style' => 'position: relative;']) ?>
                                        </span>
                                    </legend>

                                    <?php if (count($controller->get_actions_4_controller()) > 0) { ?>
                                    <table class="table" <?=$cyes ? '' : 'style="display:none"'?>>
                                        <thead>
                                        <tr><th>Feature</th><th style="width: 80px;">Permission</th></tr>
                                        </thead>
                                        <tbody>
                                        <!-- <tr>
                                    <td><?=$controller->alias?></td>
                                    <td>
                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn"><input type="radio" value="1" name="resource[<?= $controller->id; ?>]">Yes</label>
                                            <label class="btn"><input type="radio" value="0" name="resource[<?= $controller->id; ?>]">No</label>
                                        </div>
                                    </td>
                                </tr> -->
                                        <?php
                                        foreach ($controller->get_actions_4_controller() as $action) {
                                            $yes = Model_Roles::has_permission($role['id'], $action->id);
                                            ?>
                                            <tr>
                                                <td><?=$action->name?></td>
                                                <td>
                                                    <input type="hidden" name="resource[<?= $action->id; ?>]" value="0" />
                                                    <?= Form::ib_checkbox_switch(null, 'resource['.$action->id.']', '1', !!$yes,  ['style' => 'position: relative;']); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        foreach ($controller->get_code_pieces_4_controller() as $action) {
                                            $yes = Model_Roles::has_permission($role['id'], $action->id) ? $cyes : false;
                                            ?>
                                            <tr>
                                                <td><?=$action->name?></td>
                                                <td>
                                                    <input type="hidden" name="resource[<?= $action->id; ?>]" value="0" />
                                                    <?= Form::ib_checkbox_switch(null, 'resource['.$action->id.']', '1', !!$yes, ['style' => 'position: relative;']); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php } ?>
                                </fieldset>
                                <br />
                                <?php
                            }
                            ?>
                            </div>
                        </div>
                    </div>


            </div>
            <div class="modal-footer">
                <button class="btn" type="submit" name="action" value="save">Save</button>
                <a data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
$("#permissions-tab fieldset").find("legend input").on("change", function() {
    $(this).parents("fieldset").find("table").css("display", this.checked ? '' : 'none');
});
</script>