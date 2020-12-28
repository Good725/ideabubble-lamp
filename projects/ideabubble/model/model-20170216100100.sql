/*
ts:2017-02-16 10:01:00
*/

INSERT INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('educate', 'educate', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`) VALUES
('educate', 'educate', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = 'educate'), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
