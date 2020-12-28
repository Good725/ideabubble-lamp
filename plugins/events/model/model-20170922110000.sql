/*
ts:2017-09-22 11:00:00
*/

ALTER TABLE `plugin_events_orders`
ADD COLUMN `county`   VARCHAR(100)  NULL  AFTER `county_id` ,
ADD COLUMN `comments` VARCHAR(1023) NULL  AFTER `eircode`
;

ALTER TABLE `plugin_events_orders_payments`
ADD COLUMN `cc_expiry_mm`        VARCHAR(2) NULL  AFTER `credit_card_type` ,
ADD COLUMN `cc_expiry_yy`        VARCHAR(2) NULL  AFTER `cc_expiry_mm` ,
ADD COLUMN `cc_last_four_digits` VARCHAR(4) NULL  AFTER `cc_expiry_yy` ;

ALTER TABLE `plugin_events_orders_payments`
DROP COLUMN `cc_expiry_mm`,
DROP COLUMN `cc_expiry_yy`;