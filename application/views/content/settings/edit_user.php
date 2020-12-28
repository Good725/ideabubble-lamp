<div class="col-sm-12">

<form class="form-horizontal col-sm-8" action="<?php echo URL::Site('admin/users/edit/' . $users['id']) ?>" method="post">

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

<fieldset>
    <legend>Login info</legend>
    <div class="form-group">
        <label for="email" class="col-sm-2 control-label">Email</label>

        <div class="col-sm-7">
            <input type="text" id="email" name="email" class="form-control" value="<?php echo $users['email']; ?>">

            <p class="help-block">This is the email address that the user uses to login to the CMS.</p>
        </div>
    </div>

    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">New Password</label>

        <div class="col-sm-7">
            <input type="password" id="password" name="password" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="mpassword" class="col-sm-2 control-label">Confirm Password</label>

        <div class="col-sm-7">
            <input type="password" id="mpassword" name="mpassword" class="form-control">

            <p class="help-block">Leave the password box blank if you don't want to change the password</p>
        </div>
    </div>

    <div class="form-group">
        <label for="role_id" class="col-sm-2 control-label">Role</label>

        <div class="col-sm-3">
            <select id="role_id" name="role_id" data-content="This is the role you will have on the CMS." data-original-title="Role" class="form-control popinit" rel="popover">
                <?php foreach ($users_roles as $role) { ?>
                    <option value="<?php echo $role['id'] ?>" <?php echo $role['id'] == $users['role_id'] ? 'selected="selected"' : '' ?>> <?php echo $role['role'] ?></option>
                <?php } ?>
            </select>
        </div>
		<div class="col-sm-4">
			<input type="text" class="form-control" name="role_other" value="<?php echo $users['role_other'] ?>" />
		</div>
    </div>

    <div class="form-group">
        <label for="can_login" class="col-sm-2 control-label">Allow to login?</label>

        <div class="col-sm-7">
            <div data-original-title="Allow to login?" rel="popover" data-content="Unchecking the box will disable this users ability to login." class="btn-group popinit" id="can_login">
				<?= html::toggle_button('can_login', __('Can log in'), __('Cannot log in'), ($users['can_login'] == 1)) ?>
            </div>
        </div>
	</div>
	<div class="form-group">
        <label for="email_verified" class="col-sm-2 control-label">Email Verified?</label>

        <div class="col-sm-7">
            <div data-original-title="Email Verified?" rel="popover" data-content="Toggling this will affect the users ability to login." class="btn-group popinit" id="can_login">
				<?= html::toggle_button('email_verified', __('Verified'), __('Not verified'), ($users['email_verified'])) ?>
            </div>
        </div>
    </div>
	<div class="form-group">
        <label for="trial_start_date" class="col-sm-2 control-label">Trial Start Date</label>

        <div class="col-sm-7">
            <div data-original-title="Trial Start Date" rel="popover" data-content="If this is set then the user will not be able to login after 30 days from that day" class="btn-group popinit" id="trial_start_date">
                <input type="text" class="form-control" name="trial_start_date" value="<?=$users['trial_start_date']?>" />
            </div>
        </div>
    </div>


