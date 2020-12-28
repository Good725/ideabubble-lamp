/*
ts:2016-08-24 18:13:00
*/

DELETE FROM engine_plugins_per_role WHERE plugin_id IN (SELECT id FROM engine_plugins WHERE `name` = 'homework');

