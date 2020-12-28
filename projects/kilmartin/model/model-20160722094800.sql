/*
ts:2016-07-22 09:48:00
*/

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Attendance', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @attendance_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @attendance_id, id FROM engine_project_role WHERE `role` IN ('Manager', 'Super User'));

-- my attendance by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Attendance By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @attendance_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Attendance By Month',
		`summary` = '',
		`sql` = "SELECT DATE_FORMAT(e.datetime_start, '%m/%Y') AS `month`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY `month`\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @attendance_month_widget_id;
SELECT LAST_INSERT_ID() INTO @attendance_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@attendance_id, @attendance_month_report_id, 1, 1, null, 1, 0);

-- my attendance by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Attendance By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @attendance_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Attendance By Day',
		`summary` = '',
		`sql` = "SELECT DATE_FORMAT(e.datetime_start, '%d/%m/%Y') AS `day`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY `day`\n	ORDER BY e.datetime_start ASC",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @attendance_day_widget_id;
SELECT LAST_INSERT_ID() INTO @attendance_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@attendance_id, @attendance_day_report_id, 1, 2, null, 1, 0);

-- my attendance by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Attendance By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @attendance_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Attendance By Course',
		`summary` = '',
		`sql` = "SELECT co.title AS `course`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY course\n	ORDER BY course",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @attendance_course_widget_id;
SELECT LAST_INSERT_ID() INTO @attendance_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@attendance_id, @attendance_course_report_id, 1, 3, null, 1, 0);

-- my attendance by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Attendance By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @attendance_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Attendance By Location',
		`summary` = '',
		`sql` = "SELECT IFNULL(pl.name, l.name) AS `location`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY location\n	ORDER BY location",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @attendance_location_widget_id;
SELECT LAST_INSERT_ID() INTO @attendance_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@attendance_id, @attendance_location_report_id, 1, 1, null, 1, 0);

-- my attendance by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Attendance By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @attendance_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Attendance By Room',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS('/', pl.name, l.name) AS `room`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY room\n	ORDER BY room",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @attendance_room_widget_id;
SELECT LAST_INSERT_ID() INTO @attendance_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@attendance_id, @attendance_room_report_id, 1, 2, null, 1, 0);

-- my attendance by student
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Attendance By Student',
		`type` = 2,
		`x_axis` = 'attendance',
		`y_axis` = 'students',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @attendance_student_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Attendance By Student',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS('/', student.first_name, student.last_name) AS `student`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_contacts3_contacts student ON b.contact_id = student.id\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY b.contact_id\n	ORDER BY b.contact_id",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @attendance_student_widget_id;
SELECT LAST_INSERT_ID() INTO @attendance_student_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_student_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @attendance_student_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@attendance_id, @attendance_student_report_id, 1, 3, null, 1, 0);

-- total attendance
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Total Attendance',
		`summary` = '',
		`sql` = 'SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Attendance</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Attendance\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `count`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n) AS `counter`',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @attendance_total_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Attendance',
		`report_id` = @attendance_total_report_id,
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
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Attendance'), id FROM engine_project_role WHERE `role` IN ('Manager', 'Super User'));

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Bookings', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @bookings_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @bookings_id, id FROM engine_project_role WHERE `role` IN ('Manager', 'Super User'));

-- bookings by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Bookings By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @bookings_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Bookings By Month',
		`summary` = '',
		`sql` = 'SELECT DATE_FORMAT(b.created_date, \'%m/%Y\') AS `month`, count(*) AS `qty`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `month`\n	ORDER BY b.created_date ASC\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @bookings_month_widget_id;
SELECT LAST_INSERT_ID() INTO @bookings_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@bookings_id, @bookings_month_report_id, 1, 1, null, 1, 0);

-- bookings by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Bookings By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @bookings_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Bookings By Day',
		`summary` = '',
		`sql` = 'SELECT DATE_FORMAT(b.created_date, \'%d/%m/%Y\') AS `day`, count(*) AS `qty`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `day`\n	ORDER BY b.created_date ASC\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @bookings_day_widget_id;
SELECT LAST_INSERT_ID() INTO @bookings_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@bookings_id, @bookings_day_report_id, 1, 2, null, 1, 0);

 -- bookings by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Bookings By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @bookings_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Bookings By Course',
		`summary` = '',
		`sql` = 'SELECT co.title AS `course`, count(*) AS `qty`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `course`\n	ORDER BY b.created_date ASC\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @bookings_course_widget_id;
SELECT LAST_INSERT_ID() INTO @bookings_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@bookings_id, @bookings_course_report_id, 1, 3, null, 1, 0);

 -- bookings by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Bookings By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @bookings_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Bookings By Location',
		`summary` = '',
		`sql` = 'SELECT IF(pl.name, pl.name, l.name) AS `location`, count(*) AS `qty`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `location`\n	ORDER BY b.created_date ASC\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @bookings_location_widget_id;
SELECT LAST_INSERT_ID() INTO @bookings_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@bookings_id, @bookings_location_report_id, 1, 1, null, 1, 0);

 -- bookings by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Bookings By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @bookings_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Bookings By Room',
		`summary` = '',
		`sql` = 'SELECT CONCAT_WS(\' \', pl.name, l.name) AS `room`, count(*) AS `qty`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `room`\n	ORDER BY b.created_date ASC\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @bookings_room_widget_id;
