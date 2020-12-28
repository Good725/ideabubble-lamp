/*
ts:2017-09-06 10:00:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`) VALUES (
  'default_email_sender',
  'Default Email Sender',
  'messaging',
  'testing@websitecms.ie',
  'testing@websitecms.ie',
  'testing@websitecms.ie',
  'testing@websitecms.ie',
  'testing@websitecms.ie',
  'Email address used when users who do not have the messaging_per_user_inbox permission send an email.',
  'text',
  'Messaging'
);

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_access_own_mail',    'Messaging / Access Own Mail',     `id` FROM `engine_resources` WHERE alias = 'messaging';

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_access_system_mail', 'Messaging / Access System Mail',  `id` FROM `engine_resources` WHERE alias = 'messaging';

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_access_others_mail', "Messaging / Access Others' Mail", `id` FROM `engine_resources` WHERE alias = 'messaging';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id`
  FROM   `engine_project_role`
  JOIN   `engine_resources`
  WHERE  `engine_project_role`.`role` = 'Super User'
  AND    `engine_resources`.`alias` IN ('messaging_access_others_mail', 'messaging_access_own_mail', 'messaging_access_system_mail');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id`
  FROM   `engine_project_role`
  JOIN   `engine_resources`
  WHERE  `engine_project_role`.`role` = 'Administrator'
  AND    `engine_resources`.`alias` IN ('messaging_access_system_mail');


INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_access_drafts',    'Messaging / Access Drafts',     `id` FROM `engine_resources` WHERE alias = 'messaging';


INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id`
  FROM   `engine_project_role`
  JOIN   `engine_resources`
  WHERE  `engine_project_role`.`role` = 'Super User'
  AND    `engine_resources`.`alias` IN ('messaging_access_drafts');
