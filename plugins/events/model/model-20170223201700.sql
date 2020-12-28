/*
ts:2017-02-23 20:17:00
*/

ALTER TABLE plugin_events_events ADD COLUMN commission_fixed_amount DECIMAL(10, 2);
ALTER TABLE plugin_events_events ADD COLUMN commission_amount DECIMAL(10, 2);
ALTER TABLE plugin_events_events ADD COLUMN commission_type ENUM('Fixed', 'Percent');


ALTER TABLE `plugin_events_events` ADD FULLTEXT INDEX (`name`, `description`) ;
ALTER TABLE `engine_lookup_values` ADD FULLTEXT INDEX (`label`);
ALTER TABLE `plugin_events_venues` ADD FULLTEXT INDEX (`name`);