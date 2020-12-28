/*
ts:2016-04-06 17:55:00
*/

ALTER TABLE `plugin_propman_ratecards` DROP COLUMN is_deal;
ALTER TABLE `plugin_propman_ratecards_date_ranges` ADD COLUMN is_deal TINYINT DEFAULT 0;
