/*
ts:2019-09-13 11:07:00
*/

UPDATE `engine_project_role`
SET `default_dashboard_id` = (select id
                              from plugin_dashboards
                              where `title` = 'Welcome'
                              order by `date_created` desc
                              limit 1)
WHERE `role` = 'Administrator' AND (`default_dashboard_id` = 0 or `default_dashboard_id` = -1 or `default_dashboard_id` IS NULL);

UPDATE `engine_users`
SET `default_dashboard_id` = null
where `default_dashboard_id` = -1;