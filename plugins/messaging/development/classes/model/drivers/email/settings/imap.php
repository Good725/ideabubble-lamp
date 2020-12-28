<div id="list_accounts_wrapper">
    <form method="post">
        <table id="list_accounts_table" class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Host</th>
                <th scope="col">Username</th>
                <th scope="col">Port</th>
                <th scope="col">Security</th>
                <th scope="col">Protocol</th>
                <th scope="col">Sync Period(Mins)</th>
                <th scope="col">Last Sync</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($settings as $i => $account){ ?>
                <tr data-id="<?=$account['id']?>">
                    <td><?=$account['host']?></td>
                    <td><?=$account['username']?></td>
                    <td><?=$account['port']?></td>
                    <td><?=$account['security']?></td>
                    <td><?=$account['use_pop3'] ? 'Pop3' : 'Imap'?></td>
                    <td><?=$account['auto_sync_minutes']?></td>
                    <td><?=$account['last_synced']?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle btn-actions" type="button" data-toggle="dropdown">
                                <?= __('Actions') ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="sync" href="/admin/messaging/receive_sync/email-imap?id=<?=$account['id']?>"><span class="icon-thumps-up"></span> <?= __('Sync Now') ?></a>
                                </li>
                                <li>
                                    <a class="delete">
                                        <span class="icon-ban-circle"></span> <?= __('Delete') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr><th colspan="8"><button type="button" class="btn add-account" name="add">New</button></th></tr>
            </tfoot>
        </table>
    </form>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="imap-account-edit-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="imap-account-form" name="imap-account-form" method="post">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <h4 class="modal-title"><?= __('Account Details') ?></h4>
                </div>
                <div class="modal-body">

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-username">Username</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="imap-username"  type="text" name="username" value="" placeholder="Username" />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-password">Password</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="imap-password"  type="text" name="password" value="" placeholder="Password" />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-host">Host</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="imap-host"  type="text" name="host" value="" placeholder="Host" />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-port">Port</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="imap-port"  type="text" name="port" value="" placeholder="Port" />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-security">Security</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="imap-security" name="security">
                                <?=html::optionsFromArray(array('' => 'None', 'SSL' => 'SSL', 'TLS' => 'TLS'), '')?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-username">Protocol</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="imap-use_pop3" name="use_pop3">
                                <?=html::optionsFromArray(array('0' => 'Imap', '1' => 'Pop3'), 0)?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="imap-auto_sync_minutes">Sync Period(Mins)</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="imap-auto_sync_minutes"  type="text" name="auto_sync_minutes" value="10" placeholder="Sync Period(Mins)" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="well text-center">
                        <button type="submit" id="account-save" name="action" value="save" class="btn btn-primary continue-button"><?= __('Save') ?></button>
                        <button type="button" id="account-test" name="action" value="test" class="btn btn-primary continue-button"><?= __('Test Credentials') ?></button>
                        <a class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="imap-account-delete-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="id" value="" />
                <input type="hidden" name="deleted" value="1" />
                <div class="modal-header">
                    <h4 class="modal-title"><?= __('Account Delete') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?=__('Do you want to delete')?></p>

                </div>
                <div class="modal-footer">
                    <div class="well text-center">
                        <button type="submit" id="account-save" name="action" value="save" class="btn btn-primary continue-button"><?= __('Delete') ?></button>
                        <a class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(".btn.add-account").on("click", function(){
    display_editor();
});

$("a.delete").on("click", function(){
    var id = $(this).parents("tr").data("id");
    display_delete(id);
});

function display_editor()
{
    $("#imap-account-edit-modal input[type=text]").val("");
    $("#imap-account-edit-modal").modal();
}

function display_delete(id)
{
    $("#imap-account-delete-modal input[name=id]").val(id);
    $("#imap-account-delete-modal").modal();
}

$("#account-test").on("click", function(){
    var data = $("#imap-account-form").serialize();
    data += "&action=test";
    $.post(
        "/admin/messaging/custom_settings/email-imap",
        data,
        function (response) {
            if (response.success) {
                alert("Successfully connected");
            } else {
                alert("Not connected");
            }
        }
    );
})
</script>