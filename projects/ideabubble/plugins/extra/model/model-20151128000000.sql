/*
ts:2015-11-28 00:00:00
*/
ALTER TABLE `plugin_extra_realvault_payers` MODIFY COLUMN `realvault_id` VARCHAR(40);
ALTER TABLE `plugin_extra_invoices` ADD COLUMN `date_from` DATE;
ALTER TABLE `plugin_extra_invoices` ADD COLUMN `date_to` DATE;
ALTER TABLE `plugin_extra_invoices` ADD COLUMN `status` ENUM('Unpaid', 'Paid', 'Cancelled') NOT NULL DEFAULT 'Unpaid';
ALTER TABLE `plugin_extra_invoices` ADD COLUMN `cart_id` VARCHAR(20);
