/*
ts:2016-07-13 12:25:00
*/

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('My Roll Calls', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @my_rollcalls_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @my_rollcalls_id, id FROM engine_project_role WHERE `role` IN ('Teacher', 'Super User'));

-- my roll calls by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Roll Calls By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Roll Calls By Month',
		`summary` = '',
		`sql` = "SELECT DATE_FORMAT(e.datetime_start, '%m/%Y') AS `month`, COUNT(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY `month`\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_rollcalls_month_widget_id;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_month_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_rollcalls_id, @my_rollcalls_month_report_id, 1, 1, null, 1, 0);

-- my roll calls by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Roll Calls By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Roll Calls By Day',
		`summary` = '',
		`sql` = "SELECT DATE_FORMAT(e.datetime_start, '%d/%m/%Y') AS `day`, COUNT(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY `day`\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_rollcalls_day_widget_id;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_day_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_rollcalls_id, @my_rollcalls_day_report_id, 1, 2, null, 1, 0);

-- my roll calls by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Roll Calls By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Roll Calls By Course',
		`summary` = '',
		`sql` = "SELECT co.title AS `course`, COUNT(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY co.id\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_rollcalls_course_widget_id;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_course_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_rollcalls_id, @my_rollcalls_course_report_id, 1, 3, null, 1, 0);

-- my roll calls by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Roll Calls By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Roll Calls By Location',
		`summary` = '',
		`sql` = "SELECT IFNULL(pl.`name`, l.`name`) AS `location`, COUNT(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY `location`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_rollcalls_location_widget_id;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_location_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_rollcalls_id, @my_rollcalls_location_report_id, 1, 1, null, 1, 0);

-- my roll calls by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Roll Calls By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Roll Calls By Room',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS('/',pl.`name`, l.`name`) AS `room`, COUNT(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY `room`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_rollcalls_room_widget_id;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_room_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_rollcalls_id, @my_rollcalls_room_report_id, 1, 2, null, 1, 0);

-- my roll calls by student
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Roll Calls By Student',
		`type` = 2,
		`x_axis` = 'schedule',
		`y_axis` = 'students',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_student_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Roll Calls By Student',
		`summary` = '',
		`sql` = "SELECT `schedule`, count(*) AS `students`\n	FROM (SELECT DISTINCT s.id, s.`name` AS `schedule`, b.contact_id\n					FROM plugin_contacts3_contacts ca\n						INNER JOIN plugin_contacts3_contact_has_notifications n \n							ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n						INNER JOIN engine_users u ON n.`value` = u.email\n						INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n						INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n						INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND bs.deleted = 0\n						INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0\n					WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}') sc\n	GROUP BY `schedule`;\n",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_rollcalls_student_widget_id;
SELECT LAST_INSERT_ID() INTO @my_rollcalls_student_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_student_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_student_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_rollcalls_student_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_rollcalls_id, @my_rollcalls_student_report_id, 1, 3, null, 1, 0);


INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('My Schedules', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @my_schedules_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @my_schedules_id, id FROM engine_project_role WHERE `role` IN ('Teacher', 'Super User'));

-- my schedules by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_schedules_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Month',
		`summary` = '',
		`sql` = "SELECT `month`, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT DATE_FORMAT(e.datetime_start, '%m/%Y') AS `month`, s.id, s.name as `schedule`\n			FROM plugin_contacts3_contacts ca\n				INNER JOIN plugin_contacts3_contact_has_notifications n \n					ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n				INNER JOIN engine_users u ON n.`value` = u.email\n				INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n			ORDER BY e.datetime_start ASC) sc\n	GROUP BY `month`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_schedules_month_widget_id;
SELECT LAST_INSERT_ID() INTO @my_schedules_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_month_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_schedules_id, @my_schedules_month_report_id, 1, 1, null, 1, 0);

-- my schedules by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_schedules_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Day',
		`summary` = '',
		`sql` = "SELECT `day`, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT DATE_FORMAT(e.datetime_start, '%d/%m/%Y') AS `day`, s.id, s.name as `schedule`\n			FROM plugin_contacts3_contacts ca\n				INNER JOIN plugin_contacts3_contact_has_notifications n \n					ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n				INNER JOIN engine_users u ON n.`value` = u.email\n				INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n			ORDER BY e.datetime_start ASC) sc\n	GROUP BY `day`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_schedules_day_widget_id;
SELECT LAST_INSERT_ID() INTO @my_schedules_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_day_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_schedules_id, @my_schedules_day_report_id, 1, 2, null, 1, 0);

-- my schedules by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_schedules_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Course',
		`summary` = '',
		`sql` = "SELECT course, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT co.title AS `course`, s.id, s.name AS `schedule`\n			FROM plugin_contacts3_contacts ca\n				INNER JOIN plugin_contacts3_contact_has_notifications n \n					ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n				INNER JOIN engine_users u ON n.`value` = u.email\n				INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}') sc\n	GROUP BY course\n	ORDER BY course",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_schedules_course_widget_id;
SELECT LAST_INSERT_ID() INTO @my_schedules_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_course_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_schedules_id, @my_schedules_course_report_id, 1, 3, null, 1, 0);

