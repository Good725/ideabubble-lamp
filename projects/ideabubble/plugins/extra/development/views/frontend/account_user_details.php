<div class="confirmation_message"></div>
<h2>Your User Details</h2>
<form class="service-form" action="frontend/extra/update_user" method="post" id="contact_user_form">
    <input type="hidden" name="user_id" value="<?= $customer['user_id'] ?>" />

    <fieldset class="form-block">
        <legend>Your User Details</legend>

        <div>
            <label class="form-label" for="user_details_first_name">First Name</label>
            <input id="user_details_first_name" type="text" name="user_first_name" value="<?= $user['name'] ?>" />
        </div>

        <div>
            <label class="form-label" for="user_details_last_name">Last Name</label>
            <input id="user_details_last_name" type="text" name="user_last_name" value="<?= $user['surname'] ?>" />
        </div>

        <div>
            <label class="form-label" for="user_details_phone">Phone</label>
            <input id="user_details_phone" type="text" name="user_phone" value="<?= $user['phone'] ?>" />
        </div>

        <div>
            <label class="form-label" for="user_details_email">Email</label>
            <input id="user_details_email" type="text" name="user_email" value="<?= $user['email'] ?>" readonly="readonly"/>
        </div>
    </fieldset>
	
	<fieldset class="form-block">
        <legend>Password</legend>

        <div>
            <label class="form-label" for="user_password_new">New Password</label>
            <input autocomplete="off" id="user_password_new" class="validate[equals[user_password_new_confirm]]" type="password" name="user_password_new" value=""/>
        </div>
		<div>
            <label class="form-label" for="user_password_new_confirm">Confirm New Password</label>
            <input autocomplete="off" id="user_password_new_confirm" class="validate[equals[user_password_new]]" type="password" name="user_password_new_confirm" value=""/>
			 <br /><span>(* leave empty to not change)</span>
        </div>
    </fieldset>
	<br clear="all" />
    <button type="submit" name="update_user" id="update_user">Save</button>
</form>
<script>
    $("#contact_user_form").on("submit", function(){
        var valid = $(this).validationEngine().validationEngine('validate');
        return valid;
    });
</script>
