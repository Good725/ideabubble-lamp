/*
ts:2016-02-05 17:50:00
*/
INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`,                   `directory`,  `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`,    `date_modified`,   `created_by`, `modified_by`, `publish`, `deleted`) SELECT
 'Content Banners (wide)',  'banners',    '450',          '0',           'fith',         '1',     '270',          '0',           'fith',         CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`,         `id`,          '1',       '0'
FROM `users` WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`,                   `directory`,  `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`,    `date_modified`,   `created_by`, `modified_by`, `publish`, `deleted`) SELECT
 'Content Banners',         'banners',    '0',            '1900',        'fitw',         '1',     '0',            '300',         'fith',         CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`,         `id`,          '1',       '0'
FROM `users` WHERE `email` = 'super@ideabubble.ie';