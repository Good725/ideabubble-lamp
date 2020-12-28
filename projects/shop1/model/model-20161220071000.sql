/*
ts:2016-12-20 07:10:00
*/

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
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

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_help')
);

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_messages')
);