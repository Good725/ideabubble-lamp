/*
ts:2020-07-28 15:01:00
*/

DELETE FROM `engine_role_permissions`
WHERE `role_id`     = (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator' LIMIT 1)
AND   `resource_id` = (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'timetables_view_planner' LIMIT 1);
