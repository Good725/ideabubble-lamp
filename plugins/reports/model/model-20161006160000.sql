/*
ts:2016-10-06 16:00:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'reports', 'Reports', 'Reports');

SELECT `id` INTO @t_2016_10_06_reports_resource_id FROM `engine_resources` o where o.`alias` = 'reports';

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'reports_edit', 'Reports / Edit', 'Reports Edit', @t_2016_10_06_reports_resource_id);

SELECT `id` INTO @i_2016_10_06_reports_edit_resource_id FROM `engine_resources` o where o.`alias` = 'reports_edit';


INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator' LIMIT 1),
  @t_2016_10_06_reports_resource_id
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User' LIMIT 1),
  @t_2016_10_06_reports_resource_id
);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator' LIMIT 1),
  @i_2016_10_06_reports_edit_resource_id
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User' LIMIT 1),
  @i_2016_10_06_reports_edit_resource_id
);
