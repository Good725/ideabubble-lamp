/*
ts:2020-07-30 16:00:00
*/

-- Email messaging template for when someone submits a request for a GDPR data cleanse.
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `overwrite_cms_message`, `date_created`, `date_updated`, `created_by`)
VALUES (
  'gdpr_request_cleanse_admin',
  'Email notification sent to the administration when someone requests a GDPR data cleanse',
  'DASHBOARD',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Request for data cleanse from $company_name',
  '<p>A request to have data cleansed has been submitted for the following user:</p>
\n
\n<p>
\nName: $name<br />
\nEmail: $email<br />
\nContact ID: $contact_id<br />
\n</p>
  ',
  '$company, $contact_id, $email, $name, $user_id',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  1
);

UPDATE
  `plugin_messaging_notification_templates`
SET
  `driver`       = 'EMAIL',
  `date_updated` = CURRENT_TIMESTAMP,
  `message`      = '<p>A request to have data cleansed has been submitted for the following user:</p>
\n
\n<p>
\nName: $name<br />
\nEmail: <a href="mailto:$email">$email</a><br />
\nContact ID: $contact_id<br />
\n</p>
\n',
  `usable_parameters_in_template` = '$company_name, $contact_id, $email, $name, $user_id'
WHERE
  `name` = 'gdpr_request_cleanse_admin';


-- Allow data cleanse actions to be tracked
INSERT INTO `engine_activities_actions` (`stub`, `name`) VALUES ('request_data_cleanse', 'Request data cleanse');
INSERT INTO `engine_activities_item_types` (`stub`, `name`, `table_name`) VALUES ('contact3', 'Contact', 'plugin_contacts3_contacts');

-- Permissions
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES
('0', 'gdpr', 'GDPR', '');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  ('1', 'gdpr_request_cleanse', 'GDPR / Request cleanse', 'Allow the user to request to have their data cleansed.', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'gdpr')),
  ('1', 'gdpr_download_data',   'GDPR / Download data',   'Allow the user to download their data.',                 (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'gdpr')),
  ('1', 'gdpr_delete_data',     'GDPR / Delete data',     'Allow the user to delete their data.',                   (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'gdpr'));

-- Assign the "download data" permission
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `id`, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'gdpr_download_data' LIMIT 1)
  FROM `engine_project_role`
  WHERE `role` IN ('Super user', 'Administrator', 'External user', 'Teacher', 'Student', 'Parent/Guardian', 'Org rep');

-- Assign the "delete data" permission
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `id`, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'gdpr_delete_data' LIMIT 1)
  FROM `engine_project_role`
  WHERE `role` IN ('Super user', 'Administrator', 'External user', 'Teacher', 'Student', 'Parent/Guardian', 'Org rep');

