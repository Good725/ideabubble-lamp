/*
ts:2016-08-30 23:10:00
*/

ALTER TABLE `plugin_courses_bookings` MODIFY COLUMN `status`  ENUM('Enquiry','Confirmed','Cancelled','Processing','Pending') NOT NULL;
ALTER TABLE `plugin_courses_bookings_has_schedules` MODIFY COLUMN `status`  ENUM('Enquiry','Confirmed','Cancelled','Processing','Pending') NOT NULL;


