/*
ts:2016-08-02 11:29:00
*/
ALTER TABLE plugin_events_venues ADD COLUMN `snapchat_url` VARCHAR(255);
ALTER TABLE plugin_events_venues ADD COLUMN `instagram_url` VARCHAR(255);
ALTER TABLE plugin_events_organizers ADD COLUMN `snapchat` VARCHAR(255);
ALTER TABLE plugin_events_organizers ADD COLUMN `instagram` VARCHAR(255);
