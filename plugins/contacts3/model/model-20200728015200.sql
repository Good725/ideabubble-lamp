/*
ts:2020-07-28 01:52:00
*/
ALTER TABLE `engine_countries`
    ADD COLUMN `alpha2_code` VARCHAR(2) NULL AFTER `name`,
    ADD COLUMN `alpha3_code` VARCHAR(3) NULL AFTER `alpha2_code`,
    ADD COLUMN `iso_code` INT NULL AFTER `alpha3_code`,
    ADD COLUMN `dial_code` INT NULL AFTER `iso_code`;


