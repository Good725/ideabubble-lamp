/*
ts:2016-05-11 13:00:00
*/

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Course Banners',
  'courses',
  '360',
  '1200',
  'fit',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

ALTER IGNORE TABLE `plugin_courses_courses` ADD COLUMN `banner` VARCHAR(255) NULL DEFAULT NULL  AFTER `description` ;

UPDATE IGNORE `plugin_media_shared_media_photo_presets`
SET `thumb`='1', `height_thumb`='120', `width_thumb`='400', `action_thumb`='fit', `date_modified`=CURRENT_TIMESTAMP
WHERE `title`='Course Banners';
