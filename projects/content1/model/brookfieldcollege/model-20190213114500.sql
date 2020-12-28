/*
ts:2019-02-13 11:45:00
*/

UPDATE
  `engine_project_role`
SET
  `default_dashboard_id` = (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Welcome' AND `deleted` = 0 LIMIT 1)
WHERE
  `role` IN ('Super User', 'Administrator', 'Teacher', 'Manager')
;