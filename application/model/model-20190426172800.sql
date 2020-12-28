/*
ts:2019-04-26 17:28:00
*/

UPDATE `engine_settings`
SET `name` = 'Always On (debug mode)'
WHERE `variable` = 'browser_sniffer_testmode';

UPDATE `engine_settings`
SET `note` = 'This will force the Browser sniffer to display always if set to ON. NOTE: When enabled, the red box top right will ONLY appear in Debug Mode.'
WHERE `variable` = 'browser_sniffer_testmode';