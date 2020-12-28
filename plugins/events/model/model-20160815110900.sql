/*
ts:2016-08-15 11:09:00
*/

ALTER TABLE plugin_events_events DROP COLUMN image_file_id;
ALTER TABLE plugin_events_events ADD COLUMN image_media_id INT;

ALTER TABLE plugin_events_venues DROP COLUMN venue_file_id;
ALTER TABLE plugin_events_venues ADD COLUMN image_media_id INT;


ALTER TABLE plugin_events_organizers DROP COLUMN profile_picture_file_id;
ALTER TABLE plugin_events_organizers DROP COLUMN banner_file_id;
ALTER TABLE plugin_events_organizers ADD COLUMN profile_media_id INT;
ALTER TABLE plugin_events_organizers ADD COLUMN banner_media_id INT;

