/*
ts:2018-09-26 08:43:00
*/

ALTER TABLE `plugin_ib_educate_bookings` ADD INDEX `contact_id` (`contact_id`) ;
ALTER TABLE `plugin_ib_educate_booking_has_schedules`
ADD INDEX `booking_id` (`booking_id`) ,
ADD INDEX `schedule_id` (`schedule_id`) ;


