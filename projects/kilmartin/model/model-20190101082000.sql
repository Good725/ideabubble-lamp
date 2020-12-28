/*
ts:2019-01-01 08:20:00
*/

DELETE FROM plugin_reports_parameters WHERE name <> 'date' AND report_id in (select id from plugin_reports_reports where name='Master Roll Call' and `delete`=0) /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Master Roll Call' and `delete`=0), 'custom', 'room', 'SELECT \r\n		DISTINCT IFNULL(locations.id, buildings.id), \r\n		CONCAT_WS(\' \', buildings.`name`, locations.`name`) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) \r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`') /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Master Roll Call' and `delete`=0), 'custom', 'trainer_id', 'SELECT DISTINCT t.id, CONCAT_WS(\' \', t.title, t.first_name, t.last_name)\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND s.delete=0 AND e.delete=0\r\n		INNER JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND s.location_id = \'{!room!}\'\r\n	ORDER BY t.first_name, t.last_name\r\n') /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Master Roll Call' and `delete`=0), 'custom', 'schedule_id', 'SELECT \r\n		DISTINCT s.id, \r\n		CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND (s.trainer_id = \'{!trainer_id!}\' OR e.trainer_id = \'{!trainer_id!}\')\r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`, s.`name`') /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Master Roll Call' and `delete`=0), 'custom', 'timeslot_id', 'SELECT DISTINCT e.id, date_format(e.datetime_start, \'%H:%i\') as timeslot\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\r\n		WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND s.id = \'{!schedule_id!}\' AND e.delete=0\r\n	ORDER BY e.datetime_start') /*6*/;


DELETE FROM plugin_reports_parameters WHERE name <> 'date' AND report_id in (select id from plugin_reports_reports where name='Print Roll Call' and `delete`=0) /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Print Roll Call' and `delete`=0), 'custom', 'room', 'SELECT \r\n		DISTINCT IFNULL(locations.id, buildings.id), \r\n		CONCAT_WS(\' \', buildings.`name`, locations.`name`) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) \r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`') /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Print Roll Call' and `delete`=0), 'custom', 'trainer_id', 'SELECT DISTINCT t.id, CONCAT_WS(\' \', t.title, t.first_name, t.last_name)\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND s.delete=0 AND e.delete=0\r\n		INNER JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND s.location_id = \'{!room!}\'\r\n	ORDER BY t.first_name, t.last_name\r\n') /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Print Roll Call' and `delete`=0), 'custom', 'schedule_id', 'SELECT \r\n		DISTINCT s.id, \r\n		CONCAT( s.`name`, \' \', IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND (s.trainer_id = \'{!trainer_id!}\' OR e.trainer_id = \'{!trainer_id!}\')\r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`, s.`name`') /*6*/;

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Print Roll Call' and `delete`=0), 'custom', 'timeslot_id', 'SELECT DISTINCT e.id, date_format(e.datetime_start, \'%H:%i\') as timeslot\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\r\n		WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND s.id = \'{!schedule_id!}\' AND e.delete=0\r\n	ORDER BY e.datetime_start') /*6*/;


UPDATE `plugin_reports_parameters`
  SET
    `name`='location',
    `value`='SELECT \r\n		DISTINCT buildings.id, \r\n		buildings.`name` AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		INNER JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		INNER JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`'
    WHERE `name`='room' and report_id in ((select id from plugin_reports_reports where name in ('Print Roll Call', 'Master Roll Call') and `delete`=0));

UPDATE `plugin_reports_parameters`
  SET
    `value`='SELECT DISTINCT t.id, CONCAT_WS(\' \', t.title, t.first_name, t.last_name)\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND s.delete=0 AND e.delete=0\r\n		INNER JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND buildings.id = \'{!location!}\'\r\n	ORDER BY t.first_name, t.last_name\r\n'
  WHERE `name`='trainer_id' and report_id in ((select id from plugin_reports_reports where name in ('Print Roll Call', 'Master Roll Call') and `delete`=0));
