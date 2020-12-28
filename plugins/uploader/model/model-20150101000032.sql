/*
ts:2015-01-01 00:00:32
*/
INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('uploader', 'Uploader', '0', '0', NULL);

UPDATE `plugins` SET icon = 'uploader.png' WHERE friendly_name = 'Uploader';
UPDATE `plugins` SET `plugins`.`order` = 99 WHERE friendly_name = 'Uploader';
