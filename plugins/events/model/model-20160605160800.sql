/*
ts:2016-06-05 16:08:00
*/

ALTER TABLE plugin_events_events ADD COLUMN `status` ENUM('Wait', 'Live', 'Cancelled', 'Postponed', 'Inappropriate') DEFAULT 'Wait';
ALTER TABLE plugin_events_events ADD COLUMN `status_reason` TEXT;
