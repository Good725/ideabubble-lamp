/*
ts:2016-08-24 00:40:00
*/

ALTER TABLE plugin_events_organizers DROP COLUMN is_primary;
ALTER TABLE plugin_events_events_has_organizers ADD COLUMN is_primary TINYINT(1) NOT NULL DEFAULT 0;
