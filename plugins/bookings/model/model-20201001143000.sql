/*
ts:2020-10-01 16:30:00
*/

UPDATE `plugin_reports_reports` SET `sql`='SELECT \n `contact`.`id` AS \'ContactID\', \n  `course`.`title` AS \'Course\', \n  `schedule`.`name` AS \'Schedule\', \n  IF (JSON_UNQUOTE(`application`.`data` -> \'$.first_name\') IS NULL , \n	 CONCAT_WS(\'  \', `contact`.`first_name`, `contact`.`last_name`), \n     CONCAT_WS(\'  \', \n \n          JSON_UNQUOTE(`application`.`data` -> \'$.first_name\'), \n \n           JSON_UNQUOTE(`application`.`data` -> \'$.last_name\')) \n) \n  AS \'Name\', \nCONCAT( \n  \'<span class=\"hidden\">\', \n    STR_TO_DATE( \n      JSON_UNQUOTE(`application`.`data` -> \'$.dob\'), \n      \'%d/%m/%Y\' \n    ), \n    \'</span>\', \n    DATE_FORMAT( \n      STR_TO_DATE( \n        JSON_UNQUOTE(`application`.`data` -> \'$.dob\'), \n        \'%d/%m/%Y\' \n      ), \n      \'%e %M %Y\' \n    ) \n  ) AS \'DOB\', \nJSON_UNQUOTE( \n    `application`.`data` -> \'$.gender\' \n  ) AS \'Gender\', \n  JSON_UNQUOTE( \n    `application`.`data` -> \'$.nationality\' \n  ) AS \'Nationality\', \nJSON_UNQUOTE(`application`.`data` -> \'$.pps\') AS \'PPS\', \nCONCAT_WS( \n    \', \', \n    JSON_UNQUOTE( \n      `application`.`data` -> \'$.address1\' \n   ), \n    JSON_UNQUOTE( \n      `application`.`data` -> \'$.address2\' \n    ), \n    JSON_UNQUOTE( \n     `application`.`data` -> \'$.address3\' \n    ), \n    JSON_UNQUOTE(`application`.`data` -> \'$.city\'), \n    JSON_UNQUOTE( \n      `application`.`data` -> \'$.country\' \n    ) \n  ) AS \'Address\', \n  JSON_UNQUOTE(`application`.`data` -> \'$.city\') as \'City\', \n `county`.`name` as \'County\', \n  JSON_UNQUOTE( \n    `application`.`data` -> \'$.phone\' \n  ) AS \'Phone\', \n  JSON_UNQUOTE( \n    `application`.`data` -> \'$.email\' \n  ) AS \'Email\', \nJSON_UNQUOTE( \n    `application`.`data` -> \'$.schedule_name\' \n  ) AS \'TU Dublin Code\', \n IF( `application_status`.`title` IS NULL OR  `application_status`.`title` != \'Completed\',  \'\' , DATE_FORMAT(MAX(`application_history`.`timestamp`), \'%Y-%m-%d %H:%i:%s\')) \n   AS \'Application Submitted\', \n CASE \n WHEN `application_status`.`title` IS NULL OR  `application_status`.`title` = \'Enquiry\' \n THEN   \'Not Submitted\'  WHEN `application_status`.`title` = \'Completed\' THEN \'Submitted\' ELSE `application_status`.`title` END \n   AS \'Application Status\', \nJSON_UNQUOTE( \n    `application`.`data` -> \'$.highest_qualification\' \n  ) AS \'Highest Qualification\', \nJSON_UNQUOTE( \n    `application`.`data` -> \'$.employment_history\' \n  ) AS \'Employment History\', \nJSON_UNQUOTE( \n    `application`.`data` -> \'$.other_information\' \n  ) AS \'Other Information\', \nJSON_UNQUOTE( \n    `application`.`data` -> \'$.declaration\' \n  ) AS \'Declaration\' \nFROM \n  `plugin_ib_educate_bookings_has_applications_history` `application_history` \n  LEFT JOIN  `plugin_ib_educate_bookings_has_applications` `application` \n			ON `application_history`.`booking_id` = `application`.`booking_id` \n   LEFT JOIN \n	       `plugin_ib_educate_bookings_status` `application_status` \n           ON `application`.`status_id` = `application_status`.`status_id` \n \n  LEFT JOIN `plugin_courses_schedules` `schedule` ON JSON_UNQUOTE( \n    `application`.`data` -> \'$.schedule_id\' \n  ) = `schedule`.`id` \n  INNER JOIN `plugin_contacts3_contacts` `contact`  ON application.delegate_id = `contact`.`id` \n  LEFT JOIN `plugin_courses_counties` `county` ON JSON_UNQUOTE( \n \n    `application`.`data` -> \'$.county\' \n \n  ) = `county`.`id` \n  LEFT JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id` \n  LEFT JOIN `plugin_courses_categories` `category` ON `course`.`category_id` = `category`.`id` \n  LEFT JOIN `plugin_ib_educate_bookings` `booking` ON `application`.`booking_id` = `booking`.`booking_id` \n  LEFT JOIN `plugin_ib_educate_booking_items` `item` ON `item`.`booking_id` = `booking`.`booking_id` \n  LEFT JOIN `plugin_courses_schedules_events` `event` ON `item`.`period_id` = `event`.`id` \n  LEFT JOIN `plugin_courses_locations` `sublocation` ON `schedule`.`location_id` = `sublocation`.`id` \n  LEFT JOIN `plugin_courses_locations` `location` ON `sublocation`.`parent_id` = `location`.`id` \nWHERE \n   ( \n    `category`.`id` = \'{!Category!}\' \n    OR \'{!Category!}\' = \'\' \n  ) \n  AND ( \n    `course`.`id` = \'{!Course!}\' \n    OR \'{!Course!}\' = \'\' \n  ) \n  AND ( \n    `schedule`.`id` = \'{!Schedule!}\' \n    OR \'{!Schedule!}\' = \'\' \n  ) \n    AND ( \n    `location`.`id` = \'{!Location!}\' \n    OR `sublocation`.`id` = \'{!Location!}\' \n    OR \'{!Location!}\' = \'\' \n ) \n AND ( \n `application_status`.`status_id` IN(\'{!Status!}\') \n  OR \'{!Status!}\' = \'\' \n) \n \nGROUP BY \n  `booking`.`booking_id`,\r\n	application.delegate_id\n HAVING (CONCAT(\'{!DASHBOARD-FROM!}\', \" 00:00:00\")  <= MAX(`application_history`.`timestamp`) OR \'\' = \'{!DASHBOARD-FROM!}\') \n    AND (CONCAT(\'{!DASHBOARD-TO!}\', \" 23:59:59\")   >= MAX(`application_history`.`timestamp`) OR \'\' = \'{!DASHBOARD-TO!}\') \nORDER BY \n  `booking`.`created_date` DESC' WHERE (`name`='TU application');
