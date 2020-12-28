/*
ts:2016-02-05 10:34:00
*/

ALTER TABLE `plugin_propman_ratecards_calendar` DROP PRIMARY KEY, ADD INDEX (`ratecard_id`, `date`);
ALTER TABLE `plugin_propman_ratecards_calendar` ADD COLUMN `id` INT AUTO_INCREMENT PRIMARY KEY;
