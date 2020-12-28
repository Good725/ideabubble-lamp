/*
ts:2020-04-24 22:47:00
*/

ALTER TABLE `plugin_bookings_discounts`
    ADD COLUMN `member_only` TINYINT(1) NULL DEFAULT 0 AFTER `apply_to`;