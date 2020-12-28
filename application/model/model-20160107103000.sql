/*
ts:2016-01-07 10:30:00
*/

INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`)
  VALUES ('0', 'user', 'User');
INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'user_profile', 'User / View / Edit Profile', `id` FROM resources where alias = 'user';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `resources`.`id` FROM `engine_project_role` JOIN `resources`
  WHERE `engine_project_role`.`role` IN ('Super User') AND `resources`.`alias` = 'user_profile';
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `resources`.`id` FROM `engine_project_role` JOIN `resources`
  WHERE `engine_project_role`.`role` IN ('Super User') AND `resources`.`alias` = 'user';
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `resources`.`id` FROM `engine_project_role` JOIN `resources`
  WHERE `engine_project_role`.`role` IN ('Administrator') AND `resources`.`alias` = 'user_profile';
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `resources`.`id` FROM `engine_project_role` JOIN `resources`
  WHERE `engine_project_role`.`role` IN ('Administrator') AND `resources`.`alias` = 'user';

INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'my_activities', 'User / Activities', `id` FROM resources where alias = 'user';
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `resources`.`id` FROM `engine_project_role` JOIN `resources`
  WHERE `engine_project_role`.`role` IN ('Super User') AND `resources`.`alias` = 'my_activities';
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `resources`.`id` FROM `engine_project_role` JOIN `resources`
  WHERE `engine_project_role`.`role` IN ('Administrator') AND `resources`.`alias` = 'my_activities';