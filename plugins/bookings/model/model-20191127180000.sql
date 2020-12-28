/*
ts:2019-11-27 18:00:00
*/

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Bookings By category',
    `summary` = '',
    `sql` = "SELECT `category`.`category` AS 'Category', FORMAT(SUM(`booking`.`amount`), 0) as 'Amount (EUR)', FORMAT(COUNT(`booking`.`booking_id`), 0) as 'Number of bookings'
\nFROM `plugin_courses_categories` as `category`
\nLEFT JOIN `plugin_courses_courses`                  `course`   ON `course`  .`category_id` = `category`.`id`
\nLEFT JOIN `plugin_courses_schedules`                `schedule` ON `schedule`.`course_id`   = `course`  .`id`
\nLEFT JOIN `plugin_ib_educate_booking_has_schedules` `bhs`      ON `bhs`     .`schedule_id` = `schedule`.`id`
\nLEFT JOIN `plugin_ib_educate_bookings`              `booking`
\n    ON  `bhs`.`booking_id` = `booking`.`booking_id`
\n    AND `booking`.`delete` = 0
\n    AND `booking`.`booking_status` IN (2, 4, 5)
\n    AND  '{!DASHBOARD-FROM!}' <= `booking`.`created_date` AND '{!DASHBOARD-TO!}' >= `booking`.`created_date`
\nWHERE `category`.`delete` = 0
\nGROUP BY `category`.`id`
\nORDER BY `amount` DESC
\n",
    `category` = 0,
    `sub_category` = 0,
    `dashboard` = 0,
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';

INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings by category' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, `is_multiselect` = 0;
INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Bookings by category' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-TO',   `value` = '', `delete` = 0, `is_multiselect` = 0;

-- Add "amount" column to "bookings by course"
UPDATE
  `plugin_reports_reports`
SET
  `sql` = "SELECT b.booking_id AS `booking id`,  CONCAT_WS(' ', students.first_name, students.last_name) AS `student`, `b`.`amount`, pl.name AS `location`, l.name AS `room`, co.title AS `course`, s.name AS `schedule`, DATE_FORMAT(b.created_date, '%d/%m/%Y') AS `booked day`, CONCAT_WS(' ', teachers.first_name, teachers.last_name) AS `teacher`
\nFROM plugin_courses_schedules s
\n    INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.schedule_id = s.id AND hs.deleted = 0 AND s.`delete` = 0
\n    INNER JOIN plugin_ib_educate_bookings b ON hs.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status in (2, 4, 5)
\n    INNER JOIN plugin_contacts3_contacts students ON b.contact_id = students.id
\n    INNER JOIN plugin_courses_courses co ON s.course_id = co.id
\n    INNER JOIN plugin_contacts3_contacts teachers ON s.trainer_id = teachers.id
\n    INNER JOIN plugin_courses_locations l ON s.location_id = l.id
\n    LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id
\nWHERE '{!DASHBOARD-FROM!}' <= b.created_date AND '{!DASHBOARD-TO!}' >= b.created_date
\nORDER BY student
"
WHERE
  `name` = 'Bookings By Course'
;