/*
ts:2017-06-14 16:00:00
*/

UPDATE `engine_settings` SET `value_dev` = 'modern', `value_test` = 'modern', `value_stage` = 'modern', `value_live` = 'modern' WHERE `variable` = 'cms_template';
UPDATE `engine_settings` SET `value_dev` = 'donate', `value_test` = 'donate', `value_stage` = 'donate', `value_live` = 'donate' WHERE `variable` = 'cms_skin';

UPDATE `engine_settings` SET `value_dev` = '#e8686d', `value_test` = '#e8686d', `value_stage` = '#e8686d', `value_live` = '#e8686d' WHERE `variable`  = 'donations_alarm_color';
UPDATE `engine_settings` SET `value_dev` = '#eadc6b', `value_test` = '#eadc6b', `value_stage` = '#eadc6b', `value_live` = '#eadc6b' WHERE `variable`  = 'donations_warn_color';

UPDATE `engine_plugins` SET `flaticon` = 'coins' WHERE `name` = 'donations';
