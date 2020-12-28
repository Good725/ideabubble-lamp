/*
ts:2019-10-08 11:34:00
*/

ALTER TABLE `plugin_purchasing_purchases`
    ADD COLUMN `comment` TEXT NULL DEFAULT NULL AFTER `status`;
