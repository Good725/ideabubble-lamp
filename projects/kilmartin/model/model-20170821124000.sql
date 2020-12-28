/*
ts:2017-08-21 12:40:00
*/


UPDATE `plugin_messaging_notification_templates`
 SET `message`='<p>Hello $first_name $last_name.</p> \n\n<p>An account has been created for you at <a href=\"$base_url\">$base_url</a>, by $primary_name with $email as a username.</p> \n\n<p>Please use the link below to set a password for this account.</p> \n\n<p><a href=\"$base_url/admin/login/reset_password_form/$validation_code\">$base_url/admin/login/reset_password_form/$validation_code</a></p> \n\n<p>If the above link has expired, you can use the \"forgot password\" form to send a new password reset link.</p> \n\n<p><a href=\"$base_url/admin/login/forgot_password\">$base_url/admin/login/forgot_password/</a></p> \n\n<p>If you did not endorse the creation of this account, you can ignore this e-mail.</p> '
 WHERE `name`= 'new_user_no_password';

UPDATE `plugin_messaging_notification_templates` 
 SET `usable_parameters_in_template`='$base_url, $email, $first_name, $last_name, $validation_code,$primary_name' WHERE `name`= 'new_user_no_password';

