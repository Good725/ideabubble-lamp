/*
ts:2020-06-11 23:41:00
*/

ALTER TABLE `plugin_contacts3_contacts`
    ADD COLUMN `domain_name` VARCHAR(255) NULL DEFAULT NULL AFTER `job_function_id`;