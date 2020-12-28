/*
ts:2016-09-21 17:27:00
*/

INSERT IGNORE INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'service-expire-reminder',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'Service expire reminder',
  'accounts@ideabubble.ie',
  'Hi $contact_name,
We are contacting you to inform you that your service(s) with us below are due for renewal.

$service_type / $service_type / $date_end / $subtotal


If you wish to renew your services can you reply confirming you wish to renew these services before $remind_date.
If you wish to ensure payment for these services immediately please click below to send us payment and reference details for the above.
Please note : failure to reply to this email could result in all your web, email services going offline.

Thank you.
Idea Bubble Accounts Department',
  '0',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);