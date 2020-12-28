/*
ts:2020-06-04 14:00:00
*/
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n
    `b`.`booking_id` AS `Booking ID`, \r\n
    `bs`.`title`     AS `Booking Status`, \r\n
     CONCAT(`c`.`first_name`, \' \' ,`c`.`last_name`) AS `Booking Name`,\r\n
     IFNULL(`county`.`name`, `parent_county`.`name`) as `County`,\r\n
    `tt`.`type`      AS `Booking Type`, \r\n
    `courses`.`title` AS `Course Name`,\r\n
    `s`.`name`       AS `Schedule Name`, \r\n
    `s`.`start_date` AS `Start Date`,\r\n
    `t`.`updated`    AS `Cancelled Date`,\r\n
    `t`.`amount`     AS `Transaction Amount` ,\r\n
    `navapi_events`.`remote_event_no`  as `Nav Event code`,\r\n
     GROUP_CONCAT(CONCAT_WS(\' \',`delagate_students`.`first_name`, `delagate_students`.`last_name`)) AS `Cancelled Delegates`\r\n
         FROM       `plugin_ib_educate_bookings` `b` \r\n
         INNER JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON `bhs`.`booking_id`     = `b` .`booking_id` \r\n
         INNER JOIN `plugin_courses_schedules`                `s`   ON `bhs`.`schedule_id`    = `s` .`id` \r\n
         INNER JOIN `plugin_courses_courses` `courses` ON `s`.`course_id` = `courses`.`id`\r\n
         LEFT JOIN `plugin_ib_educate_bookings_has_delegates` `delegates` ON `b`.`booking_id` = `delegates`.`booking_id`\r\n
         LEFT JOIN `plugin_contacts3_contacts` `delagate_students` ON `delegates`.`contact_id`=`delagate_students`.`id`\r\n
         LEFT JOIN `plugin_ib_educate_bookings_status`       `bs`  ON `bhs`  .`booking_status` = `bs`.`status_id` \r\n
         LEFT JOIN `plugin_contacts3_contacts`               `c`   ON `c`  .`id`             = `b` .`contact_id` \r\n
         LEFT JOIN `plugin_courses_locations`               `location` ON 	`s`.`location_id` =  `location`.`id`\r\n
         LEFT JOIN `plugin_courses_locations` as `parent_locations` ON `location`.`parent_id` = `parent_locations`.`id`\r\n
         LEFT JOIN `plugin_courses_counties`               `county` ON `location`.`county_id` =  `county`.`id`\r\n
         LEFT JOIN `plugin_courses_counties` as `parent_county` ON  `parent_locations`.`county_id` = `parent_county`.`id`\r\n
         LEFT JOIN \r\n
            (select `t`.*, `ths`.`schedule_id`\r\n
                from `plugin_bookings_transactions` `t` \r\n
                    left join `plugin_bookings_transactions_has_schedule` `ths` on `t`.`id` = `ths`.`transaction_id`) `t` \r\n
            ON `b`.`booking_id` = `t`.`booking_id` and `t`.`schedule_id` = `bhs`.`schedule_id`\r\n
         LEFT JOIN `plugin_bookings_transactions_types`      `tt`  \r\n
            ON `t`  .`type`           = `tt`.`id` \r\n
         LEFT JOIN `plugin_navapi_events` as `navapi_events` ON `s`.`id` = `navapi_events`.`schedule_id`\r\n
      WHERE `bhs`.`booking_status` = 3\r\n
       AND `b`.`modified_date` >= \'{!From!}\' \n
        AND `b`.`modified_date` < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY) \r\n
      GROUP BY `b`.`booking_id`\r\n'
  WHERE (`name`='Cancelled Bookings');

