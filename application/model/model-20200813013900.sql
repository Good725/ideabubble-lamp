/*
ts:2020-08-13 01:39:00
*/

INSERT IGNORE INTO
    `plugin_messaging_notification_templates` (
    `name`,
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
VALUES ( 'reset_cms_password',
           'EMAIL',
           (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
           'Password Reset Confirmation',
           'noreply@websitecms.ie',
           '<p>Hi, $first_name ,</p><p>Your username is $email</p>
           <p>To initiate the password reset process for your account, please</p>
           <p><a href="$base_urladmin/login/reset_password_form/$validation">click here</a><br>
           If clicking the link above does not work, please copy and paste the full URL below into a new browser window</p>
            <p><a href="$base_urladmin/login/reset_password_form/$validation">$base_urladmin/login/reset_password_form/$validation</a></p>
           <p>If you have received this email in error, it is likely that another user entered your email address by mistake, while trying to reset a password.
          If you did not initiate the request, you do not need to take any further action and can safely disregard his email.
           If you experience any difficulties during this process, please contact us</p>',
           '1',
           CURRENT_TIMESTAMP,
           (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
           CURRENT_TIMESTAMP,
           '1',
           '0',
           '$base_url, $email, $first_name, $last_name, $validation',
           (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Auth' LIMIT 1)
       );

UPDATE `plugin_messaging_notification_templates` SET `message`  =
    '<p>Hi, $first_name ,</p><p>Your username is $email</p>
           <p>To initiate the password reset process for your account, please</p>
           <p><a href="$base_urladmin/login/reset_password_form/$validation">click here</a><br>
           If clicking the link above does not work, please copy and paste the full URL below into a new browser window</p>
            <p><a href="$base_urladmin/login/reset_password_form/$validation">$base_urladmin/login/reset_password_form/$validation</a></p>
           <p>If you have received this email in error, it is likely that another user entered your email address by mistake, while trying to reset a password.
          If you did not initiate the request, you do not need to take any further action and can safely disregard his email.
           If you experience any difficulties during this process, please contact us</p>',
   `usable_parameters_in_template` = '$base_url, $email, $first_name, $last_name, $validation'
   WHERE `name` = 'reset_cms_password';