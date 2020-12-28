/*
ts:2018-01-01 21:54:00
*/

INSERT INTO
    `plugin_reports_parameters`
  SET
    `type` = 'custom',
    `name` = 'year',
    `value` = 'SELECT DISTINCT y.id, y.`year`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\r\n		INNER JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		INNER JOIN plugin_courses_years y on c.year_id = y.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY)\r\n	ORDER BY y.`year`',
    `delete` = 0,
    `is_multiselect` = 1,
    `report_id` = (select id from plugin_reports_reports where `name` = 'Master Roll Call');

INSERT INTO
    `plugin_reports_parameters`
  SET
    `type` = 'custom',
    `name` = 'year',
    `value` = 'SELECT DISTINCT y.id, y.`year`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\r\n		INNER JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		INNER JOIN plugin_courses_years y on c.year_id = y.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY)\r\n	ORDER BY y.`year`',
    `delete` = 0,
    `is_multiselect` = 1,
    `report_id` = (select id from plugin_reports_reports where `name` = 'Print Roll Call');


SELECT id INTO @year_param_id_2018_p1 FROM plugin_reports_parameters WHERE `name` = 'year' AND report_id = (select id from plugin_reports_reports where `name` = 'Master Roll Call');
UPDATE plugin_reports_parameters
	SET id = @year_param_id_2018_p1 + 101
	WHERE `name`='schedule_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Master Roll Call');

UPDATE plugin_reports_parameters
	SET id = @year_param_id_2018_p1 + 102
	WHERE `name`='trainer_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Master Roll Call');

SELECT id INTO  @year_param_id_2018_p2 FROM plugin_reports_parameters WHERE `name` = 'year' AND report_id = (select id from plugin_reports_reports where `name` = 'Print Roll Call');
UPDATE plugin_reports_parameters
	SET id = @year_param_id_2018_p2 + 201
	WHERE `name`='schedule_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Print Roll Call');

UPDATE plugin_reports_parameters
	SET id = @year_param_id_2018_p2 + 202
	WHERE `name`='trainer_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Print Roll Call');

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT s.id, CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND c.year_id in ({!year!})\r\n	GROUP BY s.id\r\n	ORDER BY s.`name`'
  WHERE (`name`='schedule_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Master Roll Call'));

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT s.id, CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND c.year_id = ({!year!})\r\n	GROUP BY s.id\r\n	ORDER BY s.`name`'
  WHERE (`name`='schedule_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Print Roll Call'));
