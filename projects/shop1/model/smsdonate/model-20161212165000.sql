/*
ts:2016-12-12 16:50:00
*/

UPDATE engine_settings SET value_live = 'd/m/Y', value_stage = 'd/m/Y', value_test = 'd/m/Y', value_dev = 'd/m/Y' WHERE `variable` = 'date_format';
