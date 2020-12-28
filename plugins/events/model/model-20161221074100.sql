/*
ts:2016-12-21 07:41:00
*/

ALTER TABLE `plugin_events_checkout_details` CHANGE `full_name` `ccName` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `plugin_events_checkout_details` CHANGE `state` `county` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `plugin_events_checkout_details` CHANGE `phone` `telephone` VARCHAR( 100 ) NOT NULL ;