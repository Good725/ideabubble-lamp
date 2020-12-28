/*
ts:2019-01-09 17:15:00
*/
UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `sql`           = "SELECT
\n    `b`.`booking_id` AS `Booking ID`,
\n    `bs`.`title`     AS `Booking Status`,
\n    CONCAT(`c`.`first_name`, ' ' ,`c`.`last_name`) AS `Booking Name`,
\n    `tt`.`type`      AS `Booking Type`,
\n    `s`.`name`       AS `Schedule Name`,
\n    `t`.`updated`    AS `Cancelled Date`,
\n    `t`.`amount`     AS `Transaction Amount`
\nFROM       `plugin_ib_educate_bookings` `b`
\nLEFT  JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON `bhs`.`booking_id`     = `b` .`booking_id`
\nLEFT  JOIN `plugin_courses_schedules`                `s`   ON `bhs`.`schedule_id`    = `s` .`id`
\nINNER JOIN `plugin_ib_educate_bookings_status`       `bs`  ON `b`  .`booking_status` = `bs`.`status_id`
\nINNER JOIN `plugin_contacts3_contacts`               `c`   ON `c`  .`id`             = `b` .`contact_id`
\nINNER JOIN `plugin_bookings_transactions`            `t`   ON `t`  .`booking_id`     = `b` .`booking_id`
\nINNER JOIN `plugin_bookings_transactions_types`      `tt`  ON `t`  .`type`           = `tt`.`id`
\nWHERE `b`.`booking_status` = 3
\nAND `t`.`updated` >= '{!From!}' AND `t`.`updated` < DATE_ADD('{!To!}', INTERVAL 1 DAY)"
WHERE
  `name` = 'Cancelled Bookings';

UPDATE `plugin_reports_reports`
  SET `sql`= '\r\nSELECT \r\n    `b`.`booking_id` AS `Booking ID`, \r\n    `bs`.`title`     AS `Booking Status`, \r\n    CONCAT(`c`.`first_name`, \' \' ,`c`.`last_name`) AS `Booking Name`, \r\n    `tt`.`type`      AS `Booking Type`, \r\n    `s`.`name`       AS `Schedule Name`, \r\n    `t`.`updated`    AS `Cancelled Date`, \r\n    `t`.`amount`     AS `Transaction Amount` \r\nFROM       `plugin_ib_educate_bookings` `b` \r\nINNER JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON `bhs`.`booking_id`     = `b` .`booking_id` \r\nINNER JOIN `plugin_courses_schedules`                `s`   ON `bhs`.`schedule_id`    = `s` .`id` \r\nLEFT JOIN `plugin_ib_educate_bookings_status`       `bs`  ON `bhs`  .`booking_status` = `bs`.`status_id` \r\nLEFT JOIN `plugin_contacts3_contacts`               `c`   ON `c`  .`id`             = `b` .`contact_id` \r\nLEFT JOIN (select t.*, ths.schedule_id from plugin_bookings_transactions t left join plugin_bookings_transactions_has_schedule ths on t.id = ths.transaction_id) t ON b.booking_id = t.booking_id and t.schedule_id = bhs.schedule_id\r\nLEFT JOIN `plugin_bookings_transactions_types`      `tt`  ON `t`  .`type`           = `tt`.`id` \r\nWHERE `bhs`.`booking_status` = 3 \r\nAND `b`.`modified_date` >= \'{!From!}\' AND `b`.`modified_date` < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY)'
  WHERE `name` = 'Cancelled Bookings';
