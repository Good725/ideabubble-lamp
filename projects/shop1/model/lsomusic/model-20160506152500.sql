/*
ts:2016-05-06 15:25:00
*/

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('course_category');


INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'courses.html',
  'Courses',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_category' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);
