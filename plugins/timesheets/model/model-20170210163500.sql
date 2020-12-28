/*
ts:2017-02-10 16:35:00
*/
INSERT IGNORE INTO `engine_project_role` (`role`, `publish`, `deleted`) VALUES ('Teacher', '1', '0');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'timesheets_index', 'Timesheets Index', 'Timesheets Index');

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_index')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Teacher'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_index')
);
