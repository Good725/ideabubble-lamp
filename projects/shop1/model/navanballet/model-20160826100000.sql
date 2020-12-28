/*
ts:2016-08-26 10:00:00
*/

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'photo-permission',
  'Photo Usage Permission',
  '<h1>Photo Usage Permission</h1> <p>From time to time, Navan School of Ballet will be taking photographs/video from class, rehearsals and shows. I grant permission for photography/video arising from class activities and events to be used on Navan School of Ballet website and other print and social media.</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);