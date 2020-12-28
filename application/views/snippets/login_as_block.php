<?php
$currentLoggedInUser = Auth::instance()->get_user();
if(Session::instance()->get('auth_forced') && Session::instance()->get('login_as_return_id')):
    ?>
    <div style="text-align: center; width: 100%;">You logged in as <b><?= $currentLoggedInUser['email'] ?></b>. <a href='/admin/users/login_back'>Log back in as Super User</a></div>
<?php endif; ?>