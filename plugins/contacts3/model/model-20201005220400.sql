/*
ts:2020-10-05 22:04:00
*/

ALTER TABLE `plugin_contacts3_contacts`
    ADD COLUMN `is_public_domain` TINYINT NULL DEFAULT 0 AFTER `gdpr_cleansed_by_report_id`;