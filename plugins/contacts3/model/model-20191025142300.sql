/*
ts:2019-10-25 14:23:00
*/

UPDATE `engine_settings`
SET `name` = 'Frontend register'
WHERE (`variable` = 'engine_enable_external_register');

UPDATE `engine_settings`
SET `name` = 'Frontend organisation register'
WHERE (`variable` = 'engine_enable_org_register');

UPDATE `engine_settings`
SET `name` = 'Frontend default role'
WHERE (`variable` = 'website_frontend_register_role');