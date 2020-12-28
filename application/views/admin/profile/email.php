<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>

<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=email" method="post">
        <?= $section_switcher ?>

        <input type="hidden" name="contact_id" value="<?=$contact_id;?>" />
        <?php foreach($notifications as $notification) { ?>
            <input name="contactdetail_id[]" type="hidden" value="<?= $notification['id'] ?>" />
            <input name="contactdetail_type_id[]" type="hidden" value="<?= $notification['type_id'] ?>" />
            <input name="contactdetail_value[]" type="hidden" value="<?= $notification['value'] ?>" />
        <?php } ?>

        <section>
            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_textarea('Default Message Signature', 'default_messaging_signature', @$user['default_messaging_signature'], array('id' => 'default_messaging_signature', 'rows' => 3)) ?>
                </div>
            </div>

            <?php if (Settings::instance()->get('imap_per_user') == 1) { ?>
                <div>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?= Form::ib_input('Imap/Pop Username', 'imap[username]', @$imap_settings['username'], array('id' => 'imap-username')) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?= Form::ib_input('Password', 'imap[password]', @$imap_settings['password'], array('type' => 'password', 'id' => 'imap-password')) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?= Form::ib_input('Imap/Pop Host', 'imap[host]', @$imap_settings['host'], array('id' => 'imap-host')) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?= Form::ib_input('Imap/Pop Port', 'imap[port]', @$imap_settings['port'], array('id' => 'imap-port')) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php
                            $options = array('' => 'None', 'SSL' => 'SSL', 'TLS' => 'TLS');
                            echo Form::ib_select('Imap/Pop Security', 'imap[security]', $options, @$imap_settings['security'], array('id' => 'imap-security'));
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php
                            $options = array('0' => 'Imap', '1' => 'Pop3');
                            echo Form::ib_select('Imap/Pop Protocol', 'imap[use_pop3]', $options, @$imap_settings['use_pop3'], array('id' => 'imap-use_pop3'));
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php
                            $value = @$imap_settings['auto_sync_minutes'] ? @$imap_settings['auto_sync_minutes'] : 10;
                            echo Form::ib_input('Sync period (minutes)', 'imap[auto_sync_minutes]', $value, array('id' => 'imap-auto_sync_minutes'));
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
