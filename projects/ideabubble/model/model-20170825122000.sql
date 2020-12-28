/*
ts:2017-08-25 12:20:00
*/

UPDATE
  `plugin_media_shared_media_photo_presets`
SET
  `height_large`  = '130',
  `width_large`   = '295',
  `action_large`  = 'crop',
  `thumb`         = 0,
  `height_thumb`  = '65',
  `width_thumb`   = '148',
  `action_thumb`  = 'fit',
  `date_created`  = CURRENT_TIMESTAMP,
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `title` = 'testimonials'
;