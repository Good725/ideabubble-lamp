/*
ts:2020-07-30 16:01:00
*/

-- Set the sender for the GDPR data cleanse request email
UPDATE `plugin_messaging_notification_templates` SET `sender` = 'training@ibec.ie' WHERE `name` = 'gdpr_request_cleanse_admin';

-- Create a user to use as a recipient, if they do not already exist.
INSERT IGNORE INTO `engine_users` (`role_id`, `email`, `password`, `name`, `registered`, `email_verified`, `can_login`) VALUES (
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User' LIMIT 1),
  'dataprivacyoffice@ibec.ie',
  '!',
  'Data privacy office',
  CURRENT_TIMESTAMP,
  '0',
  '0'
);

-- Set the recipient for the GDPR data cleanse request email
INSERT IGNORE INTO `plugin_messaging_notification_template_targets` (`template_id`, `target_type`, `target`, `x_details`, `date_created`) VALUES (
  (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'gdpr_request_cleanse_admin' LIMIT 1),
  'CMS_USER',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'dataprivacyoffice@ibec.ie'),
  'to',
  CURRENT_TIMESTAMP
);


-- Assign the "GDPR request cleanse" permission.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `id`, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'gdpr_request_cleanse' LIMIT 1)
  FROM `engine_project_role`
  WHERE `role` IN ('Super user', 'Administrator', 'External user', 'Teacher', 'Student', 'Parent/Guardian', 'Org rep');

-- Remove the "download data" and "delete data" permission from select roles.
DELETE FROM `engine_role_permissions`
WHERE `role_id`     IN (SELECT `id` FROM `engine_project_role` WHERE `role` IN ('External user', 'Teacher', 'Student', 'Parent/Guardian', 'Org rep'))
AND   `resource_id` IN (SELECT `id` FROM `engine_resources`    WHERE `alias` IN ('gdpr_download_data', 'gdpr_delete_data'));