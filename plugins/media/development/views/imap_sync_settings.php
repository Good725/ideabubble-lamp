<?= (isset($alert)) ? $alert : '' ?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<div id="imap_settings_template" style="padding-top: 10px;">
    <form class="form-horizontal imap_settings_form" name="imap_settings_form" method="post">
        <div class="tab-content">
            <!-- Message tab -->
            <div role="tabpanel" class="tab-pane active">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="settings_file_types">File Types</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="settings[file_types]" id="settings_file_types"><?=@$settings['file_types']?></textarea>
                        </div>
                    </div>

                    <div class="form-group" id="message-template-footer-wrapper">
                        <label class="col-sm-2 control-label">Imap Accounts To Sync</label>
                        <div class="col-sm-8">
                            <ul>
                                <?php foreach ($accounts as $account) { ?>
                                <li><?=$account['username']?><input type="checkbox" name="settings[accounts][]" value="<?=$account['username']?>" <?=@in_array($account['username'], @$settings['sync_accounts']) ? 'checked="checked"' : ''?>/> </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" type="submit" name="action" value="save">Save</button>
    </form>
</div>
