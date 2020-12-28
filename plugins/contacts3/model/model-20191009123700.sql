/*
ts:2019-10-09 12:37:00
*/

ALTER TABLE `plugin_contacts3_contacts`
    ADD COLUMN `billing_residence_id` INT(11) NULL DEFAULT NULL AFTER `residence`;
