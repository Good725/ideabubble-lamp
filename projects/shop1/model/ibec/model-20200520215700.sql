/*
ts:2020-05-20 21:57:00
*/

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
     VALUES ('Sales Quote Booking', 'sql', '1', '0');

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = "SELECT DISTINCT
\n    `bookings`.`booking_id`            AS `Booking ID`,
\n    `bookings`.`amount`                AS `Booking amount`,
\n    `bookings`.`created_date`          AS `Date created`,
\n    `schedules`.`name`                 AS `Schedule`,
\n    DATE_FORMAT(`schedules`.`start_date`, '%d %M %Y') AS `Start date`,
\n    DATE_FORMAT(`schedules`.`end_date`,   '%d %M %Y') AS `And date`,
\n    `courses`.`title`                  AS `Course`,
\n    `categories`.`category`            AS `Category`,
\n    `schedule_parent_locations`.`name` AS `Location`,
\n    `schedule_locations`.`name`        AS `Sublocation`,
\n    CONCAT_WS(' ', `lead_bookers`.`first_name`, `lead_bookers`.`last_name`) AS `Lead booker`,
\n    `bookings`.`contact_id`            AS `Lead booker ID`,
\n    `lead_booker_emails`.`value`       AS `Lead booker email`,
\n    `lead_booker_mobiles`.`value`      AS `Lead booker mobile`,
\n    `delegate_count`.`delegatecount`   AS `Delegates`
\n
\nFROM
\n    `plugin_ib_educate_bookings` AS `bookings`
\n
\nLEFT JOIN
\n    `plugin_ib_educate_bookings_status` AS `booking_status`
\n    ON `bookings`.`booking_status` = `booking_status`.`status_id`
\n
\nLEFT JOIN
\n    `plugin_ib_educate_booking_has_schedules` AS `has_schedules`
\n     ON `bookings`.`booking_id` = `has_schedules`.`booking_id`
\n
\nLEFT JOIN
\n    `plugin_ib_educate_bookings_status` AS `bhs_booking_status`
\n    ON `has_schedules`.`booking_status` = `bhs_booking_status`.`status_id`
\n    AND `bhs_booking_status`.`title` <> 'Cancelled'
\n
\nLEFT JOIN
\n   `plugin_courses_schedules` AS `schedules`
\n    ON `has_schedules`.`schedule_id` = `schedules`.`id`
\n
\nLEFT JOIN
\n    `plugin_courses_courses` AS `courses`
\n    ON `schedules`.`course_id` = `courses`.`id`
\n
\nLEFT JOIN
\n    `plugin_courses_categories` AS `categories`
\n    ON `courses`.`category_id` = `categories`.`id`
\n
\nLEFT JOIN
\n    `plugin_courses_subjects` AS `subjects`
\n    ON `courses`.`subject_id` = `subjects`.`id`
\n
\nLEFT JOIN
\n    `plugin_courses_levels` AS `levels`
\n    ON `courses`.`level_id` = `levels`.`id`
\n
\nLEFT JOIN
\n    `plugin_courses_locations` AS `schedule_locations`
\n    ON `schedules`.`location_id` = `schedule_locations`.`id`
\n
\nLEFT JOIN
\n    `plugin_courses_locations` AS `schedule_parent_locations`
\n    ON `schedule_locations`.`parent_id` = `schedule_parent_locations`.`id`
\n
\nLEFT JOIN
\n    `plugin_contacts3_contacts` AS `lead_bookers`
\n    ON (`bookings`.`contact_id` = `lead_bookers`.`id`)
\n
\nLEFT JOIN
\n    `plugin_contacts3_contact_has_notifications` AS `lead_booker_emails`
\n    ON `lead_bookers`.`notifications_group_id` = `lead_booker_emails`.`group_id`
\n    AND `lead_booker_emails`.`notification_id` = 1
\n
\nLEFT JOIN
\n    `plugin_contacts3_contact_has_notifications` AS `lead_booker_mobiles`
\n    ON `lead_bookers`.`notifications_group_id` = `lead_booker_mobiles`.`group_id`
\n    AND `lead_booker_mobiles`.`notification_id` = 2
\n
\nLEFT JOIN
\n    (SELECT`booking_id`, COUNT(*) AS `delegatecount` FROM `plugin_ib_educate_bookings_has_delegates` GROUP BY `booking_id`) AS `delegate_count`
\n    ON `bookings`.`booking_id` = `delegate_count`.`booking_id`
\n
\nWHERE
\n    `bookings`.`delete` = 0
\n    AND `booking_status`.`title` = 'Sales Quote'
\n    AND `bookings`.`created_date` >= CONCAT({!DASHBOARD-FROM!}, ' 00:00:00')
\n    AND `bookings`.`created_date` <= CONCAT({!DASHBOARD-TO!}, ' 23:59:59')
\n
\nGROUP BY
\n    `bookings`.`booking_id`
\n"
WHERE
    `name` = 'Sales Quote Booking';

INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Sales Quote Booking' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, `is_multiselect` = 0;
INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Sales Quote Booking' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-TO',   `value` = '', `delete` = 0, `is_multiselect` = 0;
