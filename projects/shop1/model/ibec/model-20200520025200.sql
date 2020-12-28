/*
ts:2020-05-02 01:12:00
*/
UPDATE `plugin_reports_reports`
   SET `sql` = 'SELECT\r\n
                `categories`.`category` as `Category`,\r\n
                `courses_types`.`type` as `Type`,\r\n
                `courses`.`title` as `Course` ,\r\n
                `schedules`.`name` as `Schedule`,\r\n
                 IFNULL(`county`.`name`, `parent_county`.`name`) as `County`,\r\n
                COUNT(`booking_schedule`.`booking_id`) as `Bookings Number`,\r\n
                IFNULL(`schedules`.`fee_amount`, 0.00) as `Fee`,\r\n
                IFNULL(`schedules`.`fee_amount`  * COUNT(`booking_schedule`.`booking_id`), 0) as `Total income lost`,\r\n
                `navapi_events`.`remote_event_no`  as `Nav Event code`,\r\n
                `schedules`.`start_date` as `StartDate`,\r\n
                `schedules`.`date_modified` as `Cancelled Date`\r\n
            FROM `plugin_courses_schedules` AS `schedules`\r\n
                INNER JOIN `plugin_courses_schedules_status` as `schedules_status`\r\n
                    ON  `schedules`.`schedule_status` = `schedules_status`.`id`\r\n
                INNER JOIN `plugin_courses_courses` AS `courses` ON `schedules`.`course_id` = `courses`.`id`\r\n
                LEFT JOIN `plugin_courses_types` AS `courses_types` ON `courses`.`type_id` = `courses_types`.`id`\r\n
                INNER JOIN `plugin_courses_categories` AS `categories` ON `courses`.`category_id` = `categories`.`id`\r\n
                LEFT JOIN `plugin_navapi_events` as `navapi_events` ON `schedules`.`id` = `navapi_events`.`schedule_id`\r\n
                LEFT JOIN `plugin_ib_educate_booking_has_schedules` as `booking_schedule` ON `booking_schedule`.`schedule_id` = `schedules`.`id`\r\n
                LEFT JOIN `plugin_ib_educate_bookings` as `bookings` ON `booking_schedule`.`booking_id` = `bookings`.`booking_id`\r\n
                LEFT JOIN `plugin_courses_locations` as `locations` ON `locations`.`id` = `schedules`.`location_id`\r\n
                LEFT JOIN `plugin_courses_locations` as `parent_locations` ON `locations`.`parent_id` = `parent_locations`.`id`\r\n
                LEFT JOIN `plugin_courses_counties` as `county` ON  `locations`.`county_id` = `county`.`id`\r\n
                LEFT JOIN `plugin_courses_counties` as `parent_county` ON  `parent_locations`.`county_id` = `parent_county`.`id`\r\n
            WHERE `schedules_status`.`title` = \'Cancelled\'\r\n
            AND (`schedules`.`start_date` < date_add(\'{!Before!}\', interval 1 day) or \'\' = \'{!Before!}\')\r\n
            AND (`schedules`.`start_date` >= \'{!After!}\' or \'\' = \'{!After!}\') and (`courses`.`id` = \'{!Course!}\' or \'\' = \'{!Course!}\')\r\n
            AND (`courses`.`category_id` = \'{!Category!}\' or \'\' = \'{!Category!}\')\r\n
            GROUP BY `schedules`.`id`\r\n'
WHERE `name` = 'Cancelled Schedules';
