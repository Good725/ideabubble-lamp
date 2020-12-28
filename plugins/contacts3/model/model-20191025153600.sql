/*
ts:2019-10-25 15:36:00
*/

ALTER TABLE `plugin_contacts3_organisations`
    ADD COLUMN `primary_biller_id` INT(11) NULL DEFAULT NULL AFTER `contact_id`;
