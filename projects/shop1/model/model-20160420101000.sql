/*
ts:2016-04-20 10:10:00
*/
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '29', '29', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'accommodation';