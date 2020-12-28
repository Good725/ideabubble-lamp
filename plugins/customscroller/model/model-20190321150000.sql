/*
ts:2019-03-21 15:00:00
*/

-- Add the column for the mobile banner image
ALTER TABLE `plugin_custom_scroller_sequence_items`
ADD COLUMN `mobile_image` VARCHAR(255) NULL DEFAULT NULL AFTER `image`;


/* Add the "home banner mobile" preset, if it does not already exist */
INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Home banner mobile',
  'banners',
  '767',
  '650',
  'fith',
  '0',
  '',
  '',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM
  `plugin_media_shared_media_photo_presets`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` = 'Home banner mobile' AND `deleted` = 0)
LIMIT 1
;