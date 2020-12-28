/*
ts:2020-03-31 13:30:00
*/

DELIMITER  ;;
INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
VALUES ('Upcoming bookings', 'sql', '1', '0');;

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = 'SELECT DISTINCT
\n    GROUP_CONCAT(DISTINCT `category`.`category`)    AS `Category`,
\n    GROUP_CONCAT(DISTINCT `course`.`title`)         AS `Course`,
\n    GROUP_CONCAT(DISTINCT `schedule`.`name`)        AS `Schedule`,
\n    DATE_FORMAT(MIN(`timeslot`.`datetime_start`), ''<span class="hidden">%Y-%m-%d</span>%e %b %Y'') AS `Start date`,
\n    DATE_FORMAT(MIN(`timeslot`.`datetime_start`), ''<span class="hidden">%w</span>%a'') AS `Day`,
\n    GROUP_CONCAT(DISTINCT `parent_location`.`name`) AS `Location`,
\n    GROUP_CONCAT(DISTINCT `sub_location`.`name`)    AS `Sub location`,
\n    GROUP_CONCAT(DISTINCT CONCAT_WS('' '', `trainer`.`first_name`, `trainer`.`last_name`)) AS `Trainer`,
\n    COUNT(DISTINCT `booking`.`booking_id`)          AS `Bookings`,
\n    CONCAT(''€'', `schedule`.`fee_amount`)          AS `Fee`,
\n    CONCAT(''€'', `schedule`.`fee_amount` * COUNT(DISTINCT `booking`.`booking_id`)) AS `Total income`
\n
\nFROM `plugin_ib_educate_bookings` `booking`
\nLEFT JOIN `plugin_ib_educate_booking_items`        `item` ON         `item`.`booking_id`  =         `booking`.`booking_id`
\nLEFT JOIN `plugin_courses_schedules_events`    `timeslot` ON         `item`.`period_id`   =        `timeslot`.`id`
\nLEFT JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON          `bhs`.`booking_id`  =         `booking`.`booking_id` AND `bhs`.`deleted` = 0
\nLEFT JOIN `plugin_courses_schedules`           `schedule` ON          `bhs`.`schedule_id` =        `schedule`.`id`
\nLEFT JOIN `plugin_courses_courses`               `course` ON     `schedule`.`course_id`   =          `course`.`id`
\nLEFT JOIN `plugin_courses_categories`          `category` ON       `course`.`category_id` =        `category`.`id`
\nLEFT JOIN `plugin_courses_locations`       `sub_location` ON     `schedule`.`location_id` =    `sub_location`.`id`
\nLEFT JOIN `plugin_courses_locations`    `parent_location` ON `sub_location`.`parent_id`   = `parent_location`.`id`
\nLEFT JOIN `plugin_contacts3_contacts`           `trainer` ON     `schedule`.`trainer_id`  =         `trainer`.`id`
\n
\nWHERE `booking`.`delete` = 0
\nGROUP BY `schedule`.`id`
\n
\n HAVING ''{!DASHBOARD-FROM!}'' <= MIN(`timeslot`.`datetime_start`)
\n    AND ''{!DASHBOARD-TO!}''   >= MIN(`timeslot`.`datetime_start`)
\n;'
WHERE
  `name` = 'Upcoming bookings'
;;

INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Upcoming bookings' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, `is_multiselect` = 0;;
INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Upcoming bookings' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-TO',   `value` = '', `delete` = 0, `is_multiselect` = 0;;
