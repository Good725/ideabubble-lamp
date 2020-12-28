/*
ts:2019-03-27 17:00:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `overwrite_cms_message`, `date_created`, `created_by`)
VALUES (
  'host_application_admin',
  'Email sent to the administration when someone makes an application to be a host',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Host Application Submitted',
  '0',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `overwrite_cms_message`, `date_created`, `created_by`)
VALUES (
  'host_application_applicant',
  'Email sent to the person making the application to be a host',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Host Application Request Received',
  '<p>Hello $first_name</p>

  <p>Thank you for applying to be a host. We are currently processing your request.</p>

  <p>Regards</p>
  ',
  '1',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);