<div class="col-sm-12">

    <form class="form-horizontal" action="<?php echo URL::Site('admin/users/add_user/') ?>" method="post">

        <?php // This is needed to display any error that might be loaded into the messages queue ?>
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
                    <input type="text" id="email" name="email" data-content="This is the email address the user will use to log into the CMS." rel="popover" data-original-title="E-Mail" class="form-control popinit" rel="popover" value="">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>

                <div class="col-sm-7">
                    <input type="password" id="password" name="password" data-content="This password must be at least eight characters long." rel="popover" data-original-title="Password" class="form-control popinit" value="">
                </div>
            </div>

            <div class="form-group">
                <label for="mpassword" class="col-sm-2 control-label">Confirm Password</label>

                <div class="col-sm-7">
                    <input type="password" id="mpassword" name="mpassword" data-content="Please enter the users passwod again." rel="popover" data-original-title="Confirm Password" class="form-control popinit">
                </div>
            </div>

            <div class="form-group">
                <label for="role_id" class="col-sm-2 control-label">Role</label>

                <div class="col-sm-7">
                    <select id="role_id" name="role_id" data-content="This is the role the user will have on the CMS." rel="popover" data-original-title="Role" class="form-control popinit" rel="popover">
                        <?php
                        // print the groups available to the user here.
                        foreach ($users_roles as $role) {
                            echo '<option value="' . $role['id'] . '" >';
                            echo $role['role'];
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="can_login" class="col-sm-2 control-label">Allow to login?</label>

                <div class="col-sm-7">
                    <div data-original-title="Allow to login?" rel="popover" data-content="Unchecking the box will disable this users ability to login." class="btn-group popinit" id="can_login">
						<?= html::toggle_button('can_login', 'Can log in', 'Cannot log in', TRUE) ?>
                    </div>
                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>Personal Information:</legend>

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">First Name</label>

                <div class="col-sm-7">
                    <input type="text" id="name" name="name" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="surname" class="col-sm-2 control-label">Surname</label>

                <div class="col-sm-7">
                    <input type="text" id="surname" name="surname" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="col-sm-2 control-label">Address</label>

                <div class="col-sm-7">
                    <textarea rows="3" id="address" name="address" class="form-control"></textarea>
                </div>
            </div>

			<div class="form-group">
				<label for="edit-user-eircode" class="col-sm-2 control-label">Eircode</label>

				<div class="col-sm-7">
					<input type="text" id="edit-user-eircode" name="eircode" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="phone" class="col-sm-2 control-label">Phone no.</label>

				<div class="col-sm-7">
					<input type="telephone" id="phone" name="phone" class="form-control">
				</div>
			</div>

            <?php if (isset($discounts)): ?>
                <legend>Shop Options:</legend>
                <div class="form-group">
                    <label for="discount_format_id" class="col-sm-2 control-label">Discount</label>

                    <div class="col-sm-7">
                        <select id="discount_format_id" name="discount_format_id" data-content="This is the personal discount for the user." rel="popover" data-original-title="Discount" class="form-control popinit" rel="popover">
                            <option value="" selected>None</option>
                            <?php foreach ($discounts as $discount) { ?>
                                <option value="<?php echo $discount['id'] ?>"> <?php echo $discount['title'] . ' Code: ' . $discount['code'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">Credit Account</div>

                    <div class="col-sm-7">
						<?= html::toggle_button('credit_account', __('On'), __('Off'), FALSE) ?>
                    </div>
                </div>

            <?php endif; ?>
            <!-- User Preferences -->
            <fieldset>
                <legend>User Preferences</legend>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="edit_profile_default_home_page">Default Home Page</label>

                    <div class="col-sm-7">
                        <input type="input" class="form-control" id="edit_profile_default_home_page" name="default_home_page"/>
                    </div>
                </div>
            </fieldset>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Add User</button>
            </div>
        </fieldset>

    </form>

</div>
