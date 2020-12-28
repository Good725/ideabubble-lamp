/*
ts:2019-01-15 13:00:00
*/

ALTER TABLE `plugin_reports_widgets` ADD COLUMN `extra_text` MEDIUMTEXT NULL DEFAULT NULL AFTER `html`;

ALTER TABLE `plugin_reports_widgets` ADD COLUMN `fill_color` VARCHAR(32) NULL DEFAULT NULL AFTER `extra_text`;


/* Create the dashboard, if it doesn't already exist */
INSERT INTO `plugin_dashboards` (`title`, `columns`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Welcome',
  '3',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM `plugin_dashboards`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Welcome' AND `deleted` = 0)
LIMIT 1
;

/* Create the reports, if they do not already exist */
INSERT INTO `plugin_reports_reports` (`name`, `date_created`, `delete`) SELECT 'Bookings By Month',   CURRENT_TIMESTAMP, '0' FROM `plugin_reports_reports` WHERE NOT EXISTS (SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month'   AND `delete` = 0) LIMIT 1;
INSERT INTO `plugin_reports_reports` (`name`, `date_created`, `delete`) SELECT 'Attendance By Month', CURRENT_TIMESTAMP, '0' FROM `plugin_reports_reports` WHERE NOT EXISTS (SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = 0) LIMIT 1;

/* Create the report widgets, if they do not already exist */
INSERT INTO `plugin_reports_widgets` (`name`, `date_created`, `delete`) SELECT 'Bookings By Month',   CURRENT_TIMESTAMP, '0' FROM `plugin_reports_widgets` WHERE NOT EXISTS (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Bookings By Month'   AND `delete` = 0) LIMIT 1;
INSERT INTO `plugin_reports_widgets` (`name`, `date_created`, `delete`) SELECT 'Attendance By Month', CURRENT_TIMESTAMP, '0' FROM `plugin_reports_widgets` WHERE NOT EXISTS (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Attendance By Month' AND `delete` = 0) LIMIT 1;

/* Create the sparklines, if they do not already exist */
INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `date_created`, `deleted`) SELECT 'BOOKINGS',        (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month'   AND `delete` = '0' LIMIT 1), CURRENT_TIMESTAMP, '0' FROM `plugin_reports_sparklines` WHERE NOT EXISTS (SELECT `id` FROM `plugin_reports_sparklines` WHERE `title` = 'BOOKINGS'        AND `deleted` = 0) LIMIT 1;
INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `date_created`, `deleted`) SELECT 'ATTENDANCE',      (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = '0' LIMIT 1), CURRENT_TIMESTAMP, '0' FROM `plugin_reports_sparklines` WHERE NOT EXISTS (SELECT `id` FROM `plugin_reports_sparklines` WHERE `title` = 'ATTENDANCE'      AND `deleted` = 0) LIMIT 1;
INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `date_created`, `deleted`) SELECT 'WEBSITE TRAFFIC', (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Website Traffic'     AND `delete` = '0' LIMIT 1), CURRENT_TIMESTAMP, '0' FROM `plugin_reports_sparklines` WHERE NOT EXISTS (SELECT `id` FROM `plugin_reports_sparklines` WHERE `title` = 'WEBSITE TRAFFIC' AND `deleted` = 0) LIMIT 1;

/* Update the reports to have the necessary content */
UPDATE
  `plugin_reports_reports`
SET
  `sql`           = 'SELECT b.booking_id AS `booking id`, CONCAT_WS(\' \', students.first_name, students.last_name) AS `student`, pl.name AS `location`, l.name AS `room`, co.title AS `course`, s.name AS `schedule`, DATE_FORMAT(b.created_date, \'%d/%m/%Y\') AS `booked day`, CONCAT_WS(\' \', teachers.first_name, teachers.last_name) AS `teacher`
\nFROM plugin_courses_schedules s
\nINNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0
\nINNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status in (2, 4, 5)
\nINNER JOIN plugin_contacts3_contacts students ON b.contact_id = students.id
\nINNER JOIN plugin_courses_courses co ON s.course_id = co.id
\nINNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id
\nINNER JOIN plugin_courses_locations l ON s.location_id = l.id
\nLEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id
\nWHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date
\nORDER BY b.booking_id',

  `widget_sql`    = 'SELECT DATE_FORMAT(b.created_date, \'%m/%Y\') AS `month`, count(*) AS `qty`
\nFROM plugin_courses_schedules s
\nINNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0
\nINNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status in (2, 4, 5)
\nINNER JOIN plugin_courses_courses co ON s.course_id = co.id
\nINNER JOIN plugin_courses_locations l ON s.location_id = l.id
\nLEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id
\nWHERE \'{!DASHBOARD-FROM!}\' <= b.created_date AND \'{!DASHBOARD-TO!}\' >= b.created_date
\nGROUP BY `month`
\nORDER BY b.created_date ASC',
  `dashboard`     = 1,
  `widget_id`     = (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Bookings By Month'    AND `delete`  = 0 LIMIT 1),
  `modified_by`   = (SELECT `id` FROM `engine_users`           WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `date_modified` = CURRENT_TIMESTAMP,
  `publish`       = 1
WHERE
  `name` = 'Bookings By Month' AND `delete` = 0;


UPDATE
  `plugin_reports_reports`
SET
  `sql` = 'SELECT b.booking_id AS `booking id`,  CONCAT_WS(\' \', students.first_name, students.last_name) AS `student`, pl.name AS `location`, l.name AS `room`, co.title AS `course`, s.name AS `schedule`, CONCAT_WS(\' \', teachers.first_name, teachers.last_name) AS `teacher`, e.datetime_start AS `timeslot`, IF(i.attending, \'Yes\', \'No\') AS `attending`, i.timeslot_status AS `status`
\nFROM plugin_courses_schedules_events e
\nINNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0
\nINNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0
\nINNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status in (2, 4, 5)
\nINNER JOIN plugin_contacts3_contacts students ON b.contact_id = students.id
\nINNER JOIN plugin_courses_courses co ON s.course_id = co.id
\nINNER JOIN plugin_courses_locations l ON s.location_id = l.id
\nLEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id
\nLEFT JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id
\nWHERE \'{!DASHBOARD-FROM!}\' <= e.datetime_start AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > e.datetime_start
\nORDER BY e.datetime_start ASC',

  `widget_sql` = 'SELECT DATE_FORMAT(e.datetime_start, \'%m/%Y\') AS `month`, count(*) AS `qty`
\nFROM plugin_courses_schedules_events e
\nINNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0
\nINNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0
\nINNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status in (2, 4, 5)
\nINNER JOIN plugin_courses_courses co ON s.course_id = co.id
\nINNER JOIN plugin_courses_locations l ON s.location_id = l.id
\nLEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id
\nWHERE \'{!DASHBOARD-FROM!}\' <= e.datetime_start AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > e.datetime_start
\nGROUP BY `month`
\nORDER BY e.datetime_start ASC',
  `dashboard`     = '1',
  `date_modified` = CURRENT_TIMESTAMP,
  `publish`       = '1',
  `widget_id`     = (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Attendance By Month' AND `delete` = 0 LIMIT 1),
  `report_type`   = 'sql'
WHERE
  `name` = 'Attendance By Month' AND `delete` = 0;


/* Update the widgets to have the necessary content */
UPDATE
  `plugin_reports_widgets`
SET
  `type`       = (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'line_graph' LIMIT 1),
  `x_axis`     = 'month',
  `y_axis`     = 'qty',
  `fill_color` = '#00c7ef',
  `extra_text` = '<h2>Bookings</h2>\n\n<p><a href=\"/admin/bookings\">View all bookings</a></p>',
  `publish`    = 1
WHERE
  `name`   = 'Bookings By Month'
AND
  `delete` = 0
;

UPDATE
  `plugin_reports_widgets`
SET
  `type`       = (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'line_graph' LIMIT 1),
  `x_axis`     = 'month',
  `y_axis`     = 'qty',
  `fill_color` = '#013c7f',
  `extra_text` = (SELECT CONCAT('<h2>Attendance</h2>\n\n<p><a href=\"/admin/dashboards/view_dashboard/', IFNULL(`id`, ''), '">View all attendance</a></p>') FROM `plugin_dashboards` WHERE `title` = 'Attendance' LIMIT 1),
  `publish`    = 1
WHERE
  `name`       = 'Attendance By Month'
AND
  `delete`     = 0
;

UPDATE
  `plugin_reports_widgets`
SET
  `type`       = (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'line_graph' LIMIT 1),
  `x_axis`     = 'Month',
  `y_axis`     = 'Hits',
  `fill_color` = '#8a81e6',
  `extra_text` = CONCAT('<h2>Website traffic</h2>\n\n<p><a href=\"/admin/dashboards/view_dashboard/', (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Traffic' LIMIT 1), '">View traffic dashboard</a></p>'),
  `publish`    = 1
WHERE
  `name`       = 'Website Traffic'
AND
  `delete`     = 0
;

/* Update the sparklines to have the necessary content */
UPDATE
  `plugin_reports_sparklines`
SET
  `chart_type_id` = (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'comparison_total' LIMIT 1),
  `x_axis`        = 'month',
  `y_axis`        = 'qty',
  `total_field`   = 'qty',
  `total_type_id` = (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'sum' LIMIT 1)
WHERE
  `title` = 'BOOKINGS' AND `report_id` IN (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month' AND `delete` = '0')
;

UPDATE
  `plugin_reports_sparklines`
SET
  `chart_type_id` = (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'comparison_total' LIMIT 1),
  `x_axis`        = 'month',
  `y_axis`        = 'qty',
  `total_field`   = 'qty',
  `total_type_id` = (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'sum' LIMIT 1)
WHERE
  `title` = 'ATTENDANCE' AND `report_id` IN (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = '0')
;

UPDATE
  `plugin_reports_sparklines`
SET
  `chart_type_id` = (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'comparison_total' LIMIT 1),
  `x_axis`        = 'Month',
  `y_axis`        = 'Hits',
  `total_field`   = 'Hits',
  `total_type_id` = (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'sum' LIMIT 1)
WHERE
  `title` = 'WEBSITE TRAFFIC' AND `report_id` IN (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Website Traffic' AND `delete` = '0')
;

/* Add the report parameters */
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `delete`, `is_multiselect`)
  SELECT  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month' AND `delete` = '0'),  'date', 'DASHBOARD-FROM', '0', '0'
  FROM    `plugin_reports_parameters`
  WHERE   NOT EXISTS (SELECT `id` FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month' AND `delete` = '0') AND `name` = 'DASHBOARD-FROM')
  LIMIT 1;

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `delete`, `is_multiselect`)
  SELECT  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month' AND `delete` = '0'),  'date', 'DASHBOARD-TO', '0', '0'
  FROM    `plugin_reports_parameters`
  WHERE   NOT EXISTS (SELECT `id` FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings By Month' AND `delete` = '0') AND `name` = 'DASHBOARD-TO')
  LIMIT 1;

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `delete`, `is_multiselect`)
  SELECT  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = '0' LIMIT 1),  'date', 'DASHBOARD-FROM', '0', '0'
  FROM    `plugin_reports_parameters`
  WHERE   NOT EXISTS (SELECT `id` FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = '0' LIMIT 1) AND `name` = 'DASHBOARD-FROM')
  LIMIT 1;

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `delete`, `is_multiselect`)
  SELECT  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = '0' LIMIT 1),  'date', 'DASHBOARD-TO', '0', '0'
  FROM    `plugin_reports_parameters`
  WHERE   NOT EXISTS (SELECT `id` FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Attendance By Month' AND `delete` = '0' LIMIT 1) AND `name` = 'DASHBOARD-TO')
  LIMIT 1;


/* Add the reports to the dashboard */
INSERT INTO
  `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES (
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Welcome'           AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Bookings by Month' AND `delete`  = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget'),
  '1',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
),(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Welcome'             AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Attendance by Month' AND `delete`  = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget'),
  '2',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
),(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Welcome'         AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Website Traffic' AND `delete`  = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget'),
  '3',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
)
;