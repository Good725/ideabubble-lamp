/*
ts:2020-07-18 00:06:00
*/

DELIMITER  ;;
INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
VALUES ('How did you hear about us', 'sql', '1', '0');;

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` ='\n
\nSELECT
\n`category`.`category` as `Course Category`,
\n`course_types`.`type` as `Course Type`,
\n`courses`.`title` as `Course`,
\n`schedules`.`name` as `Schedule`,
\n`schedules`.`start_date` as `Schedule Start Date`,
\n`schedules`.`end_date` as `Schedule End Date`,
\n`timeslots`.`count_timeslots` as `Course Duration`,
\nifnull(`counties`.`name`,`parent_counties`.`name`) as `Parent Location County`,
\n`bookings`.`created_date` as `Booking Date`,
\nFORMAT(`bookings`.`amount`, 2) as `Fee`,
\nifnull(FORMAT( `discount_amount`, 2), ''0.00'') as `Discount`,
\nIFNULL(`delegates_per_booking`, 0) as `Count Delegates`,
\nIF (COUNT(`has_tags`.`tag_id`) > 0, ''Yes'', ''No'') as `Member`,
\n`contacts`.`first_name` as `First Name`,
\n`contacts`.`last_name` as `Last Name`,
\nGROUP_CONCAT(DISTINCT `email_1`.`value`)  AS `Email`,
\nifnull(`how_did_you_hear_val`.`label`, `how_did_you_hear_id`.`label`) as `How did you hear about us`,
\n`contacts`.`job_title` as `Job Title`,
\n`job_functions`.`label` as `Job Function`,
\n`organisation`.`first_name` as `Company Name`,
\n`industry`.`label` as `Organisation Industry`,
\n`organisation_size`.`label` as `Organisation Size`
\n  FROM `plugin_ib_educate_bookings` as `bookings`
\n      LEFT JOIN  (SELECT `booking_id`, SUM(`amount`) as `discount_amount` FROM `plugin_ib_educate_bookings_discounts` GROUP BY `booking_id`) as `discounts`
\n       ON `discounts`.`booking_id` = `bookings`.`booking_id`
\n    LEFT JOIN `plugin_ib_educate_booking_has_schedules` as `booking_schedules`
\n       ON `booking_schedules`.`booking_id` = `bookings`.`booking_id`
\n    LEFT JOIN `plugin_courses_schedules` as `schedules`
\n	     ON `booking_schedules`.`schedule_id` = `schedules`.`id`
\n	LEFT JOIN (SELECT `plugin_courses_schedules_events`.`schedule_id`, COUNT(`plugin_courses_schedules_events`.`id`)  as `count_timeslots`
\n		FROM `plugin_courses_schedules_events`
\n        WHERE `plugin_courses_schedules_events`.`publish` = 1 AND `plugin_courses_schedules_events`.`delete` = 0
\n        GROUP BY `plugin_courses_schedules_events`.`schedule_id`) as `timeslots`
\n		ON `schedules`.`id` =  `timeslots` .`schedule_id`
\n        LEFT JOIN (SELECT `booking_id`, COUNT(*) as `delegates_per_booking`
\n			FROM `plugin_ib_educate_bookings_has_delegates`
\n            WHERE `plugin_ib_educate_bookings_has_delegates`.`deleted` = 0 AND `plugin_ib_educate_bookings_has_delegates`.`cancelled` = 0
\n            GROUP BY `booking_id`) as `delegates`
\n       ON `delegates`.`booking_id` = `bookings`.`booking_id`
\n
\n    LEFT JOIN `plugin_courses_courses` as `courses`
\n	     ON `courses`.`id` = `schedules`.`course_id`
\n    LEFT JOIN `plugin_courses_types` as  `course_types`
\n       ON `courses`.`type_id` = `course_types`.`id`
\n    LEFT JOIN `plugin_courses_categories` as `category`
\n       ON `category`.`id` = `courses`.`category_id`
\n    LEFT JOIN `plugin_courses_locations` as `locations`
\n		ON `schedules`.`location_id` = `locations`.`id`
\n	LEFT JOIN `plugin_courses_locations` as `parent_locations`
\n		ON `locations`.`parent_id` = `parent_locations`.`id`
\n	 LEFT JOIN `plugin_courses_counties` as `counties`
\n      ON `locations`.`county_id` = `counties`.`id`
\n    LEFT JOIN `plugin_courses_counties` as `parent_counties`
\n       ON `parent_locations`.`county_id` = `parent_counties`.`id`
\n    INNER JOIN `plugin_contacts3_contacts` as `contacts`
\n       ON `bookings`.`contact_id` = `contacts`.`id`
\n    LEFT  JOIN `plugin_contacts3_contact_has_notifications` `email_1`
\n       ON `contacts`.`notifications_group_id` = `email_1`.`group_id`  AND `email_1`.`notification_id`  = 1 AND `email_1`.`deleted` = 0
\n    LEFT JOIN `plugin_contacts3_relations` as `relations`
\n       ON  `relations`.`child_id` = `contacts`.`id`
\n    LEFT JOIN `plugin_contacts3_contacts`  as `organisation`
\n       ON `relations` .`parent_id` = `organisation`.`id`
\n    LEFT JOIN `plugin_contacts3_organisations` as `organisation_data`
\n       ON `organisation`.`id` = `organisation_data`.`contact_id`
\n    LEFT JOIN `plugin_contacts3_organisation_industries` as `industry`
\n       ON `organisation_data`.`organisation_industry_id` = `industry`.`id`
\n    LEFT JOIN `plugin_contacts3_organisation_sizes` as `organisation_size`
\n       ON `organisation_data`.`organisation_size_id` = `organisation_size`.`id`
\n    LEFT JOIN `plugin_contacts3_contact_has_tags` as `has_tags`
\n       ON `contacts`.`id` = `has_tags`.`contact_id`
\n       AND `has_tags`.`tag_id` = (SELECT `id` FROM `plugin_contacts3_tags` WHERE `name` =''special_member'')
\n	LEFT JOIN `plugin_contacts3_job_functions` `job_functions`
\n		ON `contacts`.`job_function_id` = `job_functions`.`id`
\n    LEFT JOIN `engine_lookup_values` as `how_did_you_hear_val`
\n		ON
\n       `how_did_you_hear_val`.`value` = `bookings`.`how_did_you_hear`
\n                AND `how_did_you_hear_val`.`field_id` = (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = ''How did you hear'')
\n   LEFT JOIN `engine_lookup_values` as `how_did_you_hear_id`
\n		ON
\n        `how_did_you_hear_id`.`id` = `bookings`.`how_did_you_hear`
\n                AND `how_did_you_hear_id`.`field_id` = (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = ''How did you hear'')
\n    GROUP BY  `bookings`.`booking_id`
\n    HAVING
\n  (
\n    CONCAT(
\n      \'{!DASHBOARD-FROM!}\', " 00:00:00"
\n    ) <= MAX(
\n      `bookings`.`created_date`
\n    )
\n    OR \'\' = \'{!DASHBOARD-FROM!}\'
\n  )
\n  AND (
\n    CONCAT(\'{!DASHBOARD-TO!}\', " 23:59:59") >= MAX(
\n     `bookings`.`created_date`
\n    )
\n    OR \'\' = \'{!DASHBOARD-TO!}\'
\n  );\n'
WHERE
        `name` = 'How did you hear about us';;

INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'How did you hear about us' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, `is_multiselect` = 0;;
INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'How did you hear about us' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-TO',   `value` = '', `delete` = 0, `is_multiselect` = 0;;
