/*
ts:2016-07-22 14:29:00
*/

UPDATE `plugin_reports_widgets`
  SET `name` = 'Attendance By Teacher', `y_axis` = 'teacher'
  WHERE `name` = 'Attendance By Student' AND `delete` = 0;

UPDATE `plugin_reports_reports`
  SET
    `name` = 'Attendance By Teacher',
		`sql` = "SELECT CONCAT_WS(\' \', teachers.first_name, teachers.last_name) AS `teacher`, count(*) AS `qty`\n	FROM plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n	GROUP BY teachers.id\n	ORDER BY teacher"
	WHERE `name` = 'Attendance By Student' AND `delete` = 0;

INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Attendance'), id FROM engine_project_role WHERE `role` IN ('Receptionist', 'Administrator'));

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT (select id from plugin_dashboards where `title` = 'Attendance'), id FROM engine_project_role WHERE `role` IN ('Receptionist', 'Administrator'));


INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Availability', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @availability_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @availability_id, id FROM engine_project_role WHERE `role` IN ('Receptionist', 'Administrator'));

 -- my availability by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Availability By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'available',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @availability_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Availability By Month',
		`summary` = '',
		`sql` = "SELECT sa.`month`, sum(IFNULL(s.max_capacity - s.booked, 0)) AS `available`\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`, DATE_FORMAT(e.datetime_start, '%m/%Y') AS `month`, DATE_FORMAT(e.datetime_start, '%Y/%m') AS `dt`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n	GROUP BY `month`\n	ORDER BY sa.`dt`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @availability_month_widget_id;
SELECT LAST_INSERT_ID() INTO @availability_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@availability_id, @availability_month_report_id, 1, 1, null, 1, 0);

-- my availability by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Availability By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'available',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @availability_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Availability By Day',
		`summary` = '',
		`sql` = "SELECT sa.`day`, sum(IFNULL(s.max_capacity - s.booked, 0)) AS `available`\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`, DATE_FORMAT(e.datetime_start, '%d/%m/%Y') AS `day`, DATE_FORMAT(e.datetime_start, '%Y/%m/%d') AS `dt`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n	GROUP BY `day`\n	ORDER BY sa.`dt`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @availability_day_widget_id;
SELECT LAST_INSERT_ID() INTO @availability_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@availability_id, @availability_day_report_id, 1, 2, null, 1, 0);

-- my availability by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Availability By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'available',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @availability_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Availability By Course',
		`summary` = '',
		`sql` = "SELECT co.title AS `course`, sum(IFNULL(s.max_capacity - s.booked, 0)) AS `available`\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n		INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n	GROUP BY course\n	ORDER BY course",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @availability_course_widget_id;
SELECT LAST_INSERT_ID() INTO @availability_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@availability_id, @availability_course_report_id, 1, 3, null, 1, 0);

-- my availability by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Availability By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'available',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @availability_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Availability By Location',
		`summary` = '',
		`sql` = "SELECT IFNULL(pl.name, l.name) AS `location`, sum(IFNULL(s.max_capacity - s.booked, 0)) AS `available`\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	GROUP BY location\n	ORDER BY location",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @availability_location_widget_id;
SELECT LAST_INSERT_ID() INTO @availability_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@availability_id, @availability_location_report_id, 1, 1, null, 1, 0);

-- my availability by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Availability By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'available',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @availability_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Availability By Room',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS('/', pl.name, l.name) AS `room`, sum(IFNULL(s.max_capacity - s.booked, 0)) AS `available`\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n		INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	GROUP BY room\n	ORDER BY room",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @availability_room_widget_id;
SELECT LAST_INSERT_ID() INTO @availability_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@availability_id, @availability_room_report_id, 1, 2, null, 1, 0);

-- my availability by teacher
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Availability By Teacher',
		`type` = 2,
		`x_axis` = 'teacher',
		`y_axis` = 'available',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @availability_teacher_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Availability By Teacher',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS(' ', teachers.first_name, teachers.last_name) AS `teacher`, sum(IFNULL(s.max_capacity - s.booked, 0)) AS `available`\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n		INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n	GROUP BY teachers.id\n	ORDER BY teacher\n",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @availability_teacher_widget_id;
