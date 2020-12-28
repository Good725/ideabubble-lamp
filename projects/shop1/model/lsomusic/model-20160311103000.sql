/*
ts:2016-03-11 10:30:00
*/

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'course_detail',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'course-detail.html',
  'Course Details',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_detail'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);


INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'checkout',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

UPDATE IGNORE `plugin_pages_pages`
SET `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'checkout' ORDER BY `id` DESC LIMIT 1)
WHERE name_tag = 'checkout.html';