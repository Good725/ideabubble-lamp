/*
ts:2019-08-13 12:31:00
*/

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator' LIMIT 1),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'courses_schedule_content_tab' LIMIT 1)
),
(
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator' LIMIT 1),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'assessments_content_tab' LIMIT 1)
);

