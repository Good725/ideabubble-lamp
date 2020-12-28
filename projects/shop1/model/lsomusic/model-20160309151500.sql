/*
ts:2016-03-09 15:15:00
*/
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'search-results',
  'Search Results',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_list'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);
