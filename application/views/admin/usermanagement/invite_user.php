<?php
    if (isset($alert)) {
        echo $alert;
    }
    $group_roles = [];
    $users_role = new Model_Roles(Auth::instance()->get_user()['role_id']);
    foreach ($users_role->get_all_roles() as $role) {
        if ($role['master_group'] <= $users_role->master_group) {
            $group_roles[$role['id']] = $role['role'];
        }
    }
    $modal_popup = $modal_popup ?? false;
?>
<?php
    if(isset($alert)){
    ?>
        <script>
            remove_popbox();
        </script>
    <?php
    }
    $form = new IbForm('invite-user', '/admin/usermanagement/invite_user');
    echo $form->start(['title' => false]);
    if ($modal_popup) {
        $modal = View::factory('/snippets/modal', ['id' => 'contact_email_invite_modal', 'class' => 'fade']);
        $modal->set('title', 'Invite user');
    } else {
        echo "<legend><h3>Invite User</h3></legend>";
    }
    ob_start();
    echo $form->textarea(__('Email(s)'), 'send-login-invite-email', '',
        ['rows' => 3, 'id' => 'contact-emails', 'class' => 'mb-0']);
    if (!$modal_popup) {
        echo "<div class='col-sm-2'>&nbsp;</div><p class='col-sm-10' style='font-size: 10px'>Separate multiple email addresses with a comma.<br/>Distribution lists
                            are not supported.</p>";
    }
    echo $form->combobox(__('Role') . '*', ($modal_popup) ? 'user_group_role_id' : 'role_id', $group_roles, null,
        ['id' => ($modal_popup) ? 'user_group_role_id' : 'role_id']);
    echo $form->wysiwyg(__('Message'), 'message', '', ['rows' => 5, 'id' => 'message']);
    echo "<div class='col-sm-2'>&nbsp;</div><p class='col-sm-10' style='font-size: 10px'>Use <i>@link@</i> in message to place registration link</p>";
    $body = ob_get_clean();
    ob_start();
    if ($modal_popup) {
        echo "<a href='#' id='contact-invite-user-submit' class='btn btn-primary' data-content='Send the user invitation email'>Invite user</a>";
    } else {
        echo "<button type='submit' name='action' value='save' class='btn btn-primary'>Invite User</button>";
    }
    echo $form->end();
    $footer = ob_get_clean();

    if ($modal_popup) {
        echo $modal->set('body', $body)->set('footer', $footer);
    } else {
        echo $body;
        echo $footer;
    }

?>

