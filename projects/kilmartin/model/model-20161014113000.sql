/*
ts:2016-10-14 11:30:00
*/

INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `publish`, `deleted`, `date_created`, `date_modified`) VALUES
('KES1', 'kes1', 'website', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`) VALUES
(
  'KES1',
  'kes1',
  (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'kes1' LIMIT 1),
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP
);


INSERT INTO `plugin_media_shared_media_photo_presets`
(`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Home panel',
  'panels',
  '124',
  '330',
  'fith',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);


INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`,`date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Home banner',
  'banners',
  '300',
  '1920',
  'fith',
  '1',
  '150',
  '960',
  'fith',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);
