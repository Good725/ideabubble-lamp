<?= (isset($alert)) ? $alert : ''; ?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>

<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=platform" method="post">
        <?= $section_switcher ?>

        <input type="hidden" name="contact_id" value="<?= $contact_id ?>" />
        <section>
            <div>
                <?php if ($dashboards): ?>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php
                            $options = array('-1' => 'Use Main Dashboard', '0'  => 'Use Role dashboard');
                            foreach ($dashboards as $dashboard) {
                                $options[$dashboard['id']] = $dashboard['title'];
                            }
                            echo Form::ib_select(__('Default Dashboard'), 'default_dashboard_id', $options, $user['default_dashboard_id']);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Default Home Page'), 'default_home_page', $user['default_home_page']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options = array('' => '');
                        $options = $options + Model_Settings::column_toggle();
                        echo Form::ib_select(__('Display Options'), 'user_column_profile', $options, $user['user_column_profile']);
                        ?>
                    </div>
                </div>

                <div class="form-group hidden">
                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Inactive logout (minutes)'), 'auto_logout_minutes', $user['auto_logout_minutes'], array('id' => 'auto_logout_minutes')) ?>
                    </div>
                </div>

                <?php if ($printing) { ?>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php
                            $options = array('' => '');
                            foreach ($printers as $printer) {
                                $options[$printer['email']] = $printer['location'] . ' ' . $printer['tray'] . ' (' . $printer['email'] . ')';
                            }
                            echo Form::ib_select(__('Default Printer'), 'default_eprinter', $options, $user['default_eprinter']);
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <section>
            <div class="form-action-group" id="ActionMenu">
                <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Save')?></button>
                <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Save & Exit')?></button>
                <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
                <a href="/admin" class="btn btn-cancel">Cancel</a>
            </div>
        </section>
    </form>
</div>
