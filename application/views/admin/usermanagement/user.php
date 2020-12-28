<?php
if (isset($alert))
{
    echo $alert;
}
?>
<?php
    if(isset($alert)){
    ?>
        <script>
            remove_popbox();
        </script>
    <?php
    }
?>

<?php if (Auth::instance()->has_access('user_edit')): ?>
    <div class="form-group">
        <div class="col-sm-12 form-action-group">
            <?php if (Auth::instance()->has_access('login_as')): ?>
                <a href="/admin/users/login_as?user_id=<?= $user['id'] ?>" class="login-as-user btn btn-default" data-user-id="'<?= $user['id'] ?>">login as</a>
            <?php endif; ?>
            <a class="btn btn-default edit" data-toggle="modal" data-target="#user-edit-modal">Edit</a>
            <a class="btn btn-default reset">Reset Password</a>


            <?php if ($user['deleted'] == 0): ?>
                <a class="btn btn-default delete">Deactivate</a>
            <?php else: ?>
                <a class="btn btn-default undelete">Activate</a>
            <?php endif; ?>

            <?php if ($user['password'] == '!'): ?>
                <a class="btn btn-default reinvite" data-toggle="modal" data-target="#invite-user-modal">Resend Invite</a>
            <?php endif; ?>

            <?php if ($user['email_verified'] == 0): ?>
                <a data-user_id="<?=$user['id']?>" class="btn btn-default verify">Verify</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>


<h3><?=$user['name'] ? $user['name'] . ' ' . $user['surname'] : $user['email']?></h3>
<div class="col-sm-6">
    <p><label>Email address : </label><?=$user['email']?></p>
    <p><label>User name : </label><?=$user['name'] . ' ' . $user['surname']?></p>
    <p><label>Group : </label><?=$user['role']['role']?></p>
    <p><label>Register Source : </label><?=$user['register_source']?></p>
</div>
<div class="col-sm-6">
    <h4>User Stats</h4>
    <p><label>User ID : </label><?=$user['id']?></p>
    <p><label>Date Created : </label> <?=$user['registered']?></p>
    <p><label>Last Login : </label> <?=$user['last_login'] ? date('Y-m-d H:i:s', $user['last_login']) : 'Never'?></p>
    <p><label>Number Of logins : </label><?=$user['logins']?></p>
</div>

<div id="user-edit-modal" class="modal fade">
    <form id="user-edit" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3><span>Edit</span> User</h3>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-2"><label class="control-label" for="email">Email *</label></div>
                        <div class="col-sm-10"><input type="text" class="form-input" name="email" id="email" value="<?=@$user['email']?>" placeholder="email" /></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2"><label class="control-label" for="first_name">First Name</label></div>
                        <div class="col-sm-10"><input type="text" class="form-input" name="name" id="first_name" value="<?=@$user['name']?>" placeholder="First Name" /></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2"><label class="control-label" for="last_name">Last Name</label></div>
                        <div class="col-sm-10"><input type="text" class="form-input" name="surname" id="last_name" value="<?=@$user['surname']?>" placeholder="Last Name" /></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2"><label class="control-label" for="role_id">Group Id</label></div>
                        <div class="col-sm-10">
                            <select class="form-input" name="role_id" id="role_id">
                                <?php foreach ($roles as $role) { ?>
                                    <option value="<?=$role['id']?>" <?=$user['role']['id'] == $role['id'] ? 'selected="selected"' : ''?>><?=$role['role']?></option>
                                <?php } ?>
                            </select>
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

<div id="invite-user-modal" class="modal fade">
    <form id="invite-user" name="invite-user" method="post" action="/admin/usermanagement/invite_user">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3><span>Edit</span> User</h3>
                </div>
                <div class="modal-body form-horizontal">
                    <input type="hidden" name="resend" value="<?=@$user['id']?>" />
                    <fieldset>
                        <legend><h3>Resend Invite</h3></legend>
                        <p><?=('Fill in the information below to create new users. We\'ll email them instructions for logging in.')?></p>
                        <div class="form-group">
                            <div class="col-sm-2"><label class="control-label" for="message">Invite Message *</label></div>
                            <div class="col-sm-10">
                                <textarea class="form-input" name="message" id="message" placeholder="Message"></textarea><br />
                                <p style="font-size: 10px">Use <i>@link@</i> in message to place registration link</p>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="resend" class="btn">Send</button>
                    <a data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

<form id="verify-form" method="post" action="/admin/usermanagement/verify_user">
    <input type="hidden" name="user_id" value="" />

</form>
<script>
$(".btn.reset").on("click", function(){
    if (confirm("Are you sure you want to reset password?")) {
        $.post(
            '/admin/users/ajax_send_password_reset',
            {user_id : <?=$user['id']?>},
            function (response) {
                if (response) {
                    alert("Password reset email has been sent.");
                } else {
                    alert("Unknown error");
                }
            }
        )
    }
});
$(".btn.delete").on("click", function(){
    if (confirm("Are you sure you want to deactivate this user?")) {
        $.post(
            '/admin/usermanagement/user_delete',
            {id : <?=$user['id']?>},
            function (response) {
                if (response) {
                    //alert("User has been deactivated");
                    window.location.href = '/admin/usermanagement/users';
                } else {
                    alert("Unknown error");
                }
            }
        )
    }
});
$(".btn.undelete").on("click", function(){
    if (confirm("Are you sure you want to activate this user?")) {
        $.post(
            '/admin/usermanagement/user_undelete',
            {id : <?=$user['id']?>},
            function (response) {
                if (response) {
                    //alert("User has been activated");
                    window.location.href = '/admin/usermanagement/users';
                } else {
                    alert("Unknown error");
                }
            }
        )
    }
});
$(".btn.verify").on("click", function(){
    $("#verify-form [name=user_id]").val($(this).data("user_id"));
    $("#verify-form").submit();
    return false;
});
</script>