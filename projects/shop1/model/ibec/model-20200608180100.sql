/*
ts:2020-06-08 18:01:00
*/

UPDATE `engine_settings` SET `value_dev` = '0',    `value_test` = '0',    `value_stage` = '0',    `value_live` = '0'    WHERE `variable` = 'sticky_mobile_footer_menu';
UPDATE `engine_settings` SET `value_dev` = '0',    `value_test` = '0',    `value_stage` = '0',    `value_live` = '0'    WHERE `variable` = 'show_cart_in_mobile_header';
UPDATE `engine_settings` SET `value_dev` = 'list', `value_test` = 'list', `value_stage` = 'list', `value_live` = 'list' WHERE `variable` = 'course_list_mode_mobile';

