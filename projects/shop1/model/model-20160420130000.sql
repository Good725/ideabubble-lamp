/*
ts:2016-04-20 13:00:00
*/

INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Shop1 - Tickets', 'tickets', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

UPDATE IGNORE `engine_site_themes` SET `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'tickets');