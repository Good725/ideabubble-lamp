/*
ts:2016-04-27 16:55:00
*/

INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Shop1 - b', 'b', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`) VALUES
(
  '28',
  '28',
  (SELECT `id` FROM `engine_site_templates`  WHERE `stub` = 'b' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP
);