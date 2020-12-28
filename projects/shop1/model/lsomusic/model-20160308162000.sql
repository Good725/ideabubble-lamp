/*
ts:2016-03-08 16:20:00
*/

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('course_list');


INSERT IGNORE INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  'Course',
  'courses',
  '300',
  '300',
  'fit',
  '1',
  '152',
  '152',
  'fit',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
  );
