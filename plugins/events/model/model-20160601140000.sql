/*
ts:2016-06-01 14:00:00
*/

ALTER TABLE plugin_events_events MODIFY COLUMN `timezone` VARCHAR(100);
ALTER TABLE plugin_events_events ADD COLUMN `display_start` TINYINT DEFAULT 1;
ALTER TABLE plugin_events_events ADD COLUMN `display_end` TINYINT DEFAULT 1;
ALTER TABLE plugin_events_events ADD COLUMN `display_timezone` TINYINT DEFAULT 1;
ALTER TABLE plugin_events_events ADD COLUMN `other_times` TEXT;

ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `channel` VARCHAR(10);
