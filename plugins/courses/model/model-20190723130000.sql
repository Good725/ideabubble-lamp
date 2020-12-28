/*
ts:2019-07-23 13:00:00
*/
ALTER TABLE `plugin_courses_schedules`
ADD COLUMN  `is_group_booking`     TINYINT(1) NULL AFTER `display_timeslots_on_frontend`,
ADD COLUMN  `allow_purchase_order` TINYINT(1) NULL AFTER `is_group_booking`;
