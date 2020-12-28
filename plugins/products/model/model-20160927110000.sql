/*
ts:2016-09-27 11:00:00
*/

ALTER TABLE `plugin_products_product` ADD COLUMN `use_postage` INT(1) NOT NULL DEFAULT 1 AFTER `order` ;
