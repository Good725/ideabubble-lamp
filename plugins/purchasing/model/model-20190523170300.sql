/*
ts:2019-05-23 17:03:00
*/

ALTER TABLE `plugin_purchasing_purchases`
ADD COLUMN `total_vat` DECIMAL(10,2) NULL DEFAULT NULL AFTER `total`;
