/*
ts:2016-02-29 14:30:00
*/
INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Shop1 - Courses2', 'courses2', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '26', '26', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'courses2';
