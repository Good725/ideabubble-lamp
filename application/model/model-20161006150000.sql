/*
ts:2016-10-06 15:00:00
*/

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'view_website_frontend')
);

