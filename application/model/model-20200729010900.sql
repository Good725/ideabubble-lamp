/*
ts:2020-07-29 01:09:00
*/

ALTER TABLE `engine_users`
    ADD COLUMN `dial_code_phone` VARCHAR(45) NULL AFTER `address_3`,
    ADD COLUMN `country_dial_code_phone` VARCHAR(45) NULL AFTER `dial_code_phone`,
    ADD COLUMN `dial_code_mobile` VARCHAR(45) NULL AFTER `phone`,
    ADD COLUMN `country_dial_code_mobile` VARCHAR(45) NULL AFTER `dial_code_mobile`;
