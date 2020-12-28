/*
ts:2016-04-05 12:45:35
*/
ALTER TABLE `plugin_products_discount_rate`  ADD `discount_rate_percentage` VARCHAR(100) NOT NULL COMMENT 'This field is for cart based discount price storing the value in percentage' AFTER `discount_rate`;
