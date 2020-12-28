/*
ts:2016-08-24 11:47:00
*/

UPDATE engine_settings SET value_live='support@uticket.ie' WHERE `variable` = 'engine_cant_login_mailto';
UPDATE engine_settings SET value_live='1', value_stage='1', value_test='1', value_dev='1' WHERE `variable`='engine_enable_external_register';

