/*
ts:2020-07-17 18:30:00
*/

ALTER TABLE `plugin_pages_pages` ADD COLUMN `draft_of` INT(11) NULL DEFAULT NULL AFTER `parent_id`;
