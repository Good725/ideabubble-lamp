/*
ts:2018-02-13 16:18:00
*/

INSERT INTO engine_resources
  (type_id, `alias`, name, parent_controller, description)
  VALUES
  (0, 'api', 'API', 0, 'API Access');

INSERT INTO engine_resources
  (type_id, `alias`, name, parent_controller, description)
  (select 1, 'api_login', 'API / Login', id, 'API Login' from engine_resources where alias='api');

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'api_login')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'api_login')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'api_login')
);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Student'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'api_login')
);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Mature Student'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'api_login')
);