</fieldset>
<fieldset>
    <legend>Personal Information:</legend>

    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">First Name</label>

        <div class="col-sm-7">
            <input type="text" id="name" name="name" class="form-control" value="<?php echo $users['name']; ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="surname" class="col-sm-2 control-label">Surname</label>

        <div class="col-sm-7">
            <input type="text" id="surname" name="surname" class="form-control" value="<?php echo $users['surname']; ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="address" class="col-sm-2 control-label">Address</label>

        <div class="col-sm-7">
            <textarea rows="3" id="address" name="address" class="form-control"><?php echo $users['address']; ?></textarea>
        </div>
    </div>

	<div class="form-group">
		<label for="edit-user-eircode" class="col-sm-2 control-label">Eircode</label>

		<div class="col-sm-7">
			<input type="text" id="edit-user-eircode" name="eircode" class="form-control" value="<?= $users['eircode'] ?>" />
		</div>
	</div>

    <div class="form-group">
        <label for="phone" class="col-sm-2 control-label">Phone no.</label>

        <div class="col-sm-7">
            <input type="telephone" id="phone" name="phone" class="form-control" value="<?php echo $users['phone']; ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="heard_from" class="col-sm-2 control-label">Heard From</label>

        <div class="col-sm-7">
            <input type="text" id="heard_from" name="heard_from" class="form-control" value="<?php echo $users['heard_from']; ?>">
        </div>
    </div>
    <!-- User Preferences -->
    <fieldset>
        <legend>User Preferences</legend>

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Default Home Page</label>

            <div class="col-sm-7">
                <input type="text" id="default_home_page" name="default_home_page" class="form-control" value="<?php echo $users['default_home_page']; ?>">
            </div>
        </div>
        <?php if (Kohana::$config->load('config')->get('daily_digest_enabled')): ?>
            <div class="form-group">
                <label for="daily_digest_email" class="col-sm-2 control-label">Enable Daily Digest Email?</label>

                <div class="col-sm-7">
                    <label class="checkbox">
                        <?php
                        $ddchecked = '';
                        $ddvalue = 0;
                        if (isset($users['daily_digest_email']) && $users['daily_digest_email'] != 0) {
                            $ddchecked = 'checked';
                            $ddvalue = 1;
                        }
                        ?>
                        <input type="checkbox" value="<?= $ddvalue ?>" id="daily_digest_email" name="daily_digest_email" <?= $ddchecked ?>>
                        Check this box to enable the Daily Digest Email for the users account email.
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('#daily_digest_email').change(
                                    function () {
                                        if ($('#daily_digest_email').is(':checked')) {
                                            $('#daily_digest_email').val('1');
                                        }
                                        else {
                                            $('#daily_digest_email').val('0');
                                        }
                                    }
                                );
                            });
                        </script>
                    </label>
                </div>
            </div>
        <?php endif; ?>
    </fieldset>

    <?php if (isset($discounts)): ?>
        <legend>Shop Options:</legend>
        <div class="form-group">
            <label for="discount_format_id" class="col-sm-2 control-label">Discount</label>

            <div class="col-sm-7">
                <select id="discount_format_id" name="discount_format_id" data-content="This is the personal discount for the user." data-original-title="Discount" class="form-control popinit" rel="popover">
                    <option value="">None</option>
                    <?php foreach ($discounts as $discount) { ?>
                        <option value="<?php echo $discount['id'] ?>" <?php echo $discount['id'] == $users['discount_format_id'] ? 'selected="selected"' : '' ?>> <?php echo $discount['title'] . ' Code: ' . $discount['code'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label">Credit Account</div>

            <div class="col-sm-7">
                <?php $credit_account = (isset($users['credit_account']) AND $users['credit_account'] == 1); ?>
				<?= html::toggle_button('credit_account', __('Yes'), __('No'), $credit_account) ?>
            </div>
        </div>

    <?php endif; ?>

    <div class="form-actions">
        <?php if (Auth::instance()->has_access('settings_users_edit_delete_btn')): ?>
            <a class="btn" data-toggle="modal" href="#delete_modal">Delete</a>
        <?php endif; ?>

        <a class="btn" href="<?php echo URL::Site("admin/" . $current_controller . "/" . $current_action . "/" . $users['id']); ?>">Cancel</a>
        <button class="btn btn-primary" type="submit">Save Changes</button>
    </div>
</fieldset>

<!--
   ===================================
   Modal Box for comment adding.
   id: comment_modal
   ===================================
   -->

<div class="modal fade" id="delete_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>

				<h3>Delete User?</h3>
			</div>
			<div class="modal-body">
				<p>Warning: This cannot be undone.</p>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" type="submit" id="delete_submit" name="delete" value="delete">Delete</button>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>


</form>

<div class="col-sm-4">
    <h3>User Info</h3>
    <table class='table table-striped table-bordered table-condensed'>
        <tbody>
            <tr>
                <td>
                    <strong>User ID</strong>
                </td>
                <td>
                    <?php echo $users['id']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Date Created</strong>
                </td>
                <td>
                    <?php echo date('jS M Y', strtotime($users['registered'])); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Last Login</strong>
                </td>
                <td>
                    <?php echo ($users['last_login'] != '' && settype($users['last_login'], "integer"))
                        ? date('jS M Y \a\t H:i', $users['last_login']) : "Never logged in"; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Number of Logins</strong>
                </td>
                <td>
                    <?= $users['logins']; ?>
                </td>
            </tr>
        </tbody>
    </table>
	
	<?php if(isset($messages) && count($messages) > 0){ ?>
	<br />
	<div class="form-group">
        <h3>Messages</h3>
        <div class="col-sm-10">
		<table class="table table-striped dataTable contact_messages_table">
			<thead>
				<tr>
					<th scope="col">Type</th>
					<th scope="col">From</th>
					<th scope="col">Subject</th>
					<th scope="col">Status</th>
					<th scope="col">Last Activity</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($messages as $message){ ?>
					<tr data-id="<?= $message['id']; ?>">
						<td><?= $message['driver']; ?></td>
						<td><?= $message['sender']; ?></td>
						<td><a target="_blank" href="/admin/messaging/details?message_id=<?=$message['id']?>"><?= htmlentities($message['subject']); ?></a></td>
						<td><?= $message['status']; ?></td>
						<td><?= IbHelpers::relative_time(max(strtotime($message['date_created']), strtotime($message['date_updated']))); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		</div>
	</div>
	<?php } ?>
</div>

</div>
