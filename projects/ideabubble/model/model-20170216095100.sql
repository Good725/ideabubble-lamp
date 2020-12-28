/*
ts:2017-02-16 09:51:00
*/

INSERT INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('ideabubble', 'ideabubble', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`) VALUES
('ideabubble', 'ideabubble', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = 'ideabubble'), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
