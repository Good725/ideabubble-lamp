/*
ts:2020-06-24 23:00:00
*/

UPDATE
  `plugin_reports_reports`
SET
  `sql` = "SELECT
\n `b`.`booking_id` AS `Booking ID`, 
\n `bs`.`title`     AS `Booking Status`, 
\n CONCAT(`c`.`first_name`, ' ' ,`c`.`last_name`) AS `Booking Name`,
\n IFNULL(`county`.`name`, `parent_county`.`name`) as `County`,
\n `tt`.`type`      AS `Booking Type`, 
\n `courses`.`title` AS `Course Name`,
\n `s`.`name`       AS `Schedule Name`, 
\n `s`.`start_date` AS `Start Date`,
\n  IFNULL(`delegates`.`date_cancelled`, `t`.`updated`) AS `Cancelled Date`,
\n `t`.`amount`     AS `Transaction Amount` ,
\n `navapi_events`.`remote_event_no`  as `Nav Event code`,
\n  IFNULL(`delegates`.`cancel_reason_code`, `bhs`.`cancel_reason_code`) AS `Reason code`,
\n GROUP_CONCAT(CONCAT_WS(' ',`delegate_students`.`first_name`, `delegate_students`.`last_name`)) AS `Cancelled Delegates`
\n FROM       `plugin_ib_educate_bookings` `b` 
\n INNER JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON `bhs`.`booking_id`     = `b` .`booking_id` 
\n INNER JOIN `plugin_courses_schedules`                `s`   ON `bhs`.`schedule_id`    = `s` .`id` 
\n INNER JOIN `plugin_courses_courses` `courses` ON `s`.`course_id` = `courses`.`id`
\n LEFT JOIN `plugin_ib_educate_bookings_has_delegates` `delegates` ON `b`.`booking_id` = `delegates`.`booking_id`
\n LEFT JOIN `plugin_contacts3_contacts` `delegate_students` ON `delegates`.`contact_id`=`delegate_students`.`id`
\n LEFT JOIN `plugin_ib_educate_bookings_status`       `bs`  ON `bhs`  .`booking_status` = `bs`.`status_id`
\n LEFT JOIN `plugin_contacts3_contacts`               `c`   ON `c`  .`id`             = `b` .`contact_id`
\n LEFT JOIN `plugin_courses_locations`               `location` ON 	`s`.`location_id` =  `location`.`id`
\n LEFT JOIN `plugin_courses_locations` as `parent_locations` ON `location`.`parent_id` = `parent_locations`.`id`
\n LEFT JOIN `plugin_courses_counties`               `county` ON `location`.`county_id` =  `county`.`id`
\n LEFT JOIN `plugin_courses_counties` as `parent_county` ON  `parent_locations`.`county_id` = `parent_county`.`id`
\n LEFT JOIN 
\n (SELECT `t`.*, `ths`.`schedule_id`
\n FROM `plugin_bookings_transactions` `t`
\n LEFT JOIN `plugin_bookings_transactions_has_schedule` `ths` on `t`.`id` = `ths`.`transaction_id`) `t`
\n ON `b`.`booking_id` = `t`.`booking_id` and `t`.`schedule_id` = `bhs`.`schedule_id`
\n LEFT JOIN `plugin_bookings_transactions_types`      `tt`  
\n ON `t`  .`type`           = `tt`.`id` 
\n LEFT JOIN `plugin_navapi_events` as `navapi_events` ON `s`.`id` = `navapi_events`.`schedule_id`
\n WHERE (`bs`.`title` = 'Cancelled' OR `delegates`.`cancelled` = 1)
\n AND `b`.`modified_date` >= '{!From!}' 
\n AND `b`.`modified_date` < DATE_ADD('{!To!}', INTERVAL 1 DAY)
\n  GROUP BY CONCAT(`b`.`booking_id`, '-', IFNULL(`delegates`.`date_cancelled`, ''))"
WHERE
  `name` = 'Cancelled Bookings';