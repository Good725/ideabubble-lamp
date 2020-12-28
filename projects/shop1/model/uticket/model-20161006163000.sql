/*
ts:2016-10-06 16:30:00
*/

INSERT IGNORE INTO
	`plugin_dashboards_sharing` (`dashboard_id`, `group_id`)
SELECT
	`dashboard`.`id`, `role`.`id`
FROM
	`plugin_dashboards` `dashboard`,
	`engine_project_role` `role`
WHERE
	`dashboard`.`title` NOT IN ('My Orders', 'My Sales')
AND
	`role`.`role` IN ('Administrator', 'Super User');