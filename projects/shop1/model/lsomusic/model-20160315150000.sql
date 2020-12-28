/*
ts:2017-03-15 15:00:00
*/

-- INSERT "IGNORE" in case these have been manually set

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
);


INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_messages')
);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_messages')
);

-- total active schedules
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Total Active Schedules',
		`summary` = '',
		`sql` = "SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Active Schedules</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Active Schedules\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `count` FROM (\n			SELECT DISTINCT s.id AS `schedule_id`, s.trainer_id\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) s\n) AS `counter`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @lsm216_active_schedules_total_report_id;

INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Active Schedules',
		`report_id` = @lsm216_active_schedules_total_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;

INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Active Schedules'), id FROM engine_project_role WHERE `role` IN ('Administrator', 'Super User'));


UPDATE `plugin_reports_reports`
  SET `sql` =	"SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Active Schedules</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `count` FROM (\n			SELECT DISTINCT s.id AS `schedule_id`, s.trainer_id\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) s\n) AS `counter`"
	WHERE `name` = 'Total Active Schedules';

UPDATE `plugin_reports_sparklines`
SET `background_color` = 'rgb(240, 240, 240)'
WHERE `title` = 'Total Active Schedules';