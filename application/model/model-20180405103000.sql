/*
ts:2018-04-05 10:30:00
*/
SELECT `id` INTO @bc73_super_id FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1;

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
  SELECT
    'News', 'news', '280', '320', 'fith', '0', '0', '0', 'fith', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @bc73_super_id, @bc73_super_id, '1', '0'
  FROM
    `plugin_media_shared_media_photo_presets`
  WHERE NOT EXISTS
    (SELECT `id` FROM `plugin_media_shared_media_photo_presets` WHERE `directory` = 'news' AND `deleted` = 0)
  LIMIT 1
;
