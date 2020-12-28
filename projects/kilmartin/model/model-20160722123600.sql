/*
ts:2016-07-22 12:36:00
*/

-- my total roll calls
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Total Roll Calls',
		`summary` = '',
		`sql` = 'SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Roll Calls</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'My Roll Calls\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM (SELECT COUNT(*) AS `count`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= e.datetime_start AND \'{!DASHBOARD-TO!}\' >= e.datetime_start AND u.id = \'{!me!}\'\n) AS `counter`',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @my_rollcalls_total_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_total_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT @my_rollcalls_total_report_id, id FROM engine_project_role WHERE `role` IN ('Teacher', 'Super User'));
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'My Total Roll Calls',
		`report_id` = @my_rollcalls_total_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;

-- my total schedules
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Total Schedules',
		`summary` = '',
		`sql` = 'SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Schedules</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'My Schedules\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM (SELECT count(*) AS `count`\n	FROM\n		(SELECT DISTINCT DATE_FORMAT(e.datetime_start, \'%d/%m/%Y\') AS `day`, s.id, s.name as `schedule`\n			FROM plugin_contacts3_contacts ca\n				INNER JOIN plugin_contacts3_contact_has_notifications n \n					ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n				INNER JOIN engine_users u ON n.`value` = u.email\n				INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE \'{!DASHBOARD-FROM!}\' <= e.datetime_start AND \'{!DASHBOARD-TO!}\' >= e.datetime_start AND u.id = \'{!me!}\'\n			ORDER BY e.datetime_start ASC) sc\n) AS `counter`',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @my_schedules_total_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_total_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT @my_schedules_total_report_id, id FROM engine_project_role WHERE `role` IN ('Teacher', 'Super User'));
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'My Total Schedules',
		`report_id` = @my_schedules_total_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;

-- my total attendance
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Total Attendance',
		`summary` = '',
		`sql` = 'SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Attendance</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'My Attendance\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM (SELECT count(*) AS `count`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= e.datetime_start AND \'{!DASHBOARD-TO!}\' >= e.datetime_start AND u.id = \'{!me!}\'\n) AS `counter`',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @my_attendance_total_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_total_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'My Total Attendance',
		`report_id` = @my_attendance_total_report_id,
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
  (SELECT @my_attendance_total_report_id, id FROM engine_project_role WHERE `role` IN ('Teacher', 'Super User'));
