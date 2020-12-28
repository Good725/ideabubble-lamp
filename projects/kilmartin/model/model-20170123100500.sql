/*
ts:2017-01-23 10:05:00
*/
-- Create the layout
INSERT INTO `plugin_pages_layouts` (`layout`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'students_absence_notes',
  '0',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

-- Create the page with the layout
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'students_absence_notes',
  'Students_absence_notes',
  ' ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'students_absence_notes' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);
