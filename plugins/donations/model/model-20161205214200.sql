/*
ts:2016-12-05 21:42:00
*/

ALTER TABLE plugin_donations_products ADD COLUMN `status` ENUM('Active', 'Deactive') NOT NULL DEFAULT 'Active';
