/*
ts:2020-04-22 20:00:00
*/

DELIMITER  ;;
INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
VALUES ('TU application', 'sql', '1', '0');;

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = "SELECT
\n    CONCAT_WS(' ', JSON_UNQUOTE(`application`.`data`->'$.first_name'), JSON_UNQUOTE(`application`.`data`->'$.last_name')) AS 'Name',
\n    CONCAT(
\n        '<span class=\"hidden\">',
\n        STR_TO_DATE(JSON_UNQUOTE(`application`.`data`->'$.dob'), '%m/%d/%Y'),
\n        '</span>',
\n        DATE_FORMAT(STR_TO_DATE(JSON_UNQUOTE(`application`.`data`->'$.dob'), '%m/%d/%Y'), '%e %M %Y')
\n    ) AS 'DOB',
\n    JSON_UNQUOTE(`application`.`data`->'$.gender')      AS 'Gender',
\n    JSON_UNQUOTE(`application`.`data`->'$.Nationality') AS 'Nationality',
\n    JSON_UNQUOTE(`application`.`data`->'$.pps')         AS 'PPS',
\n    CONCAT_WS(
\n        ', ',
\n        JSON_UNQUOTE(`application`.`data`->'$.address1'),
\n        JSON_UNQUOTE(`application`.`data`->'$.address2'),
\n        JSON_UNQUOTE(`application`.`data`->'$.address3'),
\n        JSON_UNQUOTE(`application`.`data`->'$.city'),
\n        JSON_UNQUOTE(`application`.`data`->'$.country')
\n     ) AS 'Address',
\n    JSON_UNQUOTE(`application`.`data`->'$.phone')       AS 'Phone',
\n    JSON_UNQUOTE(`application`.`data`->'$.email')       AS 'Email',
\n    `course`.`title`                                    AS 'Course',
\n    `schedule`.`name`                                   AS 'Schedule'
\nFROM `plugin_ib_educate_bookings_has_applications` `application`
\nLEFT JOIN `plugin_courses_schedules`     `schedule` ON JSON_UNQUOTE(`application`.`data`->'$.schedule_id') = `schedule`.`id`
\nLEFT JOIN `plugin_courses_courses`         `course` ON `schedule`.`course_id`     = `course`.`id`
\nLEFT JOIN `plugin_ib_educate_bookings`    `booking` ON `application`.`booking_id` = `booking`.`booking_id`
\nLEFT JOIN `plugin_ib_educate_booking_items`  `item` ON `item`.`booking_id`        = `booking`.`booking_id`
\nLEFT JOIN `plugin_courses_schedules_events` `event` ON `item`.`period_id`         = `event`.`id`
\nLEFT JOIN `plugin_courses_locations`  `sublocation` ON `schedule`.`location_id`   = `sublocation`.`id`
\nLEFT JOIN `plugin_courses_locations`     `location` ON `sublocation`.`parent_id`  = `location`.`id`
\n
\nWHERE (`event`.`datetime_start` >= '{!date!}' OR '{!date!}' = '')
\nAND   (`event`.`datetime_start` <  DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) OR '{!date!}' = '')
\nAND   (`location`.`id` = '{!location_id!}' OR `sublocation`.`id` = '{!location_id!}' OR '{!location_id!}' = '')
\nAND   (`course`.`id`   = '{!course_id!}'   OR '{!course_id!}'   = '')
\nAND   (`schedule`.`id` = '{!schedule_id!}' OR '{!schedule_id!}' = '')
\nGROUP BY `booking`.`booking_id`
\nORDER BY `booking`.`created_date` DESC;"
WHERE
  `name` = 'TU application'
;;

-- INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'Date from', `value` = '', `delete` = 0, `is_multiselect` = 0;;
-- INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'Date to',   `value` = '', `delete` = 0, `is_multiselect` = 0;;

INSERT INTO plugin_reports_parameters (report_id, `type`, `name`, `value`, `delete`, `is_multiselect`)
  (SELECT `id`, 'date', 'date', '', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'TU application');;

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`)
  (SELECT `id`, 'custom', 'location_id', 'SELECT
\n		DISTINCT buildings.id, 
\n		buildings.`name` AS `name`
\n	FROM plugin_courses_schedules s
\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0
\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0
\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0
\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id
\n		INNER JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n		INNER JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id
\n	GROUP BY s.id
\n	ORDER BY buildings.`name`, locations.`name`', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'TU application');;


INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`)
  (SELECT `id`, 'custom', 'course_id', 'SELECT
\n		DISTINCT c.id,
\n		CONCAT_WS(\' \',  c.`title`) AS `name`
\n	FROM plugin_courses_courses c
\n		INNER JOIN plugin_courses_schedules s         ON c.id = s.course_id   AND s.`delete` = 0
\n		INNER JOIN plugin_courses_schedules_events e  ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0
\n		INNER JOIN plugin_ib_educate_booking_items i  ON e.id = i.period_id   AND i.`delete` = 0 and i.booking_status <> 3
\n		INNER JOIN plugin_ib_educate_bookings b       ON i.booking_id = b.booking_id AND b.`delete` = 0 and b.booking_status <> 3
\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n		LEFT JOIN plugin_courses_locations buildings  ON locations.parent_id = buildings.id
\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND s.publish=1
\n	GROUP BY c.id
\n	ORDER BY `c`.`title`', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'Tu application');;


INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`)
  (SELECT `id`, 'custom', 'schedule_id', 'SELECT 
\n		DISTINCT s.id, 
\n		CONCAT_WS(\' \',  s.`name`, IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`
\n	FROM `plugin_courses_schedules` `s`
\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id   AND e.`delete` = 0 AND s.`delete` = 0
\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id     AND i.`delete` = 0 and i.booking_status <> 3
\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 and b.booking_status <> 3
\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id
\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id
\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND s.publish=1
\n	GROUP BY s.id
\n	ORDER BY buildings.`name`, locations.`name`, s.`name`', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'Tu application');;
