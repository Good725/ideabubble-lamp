/*
ts:2016-03-02 17:25:00
*/
INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Content1 - AHC', 'ahc', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '19', '19', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates` WHERE `stub` = 'ahc';
