/*
ts:2016-05-19 14:53:00
*/

DELETE FROM `engine_plugins` WHERE `name` = 'snip';
DROP TABLE IF EXISTS `plugin_snip_sync_history`;
DELETE FROM `engine_settings` WHERE `variable` LIKE 'snip_%';
