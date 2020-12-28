/*
ts:2015-01-01 00:01:01
*/

INSERT IGNORE INTO `plugin_panels` (`title`, `position`, `type_id`, `image`, `link_id`, `text`, `date_created`, `date_modified`)
SELECT 'Calendar', 'content_right', `id`, 0, 0, '<div class=\"calendar-panel\"> <h2>Calendar</h2>  <div id=\"panel-calendar\">&nbsp;</div> </div>', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `plugin_panels_types` WHERE `name` = 'static';

INSERT IGNORE INTO `shared_media_photo_presets` (`title`, `directory`, `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES ('Content panels', 'panels', '241', '216', 'fit', '0', '0', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

UPDATE IGNORE `shared_media_photo_presets` SET `height_large`='312' WHERE `title` = 'Banners';

