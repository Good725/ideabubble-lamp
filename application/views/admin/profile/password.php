<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>

<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=password" method="post">
        <?= $section_switcher ?>

        <input type="hidden" name="contact_id" value="<?=$contact_id;?>" />

        <section>
            <div class="form-group">
                <div class="col-sm-6">
                    <h2><?=__('Change Password')?></h2>
                </div>
            </div>

            <?php if ($user['password'] != '!') { ?>
            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Current Password'), 'current_password', null, array('type' => 'password', 'id' => 'edit_profile_password', 'autocomplete' => 'off', "required" => "required")); ?>
                </div>
            </div>
            <?php } ?>

            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('New Password'), 'password', null, array('type' => 'password', 'id' => 'edit_profile_new_password', 'autocomplete' => 'off', "required" => "required")); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Confirm Password'), 'mpassword', null, array('type' => 'password', 'id' => 'edit_profile_mpassword', 'autocomplete' => 'off', "required" => "required")); ?>
                </div>
            </div>
        </section>

        <div class="form-action-group" id="ActionMenu">
            <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Save')?></button>
            <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Save & Exit')?></button>
            <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
            <a href="/admin" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>