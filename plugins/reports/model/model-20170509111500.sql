/*
ts:2017-05-09 11:15:00
*/

INSERT IGNORE INTO `engine_role_permissions` (role_id, resource_id) VALUES (
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'  LIMIT 1),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'reports_delete' LIMIT 1)
);
