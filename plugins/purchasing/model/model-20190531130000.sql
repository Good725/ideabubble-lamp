/*
ts:2019-05-31 13:00:00
*/

ALTER TABLE `plugin_purchasing_purchases`
DROP COLUMN `business`,
DROP COLUMN `supplier`,
ADD COLUMN `business_id` INT(11) NULL AFTER `id`,
ADD COLUMN `supplier_id` INT(11) NULL AFTER `business_id`;
