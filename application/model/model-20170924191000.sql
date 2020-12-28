/*
ts:2017-09-24 19:10:00
*/

update engine_settings set `group` = 'User Registration' where variable = 'engine_enable_external_register';
update engine_settings set `type` = 'select', `options` = 'Model_Roles,get_settings_options' where variable = 'website_frontend_register_role';

