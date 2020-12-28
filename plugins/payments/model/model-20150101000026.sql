/*
ts:2015-01-01 00:00:26
*/

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('payments', 'Payments', '0', '0', NULL);

--Cart logging

CREATE TABLE IF NOT EXISTS `plugin_payments_log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `cart_details` text,
  `customer_name` varchar(60) DEFAULT NULL,
  `customer_telephone` varchar(30) DEFAULT NULL,
  `customer_address` text,
  `customer_email` varchar(120) DEFAULT NULL,
  `paid` bit(1) DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `payment_amount` double DEFAULT NULL,
  `ip_address` varchar(13) DEFAULT NULL,
  `user_agent` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE `plugins` SET icon = 'payments.png' WHERE friendly_name = 'Payments';
UPDATE `plugins` SET `plugins`.`order` = 99 WHERE friendly_name = 'Payments';

ALTER IGNORE TABLE `plugin_payments_log` ADD `purchase_time` datetime;
ALTER IGNORE TABLE `plugin_payments_log` ADD `cc_num` VARCHAR(255);
ALTER IGNORE TABLE `plugin_payments_log` ADD `realex_status` VARCHAR(255);

-- PCSYS-181
ALTER IGNORE TABLE `plugin_payments_log` ADD COLUMN `customer_user_id` INT;
UPDATE plugin_payments_log l INNER JOIN users u ON l.customer_email = u.email SET l.customer_user_id = u.id;
UPDATE `plugin_reports_reports` SET `sql` = 'SELECT \npurchase_time as `Date`,\ncustomer_name AS `Name`,\ncustomer_telephone as `Phone`,\ncustomer_email as `Checkout Email`,\nusers.id as `User Id`,\nusers.email as `User Email`,\ngroup_concat(case when t2.cart_id = plugin_payments_log.cart_id then t2.title end SEPARATOR \',\') AS `Items`,\n	(\n		CASE\n		WHEN paid = 1 THEN\n			\'Yes\'\n		ELSE\n			IF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'Credit\', \'No\')\n		END\n	) AS `Paid`,\n	payment_amount as `Total`,\nIF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'credit\', plugin_payments_log.payment_type) as `Payment Method`\nFROM\n	plugin_payments_log\nLEFT JOIN users ON plugin_payments_log.customer_user_id = users.id\nLEFT JOIN plugin_products_cart_items AS t2 ON t2.cart_id = plugin_payments_log.cart_id\nGROUP BY plugin_payments_log.cart_id\nORDER BY `purchase_time` DESC' WHERE `name` = 'Orders';
UPDATE `plugin_reports_reports` SET `sql` = 'SELECT \npurchase_time as `Date`,\ncustomer_name AS `Name`,\ncustomer_telephone as `Phone`,\ncustomer_email as `Checkout Email`,\nusers.id as `User Id`,\nusers.email as `User Email`,\ngroup_concat(case when t2.cart_id = plugin_payments_log.cart_id then t2.title end SEPARATOR \',\') AS `Items`,\n	(\n		CASE\n		WHEN paid = 1 THEN\n			\'Yes\'\n		ELSE\n			IF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'Credit\', \'No\')\n		END\n	) AS `Paid`,\n	payment_amount as `Total`,\nIF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'credit\', plugin_payments_log.payment_type) as `Payment Method`\nFROM\n	plugin_payments_log\nLEFT JOIN users ON plugin_payments_log.customer_user_id = users.id\nLEFT JOIN plugin_products_cart_items AS t2 ON t2.cart_id = plugin_payments_log.cart_id\nWHERE (paid=1 OR plugin_payments_log.payment_type = \'\')\nGROUP BY plugin_payments_log.cart_id\nORDER BY `purchase_time` DESC' WHERE `name` = 'Orders Paid';
ALTER IGNORE TABLE `plugin_payments_log` ADD COLUMN `delivery_method` VARCHAR(100);
ALTER IGNORE TABLE `plugin_payments_log` ADD COLUMN `store_id` INT;
UPDATE `plugin_reports_reports` SET `sql` = 'SELECT \npurchase_time as `Date`,\ncustomer_name AS `Name`,\ncustomer_telephone as `Phone`,\ncustomer_email as `Checkout Email`,\nusers.id as `User Id`,\nusers.email as `User Email`,\ngroup_concat(case when t2.cart_id = plugin_payments_log.cart_id then t2.title end SEPARATOR \',\') AS `Products`,\n	(\n		CASE\n		WHEN paid = 1 THEN\n			\'Yes\'\n		ELSE\n			IF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'Credit\', \'No\')\n		END\n	) AS `Paid`,\n	payment_amount as `Total`,\nIF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'credit\', plugin_payments_log.payment_type) as `Payment Method`,\n`plugin_payments_log`.`delivery_method` as `Delivery Method`,\nCONCAT_WS(\' \', store.title, store.county) as `Store`\nFROM\n	plugin_payments_log\nLEFT JOIN users ON plugin_payments_log.customer_user_id = users.id\nLEFT JOIN plugin_products_cart_items AS t2 ON t2.cart_id = plugin_payments_log.cart_id\nLEFT JOIN plugin_locations_location AS store ON `plugin_payments_log`.`store_id` = `store`.`id` \nGROUP BY plugin_payments_log.cart_id\nORDER BY `purchase_time` DESC' WHERE  `name` = 'Orders';

ALTER IGNORE TABLE `plugin_payments_log` ADD COLUMN `order_reference` VARCHAR(255) NULL;
