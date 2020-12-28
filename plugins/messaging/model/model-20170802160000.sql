/*
ts:2017-08-02 16:00:00
*/

INSERT INTO
  `plugin_messaging_notification_templates` (
    `name`,
    `description`,
    `driver`,
    `type_id`,
    `subject`,
    `sender`,
    `message`,
    `overwrite_cms_message`,
    `date_created`,
    `created_by`,
    `date_updated`,
    `publish`,
    `deleted`,
    `usable_parameters_in_template`,
    `category_id`
  )
VALUES (
  'new_user_no_password',
  'This is sent to users, when an account is created without setting up a password. The owner of the email can endorse the account creation by clicking the given URL and setting a password.',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'New account created',
  'noreply@websitecms.ie',
  '<p>Hello $first_name $last_name.</p>
\n\n<p>An account has been created for you at <a href=\"$base_url\">$base_url</a>, with $email as a username.</p>
\n\n<p>Please use the link below to set a password for this account.</p>
\n\n<p><a href=\"$base_url/admin/login/reset_password_form/$validation_code\">$base_url/admin/login/reset_password_form/$validation_code</a></p>
\n\n<p>If the above link has expired, you can use the \"forgot password\" form to send a new password reset link.</p>
\n\n<p><a href=\"$base_url/admin/login/forgot_password\">$base_url/admin/login/forgot_password/</a></p>
\n\n<p>If you did not endorse the creation of this account, you can ignore this e-mail.</p> ',
  '1',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0',
  '$base_url, $email, $first_name, $last_name, $validation_code',
  (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Website' LIMIT 1)
);
