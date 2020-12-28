/*
ts:2020-05-13 17:11:00
*/
DELIMITER  ;;
UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = 'SELECT
\n `contact`.`id` AS \'ContactID\',
\n  `course`.`title` AS \'Course\',
\n  `schedule`.`name` AS \'Schedule\',
\n  IF (JSON_UNQUOTE(`application`.`data` -> \'$.first_name\') IS NULL ,
\n	 CONCAT_WS(\'  \', `contact`.`first_name`, `contact`.`last_name`),
\n     CONCAT_WS(\'  \',
\n
\n          JSON_UNQUOTE(`application`.`data` -> \'$.first_name\'),
\n
\n           JSON_UNQUOTE(`application`.`data` -> \'$.last_name\'))
\n)
\n  AS \'Name\',
\nCONCAT(
\n  \'<span class="hidden">\',
\n    STR_TO_DATE(
\n      JSON_UNQUOTE(`application`.`data` -> \'$.dob\'),
\n      \'%d/%m/%Y\'
\n    ),
\n    \'</span>\',
\n    DATE_FORMAT(
\n      STR_TO_DATE(
\n        JSON_UNQUOTE(`application`.`data` -> \'$.dob\'),
\n        \'%d/%m/%Y\'
\n      ),
\n      \'%e %M %Y\'
\n    )
\n  ) AS \'DOB\',
\nJSON_UNQUOTE(
\n    `application`.`data` -> \'$.gender\'
\n  ) AS \'Gender\',
\n  JSON_UNQUOTE(
\n    `application`.`data` -> \'$.nationality\'
\n  ) AS \'Nationality\',
\nJSON_UNQUOTE(`application`.`data` -> \'$.pps\') AS \'PPS\',
\nCONCAT_WS(
\n    \', \',
\n    JSON_UNQUOTE(
\n      `application`.`data` -> \'$.address1\'
\n   ),
\n    JSON_UNQUOTE(
\n      `application`.`data` -> \'$.address2\'
\n    ),
\n    JSON_UNQUOTE(
\n     `application`.`data` -> \'$.address3\'
\n    ),
\n    JSON_UNQUOTE(`application`.`data` -> \'$.city\'),
\n    JSON_UNQUOTE(
\n      `application`.`data` -> \'$.country\'
\n    )
\n  ) AS \'Address\',
\n  JSON_UNQUOTE(`application`.`data` -> \'$.city\') as \'City\',
\n `county`.`name` as \'County\',
\n  JSON_UNQUOTE(
\n    `application`.`data` -> \'$.phone\'
\n  ) AS \'Phone\',
\n  JSON_UNQUOTE(
\n    `application`.`data` -> \'$.email\'
\n  ) AS \'Email\',
\nJSON_UNQUOTE(
\n    `application`.`data` -> \'$.schedule_name\'
\n  ) AS \'TU Dublin Code\',
\n IF( `application_status`.`title` IS NULL OR  `application_status`.`title` != \'Completed\',  \'\' , DATE_FORMAT(MAX(`application_history`.`timestamp`), \'%Y-%m-%d %H:%i:%s\'))
\n   AS \'Application Submitted\',
\n CASE
\n WHEN `application_status`.`title` IS NULL OR  `application_status`.`title` = \'Enquiry\'
\n THEN   \'Not Submitted\'  WHEN `application_status`.`title` = \'Completed\' THEN \'Submitted\' ELSE `application_status`.`title` END
\n   AS \'Application Status\',
\nJSON_UNQUOTE(
\n    `application`.`data` -> \'$.highest_qualification\'
\n  ) AS \'Highest Qualification\',
\nJSON_UNQUOTE(
\n    `application`.`data` -> \'$.employment_history\'
\n  ) AS \'Employment History\',
\nJSON_UNQUOTE(
\n    `application`.`data` -> \'$.other_information\'
\n  ) AS \'Other Information\',
\nJSON_UNQUOTE(
\n    `application`.`data` -> \'$.declaration\'
\n  ) AS \'Declaration\'
\nFROM
\n  `plugin_ib_educate_bookings_has_applications_history` `application_history`
\n  LEFT JOIN  `plugin_ib_educate_bookings_has_applications` `application`
\n			ON `application_history`.`booking_id` = `application`.`booking_id`
\n   LEFT JOIN
\n	       `plugin_ib_educate_bookings_status` `application_status`
\n           ON `application`.`status_id` = `application_status`.`status_id`
\n
\n  LEFT JOIN `plugin_courses_schedules` `schedule` ON JSON_UNQUOTE(
\n    `application`.`data` -> \'$.schedule_id\'
\n  ) = `schedule`.`id`
\n  LEFT JOIN `plugin_contacts3_contacts` `contact`  ON JSON_UNQUOTE(
\n
\n    `application`.`data` -> \'$.contact_id\'
\n
\n  ) = `contact`.`id`
\n  LEFT JOIN `plugin_courses_counties` `county` ON JSON_UNQUOTE(
\n
\n    `application`.`data` -> \'$.county\'
\n
\n  ) = `county`.`id`
\n  LEFT JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id`
\n  LEFT JOIN `plugin_courses_categories` `category` ON `course`.`category_id` = `category`.`id`
\n  LEFT JOIN `plugin_ib_educate_bookings` `booking` ON `application`.`booking_id` = `booking`.`booking_id`
\n  LEFT JOIN `plugin_ib_educate_booking_items` `item` ON `item`.`booking_id` = `booking`.`booking_id`
\n  LEFT JOIN `plugin_courses_schedules_events` `event` ON `item`.`period_id` = `event`.`id`
\n  LEFT JOIN `plugin_courses_locations` `sublocation` ON `schedule`.`location_id` = `sublocation`.`id`
\n  LEFT JOIN `plugin_courses_locations` `location` ON `sublocation`.`parent_id` = `location`.`id`
\nWHERE
\n   (
\n    `category`.`id` = \'{!Category!}\'
\n    OR \'{!Category!}\' = \'\'
\n  )
\n  AND (
\n    `course`.`id` = \'{!Course!}\'
\n    OR \'{!Course!}\' = \'\'
\n  )
\n  AND (
\n    `schedule`.`id` = \'{!Schedule!}\'
\n    OR \'{!Schedule!}\' = \'\'
\n  )
\n    AND (
\n    `location`.`id` = \'{!Location!}\'
\n    OR `sublocation`.`id` = \'{!Location!}\'
\n    OR \'{!Location!}\' = \'\'
\n )
\n AND (
\n `application_status`.`status_id` IN(\'{!Status!}\')
\n  OR \'{!Status!}\' = \'\'
\n)
\n
\nGROUP BY
\n  `booking`.`booking_id`
\n HAVING (CONCAT(\'{!DASHBOARD-FROM!}\', " 00:00:00")  <= MAX(`application_history`.`timestamp`) OR \'\' = \'{!DASHBOARD-FROM!}\')
\n    AND (CONCAT(\'{!DASHBOARD-TO!}\', " 23:59:59")   >= MAX(`application_history`.`timestamp`) OR \'\' = \'{!DASHBOARD-TO!}\')
\nORDER BY
\n  `booking`.`created_date` DESC'
WHERE
        `name` = 'TU application';;
UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `php_post_filter` = '\n
\n     $countries  = Model_Country::get_countries();
\n     foreach ($data as $i => $row) {
\n         if (!empty($row[\'Country\'])) {
\n               $row[\'Country\'] = $countries[$row[\'Country\']][\'name\'];
\n         }
\n         $data[$i] = $row;
\n     }'
WHERE
    `name` = 'TU application';;

DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'date' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'DASHBOARD-FROM' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'DASHBOARD-TO' ;;
INSERT IGNORE INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, `is_multiselect` = 0;;
INSERT IGNORE INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-TO',   `value` = '', `delete` = 0, `is_multiselect` = 0;;

DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'Completed' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'Status' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'Category' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'Course' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'Schedule' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'Location' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'category_id' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'course_id' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'schedule_id' ;;
DELETE FROM `plugin_reports_parameters` WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'TU application')  AND `name` = 'location_id' ;;

INSERT INTO `plugin_reports_parameters`
(
    `report_id`,
    `type`,
    `name`,
    `value`)
VALUES (
           (select id from plugin_reports_reports where name='TU Application' ORDER BY id DESC LIMIT 1),
           'custom',
           'Category',
           '((select id, category from plugin_courses_categories where `delete`=0 order by category))');;
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`)
    (SELECT `id`, 'custom', 'Course', 'SELECT
\n		DISTINCT c.id,
\n		CONCAT_WS(\' \',  c.`title`) AS `name`
\n	FROM plugin_courses_courses c
\n		INNER JOIN plugin_courses_schedules s         ON c.id = s.course_id   AND s.`delete` = 0
\n		INNER JOIN plugin_courses_schedules_events e  ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0
\n		INNER JOIN plugin_ib_educate_booking_items i  ON e.id = i.period_id   AND i.`delete` = 0 and i.booking_status <> 3
\n		INNER JOIN plugin_ib_educate_bookings b       ON i.booking_id = b.booking_id AND b.`delete` = 0 and b.booking_status <> 3
\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n		LEFT JOIN plugin_courses_locations buildings  ON locations.parent_id = buildings.id
\n	WHERE s.publish=1
\n	GROUP BY c.id
\n	ORDER BY `c`.`title`', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'Tu application');;


INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`)
    (SELECT `id`, 'custom', 'Schedule', 'SELECT
\n		DISTINCT s.id,
\n		CONCAT_WS(\' \',  s.`name`, IF(s.payment_type = 1, \'PrePAY\', \'PAYG\')) AS `name`
\n	FROM `plugin_courses_schedules` `s`
\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id   AND e.`delete` = 0 AND s.`delete` = 0
\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id     AND i.`delete` = 0 and i.booking_status <> 3
\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 and b.booking_status <> 3
\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id
\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id
\n	WHERE s.publish=1
\n	GROUP BY s.id
\n	ORDER BY buildings.`name`, locations.`name`, s.`name`', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'Tu application');;

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`)
  (SELECT `id`, 'custom', 'Location', 'SELECT
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
  (SELECT `id`, 'custom', 'Status', 'SELECT
	GROUP_CONCAT(DISTINCT  status_id ) as `status_id`,
    IF(`status_id`  = (SELECT status_id FROM `plugin_ib_educate_bookings_status` WHERE `title` = \'Completed\'), \'Completed\', \'Not Completed\')
     FROM `plugin_ib_educate_bookings_status`
     GROUP BY (CASE  WHEN  title  != \'Completed\' THEN \'Completed\' ELSE \'Not Completed\'  END)', 0,0 FROM `plugin_reports_reports` WHERE `name` = 'TU application');;


