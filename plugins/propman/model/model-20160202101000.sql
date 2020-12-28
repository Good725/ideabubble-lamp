/*
ts:2016-02-02 10:10:00
*/
UPDATE IGNORE `plugins` SET `media_folder`='properties' WHERE `name`='Propman';

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`,    `directory`,  `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`,    `date_modified`,   `created_by`, `modified_by`, `publish`, `deleted`) SELECT
 'Property', 'properties', '720',          '960',         'fith',         '1',     '270',          '360',         'fith',         CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`,         `id`,          '1',       '0'
FROM `users` WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`,            `directory`,  `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`,    `date_modified`,   `created_by`, `modified_by`, `publish`, `deleted`) SELECT
 'Property Banners', 'properties', '960',          '0',           'fith',         '1',     '270',          '0',         'fith',         CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`,         `id`,          '1',       '0'
FROM `users` WHERE `email` = 'super@ideabubble.ie';