SELECT LAST_INSERT_ID() INTO @availability_teacher_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @availability_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@availability_id, @availability_teacher_report_id, 1, 3, null, 1, 0);

-- total availability
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Total Availability',
		`summary` = '',
		`sql` = "SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Availability</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Availability\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT sum(IFNULL(s.max_capacity - s.booked, 0)) AS count\n	FROM \n		(SELECT s.id, s.`delete`, s.max_capacity, s.trainer_id, s.course_id, s.location_id, count(*) AS `booked`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND s.`delete` = 0 AND bs.deleted = 0\n				INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status = 2\n				GROUP BY s.id\n		) s\n		INNER JOIN (\n			SELECT DISTINCT s.id AS `schedule_id`\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) sa ON s.id = sa.schedule_id\n) AS `counter`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @availability_total_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Availability',
		`report_id` = @availability_total_report_id,
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
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Availability'), id FROM engine_project_role WHERE `role` IN ('Receptionist', 'Administrator', 'Super User'));

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Active Schedules', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @active_schedules_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @active_schedules_id, id FROM engine_project_role WHERE `role` IN ('Receptionist', 'Administrator', 'Super User'));

-- active schedules by month
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Active Schedules By Month',
		`type` = 2,
		`x_axis` = 'month',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @active_schedules_month_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Active Schedules By Month',
		`summary` = '',
		`sql` = "SELECT `month`, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT DATE_FORMAT(e.datetime_start, '%m/%Y') AS `month`, s.id, s.name as `schedule`\n			FROM plugin_courses_schedules_events e \n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` AND e.`delete` = 0 = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n			ORDER BY e.datetime_start ASC) sc\n	GROUP BY `month`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @active_schedules_month_widget_id;
SELECT LAST_INSERT_ID() INTO @active_schedules_month_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_month_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_month_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@active_schedules_id, @active_schedules_month_report_id, 1, 1, null, 1, 0);

-- Active Schedules by day
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Active Schedules By Day',
		`type` = 2,
		`x_axis` = 'day',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @active_schedules_day_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Active Schedules By Day',
		`summary` = '',
		`sql` = "SELECT `day`, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT DATE_FORMAT(e.datetime_start, '%d/%m/%Y') AS `day`, s.id, s.name as `schedule`\n			FROM plugin_courses_schedules_events e \n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` AND e.`delete` = 0 = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n			ORDER BY e.datetime_start ASC) sc\n	GROUP BY `day`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @active_schedules_day_widget_id;
SELECT LAST_INSERT_ID() INTO @active_schedules_day_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_day_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_day_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@active_schedules_id, @active_schedules_day_report_id, 1, 2, null, 1, 0);

-- Active Schedules by course
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Active Schedules By Course',
		`type` = 2,
		`x_axis` = 'course',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @active_schedules_course_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Active Schedules By Course',
		`summary` = '',
		`sql` = "SELECT course, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT co.title AS `course`, s.id, s.name AS `schedule`\n			FROM plugin_courses_schedules_events e \n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` AND e.`delete` = 0 = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start) sc\n	GROUP BY course\n	ORDER BY course",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @active_schedules_course_widget_id;
SELECT LAST_INSERT_ID() INTO @active_schedules_course_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_course_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_course_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@active_schedules_id, @active_schedules_course_report_id, 1, 3, null, 1, 0);

-- Active Schedules by location
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Active Schedules By Location',
		`type` = 3,
		`x_axis` = 'location',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @active_schedules_location_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Active Schedules By Location',
		`summary` = '',
		`sql` = "SELECT location, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT IFNULL(pl.name, l.name) AS `location`, s.id, s.name AS `schedule`\n			FROM plugin_courses_schedules_events e \n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` AND e.`delete` = 0 = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start) sc\n	GROUP BY location\n	ORDER BY location;",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @active_schedules_location_widget_id;
