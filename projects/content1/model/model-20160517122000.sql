/*
ts:2016-05-17 12:20:00
*/
INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Content1 - PHRC', 'phrc', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`) VALUES
('20', '20', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = 'phrc'), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
