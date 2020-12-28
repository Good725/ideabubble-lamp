/*
ts:2016-01-04 16:35:00
*/
INSERT IGNORE INTO `plugin_media_shared_media_photo_presets`
(`title`,                `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`,      `date_modified`,     `publish`, `deleted`) VALUES
('Products (Portrait)',  'products',  '500',          '500',         'fith',         '1',     '180',          '180',         'fith',         CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), 1,         0),
('Products (Landscape)', 'products',  '500',          '500',         'fitw',         '1',     '180',          '180',         'fitw',         CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), 1,         0)
;