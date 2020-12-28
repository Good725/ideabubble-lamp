/*
ts:2020-05-12 23:37:00
*/

ALTER TABLE `plugin_ib_educate_bookings` ADD COLUMN `how_did_you_hear` INT NOT NULL DEFAULT 0 AFTER `invoice_details`;
