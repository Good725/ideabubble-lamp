/*
ts:2016-10-04 12:06:00
*/

ALTER TABLE `plugin_events_events`
  ADD COLUMN `seo_keywords` VARCHAR(500) NULL AFTER `image_media_id`,
  ADD COLUMN `seo_description` VARCHAR(500) NULL AFTER `seo_keywords`,
  ADD COLUMN `footer` VARCHAR(500) NULL AFTER `seo_description`,
  ADD COLUMN `x_robots_tag` VARCHAR(25) NULL AFTER `footer`;
