/*
ts:2017-03-27 17:35:00
*/

ALTER TABLE `plugin_events_events` ADD FULLTEXT INDEX (`name`, `description`) ;
ALTER TABLE `engine_lookup_values` ADD FULLTEXT INDEX (`label`);
ALTER TABLE `plugin_events_venues` ADD FULLTEXT INDEX (`name`);

ALTER TABLE `plugin_events_venues` ADD FULLTEXT INDEX (`name`, `city`, `address_1`, `address_2`, `address_3`);