<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>
<style>
    .profile-notification-label {
        padding-left: 2em;
    }
</style>
<?php
if (@$contact3) {
    $contact_preferences = $contact3->get_preferences();
}
?>

<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=notifications" method="post">
        <input type="hidden" name="contact_id" value="<?= $contact_id ?>" />

        <?= $section_switcher ?>

        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) { ?>
        <section>
            <div class="form-group">
                <div class="col-sm-12">
                    <?php foreach($notifications as $notification): ?>
                        <input type="hidden" name="contactdetail_id[]"      value="<?= $notification['id'] ?>"      />
                        <input type="hidden" name="contactdetail_type_id[]" value="<?= $notification['type_id'] ?>" />
                        <input type="hidden" name="contactdetail_value[]"   value="<?= $notification['value'] ?>"   />
                    <?php endforeach; ?>

                    <?php
                    $input_index = 0;
                    foreach ($contact3->get_preferences() as $save_preference) {
                        $add = true;
                        foreach ($preferences as $preference) {
                            if ($save_preference['preference_id'] == $preference['id'] && $preference['group'] == 'notification') {
                                $add = false;
                                break;
                            }
                        }
                        if ($add) {
                            ?>
                            <input type="hidden" name="preferences[<?= $input_index?>]" value="<?= $save_preference['preference_id'] ?>"/>
                            <?php
                            ++$input_index;
                        }
                    }
                    ?>

                    <?php foreach ($preferences as $preference) { ?>
                        <?php if ($preference['group'] == 'notification') { ?>
                            <section>
                                <h3><?=$preference['label']?></h3>

                                <?= (trim($preference['summary'])) ? '<p>'.$preference['summary'].'</p>' : '' ?>

                                <div>
                                    <?php $notification_types = array('email' => __('E-mail'), 'sms' => __('Text message'), 'phone' => __('Phone Call')); ?>

                                    <?php foreach ($notification_types as $notification_type => $notification_label): ?>
                                        <?php
                                        $checked = false;
                                        foreach ($contact_preferences as $cpreference) {
                                            if ($cpreference['preference_id'] == $preference['id'] && ($cpreference['notification_type'] == null || $cpreference['notification_type'] == $notification_type) ) {
                                                $checked = true;
                                                break;
                                            }
                                        }
                                        ?>

                                        <div class="form-group">
                                            <div class="profile-notification-label col-xs-8 col-sm-4 col-md-3 col-lg-2"><?= $notification_label ?></div>

                                            <div class="col-xs-4 text-right">
                                                <input type="hidden" name="preferences[<?= $input_index ?>][notification_type]" value="<?= $notification_type ?>" />
                                                <input type="hidden" name="preferences[<?= $input_index ?>][preference_id]"     value="<?= $preference['id'] ?>"  />

                                                <?php
                                                $name = 'preferences['.$input_index.'][value]';
                                                $attributes = array('id' => 'contact_preference_'.$input_index);
                                                echo Form::ib_checkbox_switch(null, $name, $preference['id'], $checked);
                                                ?>
                                            </div>
                                        </div>

                                        <?php ++$input_index; ?>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </section>
        <?php } ?>

        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'events')) { ?>
        <section>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="notify_email_on_buy_ticket" id="notify_email_on_buy_ticket" value="1" <?=$account['notify_email_on_buy_ticket'] == 1 ? 'checked="checked"' : ''?> />
                    <?=__('When someone buys a ticket, email me')?>
                </label>
            </div>
        </section>
        <?php } ?>

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
