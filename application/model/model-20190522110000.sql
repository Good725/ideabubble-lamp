/*
ts:2019-05-22 11:00:00
*/

UPDATE
  `plugin_media_shared_media_photo_presets`
SET
  `directory`     = 'mobile_banners',
  `date_modified` = CURRENT_TIMESTAMP
WHERE
  `title` = 'Home banner mobile'
;
