/*
ts:2020-08-10 19:04:00
*/

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
WHERE `engine_project_role`.`role` IN ('Super User') AND `engine_resources`.`alias` = 'user_auth_2step_sms';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
WHERE `engine_project_role`.`role` IN ('Super User') AND `engine_resources`.`alias` = 'user_auth_2step_email';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
WHERE `engine_project_role`.`role` IN ('Administrator') AND `engine_resources`.`alias` = 'user_auth_2step_sms';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
WHERE `engine_project_role`.`role` IN ('Administrator') AND `engine_resources`.`alias` = 'user_auth_2step_email';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
WHERE `engine_project_role`.`role` IN ('Student') AND `engine_resources`.`alias` = 'user_auth_2step_sms';