/*
ts:2020-08-10 16:51:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
SELECT '1', 'user_auth_2step_sms', 'User / Auth / Two Step Auth SMS ', `id` FROM engine_resources where alias = 'user';

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
SELECT '1', 'user_auth_2step_email', 'User / Auth / Two Step Auth Email ', `id` FROM engine_resources where alias = 'user';
