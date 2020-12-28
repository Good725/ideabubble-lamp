/*
ts:2018-01-19 09:46:00
*/

DELETE FROM plugin_reports_parameters WHERE `name` = 'year' AND report_id IN (select id from plugin_reports_reports where `name` in ('Master Roll Call', 'Print Roll Call'));

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT s.id, CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) \r\n	GROUP BY s.id\r\n	ORDER BY s.`name`'
  WHERE (`name`='schedule_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Master Roll Call'));

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT s.id, CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) \r\n	GROUP BY s.id\r\n	ORDER BY s.`name`'
  WHERE (`name`='schedule_id' AND report_id = (select id from plugin_reports_reports where `name` = 'Print Roll Call'));

