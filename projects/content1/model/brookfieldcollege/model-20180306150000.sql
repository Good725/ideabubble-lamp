/*
ts:2018-03-06 15:00:00
*/

UPDATE `engine_settings` SET `value_dev` = 'Brookfield College', `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'addres_line_1';
UPDATE `engine_settings` SET `value_dev` = 'Monavalley',         `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'addres_line_2';
UPDATE `engine_settings` SET `value_dev` = 'Tralee, Co. Kerry',  `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'addres_line_3';

UPDATE `engine_settings` SET `value_dev` = '066 7145896',               `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'telephone';
UPDATE `engine_settings` SET `value_dev` = '066 7145897',               `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'fax';
UPDATE `engine_settings` SET `value_dev` = 'info@brookfieldcollege.ie', `value_test` = `value_dev`, `value_stage` = `value_dev`, `value_live` = `value_dev` WHERE `variable` = 'email';


SELECT `id` INTO @rc_58_super_id_3 FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0;
INSERT INTO `plugin_menus` (`category`, `title`, `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
('header', 'Find Courses', '', '', '0', '0', '1', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id_2, @rc_58_super_id_3, '_self', '0'),
('header', 'Call Me Back', '', '', '0', '0', '2', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @rc_58_super_id_2, @rc_58_super_id_3, '_self', '0');
