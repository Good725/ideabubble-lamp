/*
ts:2016-07-11 12:35:00
*/

SET @kes_1762_user_id = (SELECT `id` FROM `engine_users` WHERE `email` ='super@ideabubble.ie' AND `deleted`=0);
INSERT INTO `plugin_custom_scroller_sequences`
(`title`,        `animation_type`, `order_type`, `first_item`, `rotating_speed`, `timeout`, `pagination`, `controls`, `plugin`, `date_created`,    `date_modified`,   `created_by`,      `modified_by`,     `publish`, `deleted`) VALUES
('RAB',          'horizontal',     'ascending',  '1',          '2000',           '8000',    '1',          '1',        '20',     CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
('RAB Limerick', 'horizontal',     'ascending',  '1',          '2000',           '8000',    '1',          '1',        '20',     CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
('RAB Ennis',    'horizontal',     'ascending',  '1',          '2000',           '8000',    '1',          '1',        '20',     CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0');

SET @kes_1762_rab_banner_id   = (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'RAB'          AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1);
SET @kes_1762_l_rab_banner_id = (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'RAB Limerick' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1);
SET @kes_1762_e_rab_banner_id = (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'RAB Ennis'    AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1);

INSERT INTO `plugin_custom_scroller_sequence_items`
(`sequence_id`,             `image`,                            `image_location`, `order_no`, `date_created`,    `date_modified`,   `created_by`,      `modified_by`,     `publish`, `deleted`) VALUES
(@kes_1762_rab_banner_id,   'Students 2014.jpg',                'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_rab_banner_id,   'Kilmartin-homebanner02.jpg',       'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_rab_banner_id,   'students-jumping1.jpg',            'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_rab_banner_id,   'students-voucher-web-banner2.jpg', 'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_rab_banner_id,   'Kilmartin-homebanner01.jpg',       'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_l_rab_banner_id, 'Students 2014.jpg',                'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_l_rab_banner_id, 'Kilmartin-homebanner02.jpg',       'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_l_rab_banner_id, 'students-jumping1.jpg',            'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_l_rab_banner_id, 'students-voucher-web-banner2.jpg', 'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_l_rab_banner_id, 'Kilmartin-homebanner01.jpg',       'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_e_rab_banner_id, 'Students 2014.jpg',                'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_e_rab_banner_id, 'Kilmartin-homebanner02.jpg',       'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_e_rab_banner_id, 'students-jumping1.jpg',            'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_e_rab_banner_id, 'students-voucher-web-banner2.jpg', 'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0'),
(@kes_1762_e_rab_banner_id, 'Kilmartin-homebanner01.jpg',       'banners',        '0',        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @kes_1762_user_id, @kes_1762_user_id, '1',       '0');

UPDATE IGNORE `plugin_pages_pages`
SET
  `banner_photo` = CONCAT('3|', `id`, '|banners|', (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'RAB' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1)),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = @kes_1762_user_id
WHERE `name_tag` = 'rab.html' AND `deleted` = 0;


UPDATE IGNORE `plugin_pages_pages`
SET
  `banner_photo` = CONCAT('3|', `id`, '|banners|', (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'RAB Limerick' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1)),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = @kes_1762_user_id
WHERE `name_tag` = 'rab-limerick.html' AND `deleted` = 0;


UPDATE IGNORE `plugin_pages_pages`
SET
  `banner_photo` = CONCAT('3|', `id`, '|banners|', (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'RAB Ennis' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1)),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = @kes_1762_user_id
WHERE `name_tag` = 'rab-ennis.html' AND `deleted` = 0;
