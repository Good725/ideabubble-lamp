/*
ts:2018-10-04 17:00:00
*/

/* Add the "landing page" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'landing_page',
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
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'landing_page' AND `deleted` = 0)
LIMIT 1
;

/* Add the "landing page"-banner preset, if it does not already exist  */
INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Landing page banner',
  'banners',
  '1920',
  '700',
  'fith',
  '1',
  '960',
  '350',
  'fith',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM
  `plugin_media_shared_media_photo_presets`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` = 'Landing page banner' AND `deleted` = 0)
LIMIT 1
;