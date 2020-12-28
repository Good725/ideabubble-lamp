/*
ts:2020-09-18 10:31:00
*/

UPDATE `plugin_reports_parameters` SET `value`='SELECT \r\n		DISTINCT IFNULL(buildings.id, locations.id) as id, \r\n		IFNULL(buildings.`name`, locations.`name`) AS `name`\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0\r\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0\r\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	GROUP BY s.id\r\n	ORDER BY buildings.`name`, locations.`name`'
  WHERE (`name`='location' and report_id in (select id from plugin_reports_reports where name like '%roll call%'));

UPDATE plugin_reports_reports SET action_event = REPLACE (action_event,"$.post",'$("#report_table").dataTable().fnDestroy();$.post') WHERE `name` LIKE '%roll call%' AND `delete`=0;
