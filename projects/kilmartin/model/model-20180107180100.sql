/*
ts:2018-01-07 18:01:00
*/

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT y.id, y.`year`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\r\n		INNER JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		INNER JOIN plugin_courses_courses_has_years hy ON c.id = hy.course_id\r\n		INNER JOIN plugin_courses_years y on hy.year_id = y.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY)\r\n	ORDER BY y.`year`'
  WHERE (`report_id` in (select id from plugin_reports_reports where name in ('Master Roll Call', 'Print Roll Call'))) AND `name` = 'year';

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT s.id, CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		INNER JOIN plugin_courses_courses_has_years hy ON c.id = hy.course_id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND hy.year_id in ({!year!})\r\n	GROUP BY s.id\r\n	ORDER BY s.`name`'
  WHERE (`report_id` in (select id from plugin_reports_reports where name in ('Master Roll Call', 'Print Roll Call'))) AND `name` = 'schedule_id';
