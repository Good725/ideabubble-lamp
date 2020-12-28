/*
ts:2020-08-25 13:00:00
*/
INSERT INTO
  `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `overwrite_cms_message`, `date_created`, `date_updated`, `created_by`)
VALUES (
  'login-invitation-email',
  'Email sent to a contact when the "send login invitation" button is clicked.',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'User registration',
  '',
  '@first_name@, @link@',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IF(count(*) = 0, 1, `id`) FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);

DELIMITER ;;
UPDATE
  `plugin_messaging_notification_templates`
SET
  `description` = 'Email sent to a contact when the "send login invitation" button is clicked.',
  `usable_parameters_in_template` = '@first_name@, @link@, @theme_color@',
  `message` = '<p>Hello @first_name@,</p>
\n
\n<p>You have been invited to join our new platform. Please use the option below to accept.</p>
\n
\n<p><a href="@link@" style="background: #0074cc;
\n        background: $theme_color;
\n        border-radius: 3px;
\n        color: #fff;
\n        cursor: pointer;
\n        display: inline-block;
\n        min-width: 4em;
\n        padding: .75em 1.5em;
\n        text-align: center;
\n        text-decoration: none;
\n    ">Accept invitation</a></p>
\n
\n<p>Thank you.</p>'
WHERE
  `name` = 'login-invitation-email'
;;