SELECT LAST_INSERT_ID() INTO @bookings_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@bookings_id, @bookings_room_report_id, 1, 2, null, 1, 0);


 -- bookings by teacher
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Bookings By Teacher',
		`type` = 2,
		`x_axis` = 'teacher',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @bookings_teacher_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Bookings By Teacher',
		`summary` = '',
		`sql` = 'SELECT CONCAT_WS(\' \', teachers.first_name, teachers.last_name) AS `teacher`, count(*) AS `qty`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `teacher`\n	ORDER BY `teacher` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @bookings_teacher_widget_id;
SELECT LAST_INSERT_ID() INTO @bookings_teacher_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @bookings_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@bookings_id, @bookings_teacher_report_id, 1, 3, null, 1, 0);


   -- total bookings
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Total Bookings',
		`summary` = '',
		`sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Bookings</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Bookings\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `count`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	\n\n) AS `counter`',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @total_bookings_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Bookings',
		`report_id` = @total_bookings_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = '',
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Bookings'), id FROM engine_project_role WHERE `role` IN ('Manager', 'Super User'));

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Revenue', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @revenue_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @revenue_id, id FROM engine_project_role WHERE `role` IN ('Manager', 'Super User'));

-- revenue by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Revenue By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'revenue',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @revenue_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Revenue By Month',
		`summary` = '',
		`sql` = 'SELECT \n		DATE_FORMAT(pay.created, \'%m/%Y\') AS `month`, \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `month`\n	ORDER BY `month` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @revenue_month_widget_id;
SELECT LAST_INSERT_ID() INTO @revenue_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@revenue_id, @revenue_month_report_id, 1, 1, null, 1, 0);

-- revenue by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Revenue By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'revenue',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @revenue_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Revenue By Day',
		`summary` = '',
		`sql` = 'SELECT \n		DATE_FORMAT(pay.created, \'%d/%m/%Y\') AS `day`, \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `day`\n	ORDER BY `day` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @revenue_day_widget_id;
SELECT LAST_INSERT_ID() INTO @revenue_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@revenue_id, @revenue_day_report_id, 1, 2, null, 1, 0);

 -- revenue by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Revenue By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'revenue',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @revenue_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Revenue By Course',
		`summary` = '',
		`sql` = 'SELECT \n		co.title AS `course`, \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `course`\n	ORDER BY `course` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @revenue_course_widget_id;
SELECT LAST_INSERT_ID() INTO @revenue_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@revenue_id, @revenue_course_report_id, 1, 3, null, 1, 0);

 -- revenue by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Revenue By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'revenue',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @revenue_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Revenue By Location',
		`summary` = '',
		`sql` = 'SELECT \n		IF(pl.name, pl.name, l.name) AS `location`, \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `location`\n	ORDER BY `location` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @revenue_location_widget_id;
SELECT LAST_INSERT_ID() INTO @revenue_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@revenue_id, @revenue_location_report_id, 1, 1, null, 1, 0);

 -- revenue by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Revenue By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'revenue',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @revenue_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Revenue By Room',
		`summary` = '',
		`sql` = 'SELECT \n		CONCAT_WS(\' \', pl.name, l.name) AS `room`, \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `room`\n	ORDER BY `room` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @revenue_room_widget_id;
SELECT LAST_INSERT_ID() INTO @revenue_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@revenue_id, @revenue_room_report_id, 1, 2, null, 1, 0);


 -- revenue by teacher
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Revenue By Teacher',
		`type` = 2,
		`x_axis` = 'teacher',
		`y_axis` = 'revenue',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @revenue_teacher_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Revenue By Teacher',
		`summary` = '',
		`sql` = 'SELECT \n		CONCAT_WS(\' \', teachers.first_name, teachers.last_name) AS `teacher`, \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n	GROUP BY `teacher`\n	ORDER BY `teacher` ASC;\n',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @revenue_teacher_widget_id;
SELECT LAST_INSERT_ID() INTO @revenue_teacher_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @revenue_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@revenue_id, @revenue_teacher_report_id, 1, 3, null, 1, 0);


   -- total revenue
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Total Revenue',
		`summary` = '',
		`sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">\', \n		`revenue`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Revenue\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM (SELECT \n		SUM(IFNULL(pay.amount * IF(tt.credit, 1, -1) * IF(ps.credit, 1, -1), 0)) AS `revenue`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ths ON s.id = ths.schedule_id AND ths.deleted = 0\n		INNER JOIN plugin_bookings_transactions tx ON b.booking_id = tx.booking_id AND ths.transaction_id = tx.id AND tx.deleted = 0\n		INNER JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n		INNER JOIN plugin_bookings_transactions_payments pay ON tx.id = pay.transaction_id AND pay.deleted = 0\n		INNER JOIN plugin_bookings_transactions_payments_statuses ps ON pay.`status` = ps.id\n		\n	WHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date\n) AS `revenue`',
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @total_revenue_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Revenue',
		`report_id` = @total_revenue_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = '',
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Revenue'), id FROM engine_project_role WHERE `role` IN ('Manager', 'Super User'));
