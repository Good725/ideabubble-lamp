/*
ts:2018-06-18 10:55:00
*/

/* Add the news layout for the educate (04) template, if it does not already exist. */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'news',
  (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04' AND `deleted` = 0),
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
FROM
  `engine_site_templates`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'news' AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1) AND `deleted` = 0)
LIMIT 1
;

/* Add the news page, if it does not already exist. */
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'news',
  'news',
  '<h1>News</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
FROM
  `plugin_pages_layouts`
WHERE
  `layout` = 'news'
AND NOT EXISTS
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('news.html', 'news') AND `deleted` = 0)
LIMIT 1
;