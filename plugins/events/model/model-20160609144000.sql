/*
ts:2016-06-09 14:40:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `note`, `type`, `group`) VALUES
(
  'forecast_io_api_key',
  'forecast.io API Key',
  'events',
  'API key to use the weather forecast. This can be acquired at &lt;a href=&quot;https://developer.forecast.io/&quot;&gt;developer.forecast.io&lt;/a&gt;',
  'text',
  'Events'
);

ALTER IGNORE TABLE `plugin_events_events`
ADD COLUMN `forecast_icon`    VARCHAR(45)   NULL  AFTER `status_reason` ,
ADD COLUMN `forecast_summary` VARCHAR(1023) NULL  AFTER `forecast_icon` ,
ADD COLUMN `forecast_json`    TEXT          NULL AFTER `forecast_summary` ;

-- Renaming "Wait" to "Sale Ended"
-- 1. Add "Sale Ended" option.
-- 2. Update all "Wait" to "Sale Ended"
-- 3. Remove "Wait"
ALTER IGNORE TABLE `plugin_events_events` CHANGE `status` `status` ENUM('Wait', 'Live', 'Cancelled', 'Postponed', 'Inappropriate', 'Sale Ended') DEFAULT 'Sale Ended';
UPDATE IGNORE `plugin_events_events` SET `status` = 'Sale Ended' WHERE `status` = 'Wait';
ALTER IGNORE TABLE `plugin_events_events` CHANGE `status` `status` ENUM('Live', 'Cancelled', 'Postponed', 'Inappropriate', 'Sale Ended') DEFAULT 'Sale Ended';
