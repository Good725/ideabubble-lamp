/*
ts:2016-08-15 17:40:00
*/
INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
('Small Banner', 'banners', '358', '494', 'fit', '0', '0', '0', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), '1', '0');


UPDATE IGNORE `plugin_pages_pages`
SET `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'home-calendar')
WHERE `name_tag` IN ('home', 'home.html');

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('course_list');

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'course-list',
  'Courses',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_list'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
('Course', 'courses', '800', '800', 'fit', '1', '300', '300', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), '1', '0');

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('course_detail');

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'course-detail',
  'Course Details',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_detail'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('course_checkout');

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'course-checkout',
  'Checkout',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_checkout'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);