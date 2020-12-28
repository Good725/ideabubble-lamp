/*
ts:2016-06-24 14:22:00
*/

UPDATE `engine_settings`
SET `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1'
WHERE `variable`='images_in_news_feed';

UPDATE IGNORE
  `plugin_media_shared_media_photo_presets`
SET
  `height_large`  = '500',
  `width_large`   = '500',
  `height_thumb`  = '250',
  `width_thumb`   = '250',
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE
  `title` = 'News';
