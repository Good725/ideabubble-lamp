/*
ts:2016-11-30 11:00:00
*/
INSERT INTO `plugin_pages_layouts` (`layout`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'news',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
);

UPDATE `plugin_pages_pages`
SET `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news')
WHERE `name_tag` IN ('news', 'news.html', 'blog', 'blog.html');
