/*
ts:2020-07-28 01:43:00
*/

ALTER TABLE `plugin_contacts3_contact_has_notifications`
    ADD COLUMN `country_dial_code` VARCHAR(45) NULL DEFAULT NULL AFTER `group_id`,
    ADD COLUMN `dial_code` VARCHAR(45) NULL DEFAULT NULL AFTER `country_dial_code`;
