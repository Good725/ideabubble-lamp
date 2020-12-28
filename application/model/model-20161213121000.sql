/*
ts:2016-12-13 12:10:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`) VALUES ('0', 'user_tools_messages', 'User Tools Messages');
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`) VALUES ('0', 'user_tools_help', 'User Tools Help');

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_messages')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_help')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_messages')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_help')
);