SELECT LAST_INSERT_ID() INTO @active_schedules_location_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_location_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_location_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@active_schedules_id, @active_schedules_location_report_id, 1, 1, null, 1, 0);

-- Active Schedules by room
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Active Schedules By Room',
		`type` = 3,
		`x_axis` = 'room',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @active_schedules_room_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Active Schedules By Room',
		`summary` = '',
		`sql` = "SELECT room, count(*) AS `qty`\n	FROM\n		(SELECT DISTINCT CONCAT_WS('/', pl.name, l.name) AS `room`, s.id, s.name AS `schedule`\n			FROM plugin_courses_schedules_events e \n				INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` AND e.`delete` = 0 = 0\n				INNER JOIN plugin_courses_courses co ON s.course_id = co.id\n				INNER JOIN plugin_courses_locations l ON s.location_id = l.id\n				LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start) sc\n	GROUP BY room\n	ORDER BY room",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @active_schedules_room_widget_id;
SELECT LAST_INSERT_ID() INTO @active_schedules_room_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_room_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_room_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@active_schedules_id, @active_schedules_room_report_id, 1, 2, null, 1, 0);

-- Active Schedules by Teacher
INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Active Schedules By Teacher',
		`type` = 2,
		`x_axis` = 'teacher',
		`y_axis` = 'qty',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @active_schedules_teacher_widget_id;
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Active Schedules By Teacher',
		`summary` = '',
		`sql` = "SELECT CONCAT_WS(' ', teachers.first_name, teachers.last_name) AS `teacher`, count(*) AS qty FROM (\n			SELECT DISTINCT s.id AS `schedule_id`, s.trainer_id\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) s\n	INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id\n	GROUP BY s.trainer_id\n	ORDER BY `teacher`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type` = 'sql',
		`widget_id` = @active_schedules_teacher_widget_id;
SELECT LAST_INSERT_ID() INTO @active_schedules_teacher_report_id;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_reports_parameters
	SET `report_id` = @active_schedules_teacher_report_id, `type` = 'date', `name` = 'DASHBOARD-TO', `value` = '', `delete` = 0, is_multiselect = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@active_schedules_id, @active_schedules_teacher_report_id, 1, 3, null, 1, 0);


-- total active schedules
INSERT INTO `plugin_reports_reports`
	SET
		`name` = 'Total Active Schedules',
		`summary` = '',
		`sql` = "SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Availability</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Availability\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `count` FROM (\n			SELECT DISTINCT s.id AS `schedule_id`, s.trainer_id\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) s\n) AS `counter`",
		`category` = 0,
		`sub_category` = 0,
		`dashboard` = 1,
		`date_created` = NOW(),
		`date_modified` = NOW(),
		`publish` = 1,
		`delete` = 0,
		`report_type`='sql';
SELECT LAST_INSERT_ID()  INTO @active_schedules_total_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Active Schedules',
		`report_id` = @active_schedules_total_report_id,
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
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Active Schedules'), id FROM engine_project_role WHERE `role` IN ('Recipient', 'Administrator', 'Super User'));

DELETE FROM plugin_reports_report_sharing WHERE report_id in (select id from plugin_reports_reports where `name` = 'Total Active Schedules');

INSERT INTO plugin_reports_report_sharing
  (report_id, group_id)
  (SELECT (select id from plugin_reports_reports where `name` = 'Total Active Schedules'), id FROM engine_project_role WHERE `role` IN ('Receptionist', 'Administrator', 'Super User'));

UPDATE `plugin_reports_reports`
  SET
		`sql` = "SELECT \n	CONCAT( \n	\'<div class=\"text-center\"><h3>Total Active Schedules</h3><span style=\"font-size: 2em;\">\', \n		`count`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Active Schedules\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `count` FROM (\n			SELECT DISTINCT s.id AS `schedule_id`, s.trainer_id\n			FROM plugin_courses_schedules s\n				INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\n			WHERE '{!DASHBOARD-FROM!}' <= e.datetime_start AND '{!DASHBOARD-TO!}' >= e.datetime_start\n		) s\n) AS `counter`"
	WHERE `name` = 'Total Active Schedules';

