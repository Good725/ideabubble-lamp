/*
ts:2018-03-07 13:10:00
*/

UPDATE engine_api_plugins SET `enabled` = 1 WHERE `plugin` = 'courses';
UPDATE engine_settings SET expose_to_api = 1 WHERE `variable` in ('countdown_title', 'countdown_datetime');
