/*
ts:2020-07-24 15:30:00
*/


ALTER TABLE `plugin_bookings_discounts`
    ADD COLUMN `application_order` INT NOT NULL DEFAULT 1 AFTER `application_type`;
