/*
ts:2020-03-06 16:00:00
*/

UPDATE `plugin_ib_educate_booking_items` SET `timeslot_status` = null WHERE `timeslot_status` = 'Plan';

-- Remove 'Plan' option
ALTER TABLE `plugin_ib_educate_booking_items`
CHANGE COLUMN `timeslot_status` `timeslot_status` SET('Present', 'Late', 'Early Departures', 'Paid', 'Temporary Absence', 'Absent') NULL DEFAULT NULL ;