-- my schedules by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_schedules_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Location',
		`summary` = '',
		`sql` = "SELECT location, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT IFNULL(pl.name, l.name) AS `location`, s.id, s.name AS `schedule`\n			FROM plugin_contacts3_contacts ca\n				INNER JOIN plugin_contacts3_contact_has_notifications n \n					ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n				INNER JOIN engine_users u ON n.`value` = u.email\n				INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}') sc\n	GROUP BY location\n	ORDER BY location;",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_schedules_location_widget_id;
SELECT LAST_INSERT_ID() INTO @my_schedules_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_location_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_schedules_id, @my_schedules_location_report_id, 1, 1, null, 1, 0);

-- my schedules by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_schedules_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Room',
		`summary` = '',
		`sql` = "SELECT room, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT CONCAT_WS('/', pl.name, l.name) AS `room`, s.id, s.name AS `schedule`\n			FROM plugin_contacts3_contacts ca\n				INNER JOIN plugin_contacts3_contact_has_notifications n \n					ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n				INNER JOIN engine_users u ON n.`value` = u.email\n				INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}') sc\n	GROUP BY room\n	ORDER BY room",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_schedules_room_widget_id;
SELECT LAST_INSERT_ID() INTO @my_schedules_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_room_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_schedules_id, @my_schedules_room_report_id, 1, 2, null, 1, 0);

-- my schedules by student
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules By Student',
		`type` = 2,
		`x_axis` = 'schedule',
		`y_axis` = 'students',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_schedules_student_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Student',
		`summary` = '',
		`sql` = "SELECT `schedule`, count(*) AS `students`\n	FROM (SELECT DISTINCT s.id, s.`name` AS `schedule`, b.contact_id\n					FROM plugin_contacts3_contacts ca\n						INNER JOIN plugin_contacts3_contact_has_notifications n \n							ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n						INNER JOIN engine_users u ON n.`value` = u.email\n						INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n						INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n						INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND bs.deleted = 0\n						INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0\n					WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}') sc\n	GROUP BY `schedule`;\n",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_schedules_student_widget_id;
SELECT LAST_INSERT_ID() INTO @my_schedules_student_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_student_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_student_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_schedules_student_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_schedules_id, @my_schedules_student_report_id, 1, 3, null, 1, 0);


INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('My Attendance', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @my_attendance_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @my_attendance_id, id FROM engine_project_role WHERE `role` IN ('Teacher', 'Super User'));

-- my attendance by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Attendance By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_attendance_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Schedules By Month',
		`summary` = '',
		`sql` = "SELECT DATE_FORMAT(e.datetime_start, '%m/%Y') AS `month`, count(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY `month`\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_attendance_month_widget_id;
SELECT LAST_INSERT_ID() INTO @my_attendance_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_month_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_attendance_id, @my_attendance_month_report_id, 1, 1, null, 1, 0);

-- my attendance by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Attendance By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_attendance_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Attendance By Day',
		`summary` = '',
		`sql` = "SELECT DATE_FORMAT(e.datetime_start, '%d/%m/%Y') AS `day`, count(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY `day`\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_attendance_day_widget_id;
SELECT LAST_INSERT_ID() INTO @my_attendance_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_day_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_attendance_id, @my_attendance_day_report_id, 1, 2, null, 1, 0);

-- my attendance by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Attendance By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_attendance_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Attendance By Course',
		`summary` = '',
		`sql` = "SELECT co.title AS `course`, count(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY course\n	ORDER BY course",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_attendance_course_widget_id;
SELECT LAST_INSERT_ID() INTO @my_attendance_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_course_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_attendance_id, @my_attendance_course_report_id, 1, 3, null, 1, 0);

-- my attendance by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Attendance By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_attendance_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Attendance By Location',
		`summary` = '',
		`sql` = "SELECT IFNULL(pl.name, l.name) AS `location`, count(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY location\n	ORDER BY location",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_attendance_location_widget_id;
SELECT LAST_INSERT_ID() INTO @my_attendance_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_location_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_attendance_id, @my_attendance_location_report_id, 1, 1, null, 1, 0);

-- my attendance by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Attendance By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_attendance_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Attendance By Room',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS('/', pl.name, l.name) AS `room`, count(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY room\n	ORDER BY room",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_attendance_room_widget_id;
SELECT LAST_INSERT_ID() INTO @my_attendance_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_room_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_attendance_id, @my_attendance_room_report_id, 1, 2, null, 1, 0);

-- my attendance by student
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Attendance By Student',
		`type` = 2,
		`x_axis` = 'attendance',
		`y_axis` = 'students',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @my_attendance_student_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'My Attendance By Student',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS('/', student.first_name, student.last_name) AS `student`, count(*) AS `qty`\n	FROM plugin_contacts3_contacts ca\n		INNER JOIN plugin_contacts3_contact_has_notifications n \n			ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n		INNER JOIN engine_users u ON n.`value` = u.email\n		INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n		INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_contacts3_contacts student ON b.contact_id = student.id\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start AND u.id = '{!me!}'\n	GROUP BY b.contact_id\n	ORDER BY b.contact_id",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @my_attendance_student_widget_id;
SELECT LAST_INSERT_ID() INTO @my_attendance_student_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_student_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_student_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @my_attendance_student_report_id, `type` = 'user_id', `name` = 'me', `value` = 'logged', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@my_attendance_id, @my_attendance_student_report_id, 1, 3, null, 1, 0);
