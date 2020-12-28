/*
ts:2016-04-16 20:05:00
*/

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Avatars',
  'avatars',
  '100',
  '100',
  'fit',
  '1',
  '40',
  '40',
  'fit',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);
