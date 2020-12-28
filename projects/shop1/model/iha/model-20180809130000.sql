/*
ts:2018-08-09 13:00:00
*/


UPDATE `engine_settings` SET `value_dev` = '04'   WHERE `variable` = 'template_folder_path';
UPDATE `engine_settings` SET `value_dev` = '40'   WHERE `variable` = 'assets_folder_path';
UPDATE `engine_settings` SET `value_dev` = 'none' WHERE `variable` = 'course_finder_mode';

UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` IN ('template_folder_path', 'assets_folder_path', 'course_finder_mode')
;

UPDATE IGNORE
  `plugin_media_shared_media_photo_presets`
SET
  `width_large`  = '330',
  `height_large` = '180'
WHERE
  `title`        = 'Home panel'
;

INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'testimonials',
  'Testimonials',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM
  `plugin_pages_layouts`
WHERE
  `layout` = 'testimonials'
AND
  `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04')
AND NOT EXISTS
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('testimonials.html', 'testimonials') AND `deleted` = 0)
;

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`,         `directory`,    `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `action_thumb`, `date_created`,      `date_modified`,     `publish`, `deleted`) VALUES
('Testimonials',  'testimonials', '240',          '227',         'fith',         '0',     '',            '',             'fith',         CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), 1,         0);

UPDATE `plugin_courses_categories` SET `publish` = '0';