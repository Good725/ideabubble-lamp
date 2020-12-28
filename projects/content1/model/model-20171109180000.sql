/*
ts:2017-11-09 18:00:00
*/
INSERT INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Content1 - 03', '03', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`) VALUES
('21', '21', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '03'), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `date_created`, `date_modified`) VALUES ('testimonials', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Insert the "testimonials" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'testimonials',
  'Testimonials',
  '<h1>Testimonials</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
FROM `plugin_pages_layouts`
WHERE `layout` = 'testimonials'
AND NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('testimonials.html', 'testimonials') AND `deleted` = 0);
