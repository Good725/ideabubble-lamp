/*
ts:2019-03-01 20:05:00
*/

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT \r\n		DISTINCT s.id, \r\n		CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0 and i.booking_status <> 3\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 and b.booking_status <> 3\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND (s.trainer_id = \'{!trainer_id!}\' OR e.trainer_id = \'{!trainer_id!}\') AND s.publish=1\r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`, s.`name`'
  WHERE (`name`='schedule_id' and report_id in (select id from plugin_reports_reports where name in ('Master Roll Call', 'Print Roll Call')));
  