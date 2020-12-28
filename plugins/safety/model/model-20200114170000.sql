/*
ts:2020-01-14 17:00:00
*/

INSERT INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
 'Accident reporter',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'accident_reporter',
 'Controller_Frontend_Accidents,embed_form'
);

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `overwrite_cms_message`, `date_created`, `date_updated`, `created_by`)
VALUES (
  'accident_reported_admin',
  'Email sent to the administration when someone reports an accident',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Accident reported',
  '<p>An accident has been reported; <strong>$title</strong>.</p>
\n
\n<p><strong>Reporter</strong>: $first_name $last_name<br />
\n<strong>Email</strong>: $email<br />
\n<strong>Phone</strong>: $mobile</p>
  ',
  '$email, $first_name, $last_name, $mobile, $title',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);;

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `overwrite_cms_message`, `date_created`, `date_updated`, `created_by`)
VALUES (
  'accident_reported_user',
  'Email sent to the reporter of an accident',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Thank you for your report',
  '<p>Hello $first_name</p>

  <p>Thank you for reporting an accident. We are currently processing your request.</p>

  <p>Regards</p>',
  '$email, $first_name, $last_name, $mobile, $title',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);;