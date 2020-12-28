/*
ts:2016-09-20 01:25:00
*/

UPDATE IGNORE `plugin_pages_pages`
SET `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'newslisting' AND `deleted` = 0 LIMIT 1)
WHERE `name_tag` in ('news', 'news.html');


-- Insert the "news" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'news',
  'News',
  '<h1>News</h1>>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_layouts`
WHERE `layout` = 'newslisting'
AND NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('news.html', 'news') AND `deleted` = 0);
