/*
ts:2018-03-06 15:00:00
*/


INSERT INTO `plugin_menus`
(`category`,      `title`,      `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,  `date_entered`,    `menus_target`) VALUES
('footer_bottom', 'Visa',       '0',        '',         '0',       '0',         '1',          '1',       '0',      CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('footer_bottom', 'MasterCard', '0',        '',         '0',       '0',         '1',          '1',       '0',      CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self');


-- footer stats
SELECT `id` INTO @rc_58_super_id     FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0;
SELECT `id` INTO @rc_58_static_panel FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0;

INSERT INTO
  `plugin_panels` (`title`, `position`, `type_id`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES
  ('Students',  'footer', @rc_58_static_panel, '<h2>400</h2>  <h3>Students</h3>   <p>attending daily</p> ', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id, @rc_58_super_id, '1', '0'),
  ('Teachers',  'footer', @rc_58_static_panel, '<h2>45</h2>   <h3>Teachers</h3>   <p>in the school</p> ',   CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id, @rc_58_super_id, '1', '0'),
  ('Rooms',     'footer', @rc_58_static_panel, '<h2>30</h2>   <h3>Rooms</h3>      <p>for students</p> ',    CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id, @rc_58_super_id, '1', '0'),
  ('Locations', 'footer', @rc_58_static_panel, '<h2>2</h2>    <h3>Locations</h3>  <p>in Ireland</p> ',      CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id, @rc_58_super_id, '1', '0'),
  ('Subjects',  'footer', @rc_58_static_panel, '<h2>48</h2>   <h3>Subjects</h3>   <p>taught each day</p> ', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id, @rc_58_super_id, '1', '0');

-- addresses
UPDATE `engine_settings` SET `value_dev` = 'LIMERICK',         `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'addres_line_1';
UPDATE `engine_settings` SET `value_dev` = '83 O’Connell St',  `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'addres_line_2';
UPDATE `engine_settings` SET `value_dev` = 'Limerick',         `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'addres_line_3';

UPDATE `engine_settings` SET `value_dev` = 'ENNIS',            `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'address2_line_1';
UPDATE `engine_settings` SET `value_dev` = '6A Bindon Street', `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'address2_line_2';
UPDATE `engine_settings` SET `value_dev` = 'Ennis',            `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'address2_line_3';

-- header buttons
SELECT `id` INTO @rc_58_super_id_2    FROM `engine_users`       WHERE `email`    = 'super@ideabubble.ie'                  AND `deleted` = 0;
SELECT `id` INTO @rc_58_callback_id   FROM `plugin_pages_pages` WHERE `name_tag` IN ('call-me-back', 'call-me-back.html') AND `deleted` = 0 LIMIT 1;
SELECT `id` INTO @rc_58_pay_online_id FROM `plugin_pages_pages` WHERE `name_tag` IN ('pay-online',   'pay-online.html'  ) AND `deleted` = 0 LIMIT 1;
INSERT INTO `plugin_menus` (`category`, `title`, `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
('header', 'Call Me Back', @rc_58_callback_id,    '', '0', '0', '1', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id_2, @rc_58_super_id_2, '_self', '0'),
('header', 'Pay Now',      @rc_58_pay_online_id,  '', '0', '0', '2', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id_2, @rc_58_super_id_2, '_self', '0');

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'right',
  `value_test`  = 'right',
  `value_stage` = 'right',
  `value_live`  = 'right'
WHERE
  `variable` = 'content_location'
;
