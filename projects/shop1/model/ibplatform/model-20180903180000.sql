/*
ts:2018-09-03 18:00:00
*/


INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Home banner large',
  'banners',
  '560',
  '1920',
  'fith',
  '1',
  '280',
  '960',
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
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` = 'Home banner large' AND `deleted` = 0)
LIMIT 1
;

UPDATE `engine_settings` SET `value_dev` = '',  `value_test` = '',  `value_stage` = '', `value_live` = '' WHERE `variable` = 'company_slogan';