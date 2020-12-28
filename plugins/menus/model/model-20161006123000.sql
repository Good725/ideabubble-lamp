/*
ts:2016-10-06 12:30:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'menus', 'Menus', 'Menus');
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'menus')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'menus')
);