/*
ts:2016-03-16 17:37:00
*/

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `publish`, `deleted`, `created_by`, `modified_by`, `date_created`, `date_modified`) VALUES
('27','27',
(SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'course' LIMIT 1),
'1',
'0',
(SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
(SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
);
