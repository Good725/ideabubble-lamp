/*
ts:2016-03-23 15:00:00
*/
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  `home_page`,
  'Home',
   CURRENT_TIMESTAMP,
   CURRENT_TIMESTAMP,
   (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   1,
   0,
   1,
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'Home' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
FROM (SELECT 'home.html' AS `home_page`) `temp`
WHERE NOT EXISTS(SELECT 1 FROM `plugin_pages_pages` WHERE `name_tag` IN ('home', 'home.html') AND `deleted` = 0);