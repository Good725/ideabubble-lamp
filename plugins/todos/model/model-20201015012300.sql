/*
ts:2020-10-15 01:23:00
*/

ALTER TABLE `plugin_todos_todos2_has_results`
    ADD COLUMN `deleted` TINYINT NULL DEFAULT 0 AFTER `status`,
    ADD COLUMN `updated_by` INT NULL AFTER `deleted`,
    ADD COLUMN `date_created` DATETIME NULL AFTER `updated_by`,
    ADD COLUMN `date_updated` DATETIME NULL AFTER `date_created`;
