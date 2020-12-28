/*
ts:2016-08-22 09:09:00
*/

ALTER TABLE plugin_events_events MODIFY COLUMN status ENUM('Live','Cancelled','Postponed','Inappropriate','Sale Ended','Draft') DEFAULT 'Sale Ended';
