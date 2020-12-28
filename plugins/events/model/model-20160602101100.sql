/*
ts:2016-06-02 10:11:00
*/

ALTER TABLE plugin_events_events ADD COLUMN `display_map` TINYINT DEFAULT 0;
ALTER TABLE plugin_events_organizers ADD COLUMN `website` VARCHAR(255);
