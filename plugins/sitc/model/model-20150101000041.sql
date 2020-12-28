/*
ts:2015-01-01 00:00:41
*/
CREATE TABLE IF NOT EXISTS `plugin_sict_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sku` text NOT NULL,
  `name` text NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `img_url` text NOT NULL,
  `weight` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plugin_sict_stock_and_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `price` double NOT NULL,
  `cost` double NOT NULL,
  `distributor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plugin_sict_manufacturer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `img_url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `plugin_sict_product_relation` (
  `product_id` int(11) NOT NULL,
  `sict_product_id` int(11) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `sict_product_id` (`sict_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

ALTER IGNORE TABLE `plugin_sict_product` CHANGE `weight` `thumb_url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `plugin_sict_distributors` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `website` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

ALTER IGNORE TABLE `plugin_sict_product` ADD `category_id` INT( 64 ) NOT NULL;

ALTER IGNORE TABLE `plugin_sict_product` ADD `specification` TEXT NOT NULL;

-- PCSYS-104
ALTER IGNORE TABLE plugin_products_category ADD INDEX category_idx (category);
ALTER IGNORE TABLE plugin_products_category ADD INDEX parent_idx (parent_id);
ALTER IGNORE TABLE plugin_sict_distributors MODIFY COLUMN name VARCHAR(100);
ALTER IGNORE TABLE plugin_sict_distributors ADD INDEX name_idx (name);
ALTER IGNORE TABLE plugin_sict_manufacturer MODIFY COLUMN name VARCHAR(100);
ALTER IGNORE TABLE plugin_sict_manufacturer ADD INDEX name_idx (name);
ALTER IGNORE TABLE plugin_sict_product MODIFY COLUMN sku VARCHAR(100);
ALTER IGNORE TABLE plugin_sict_product ADD INDEX sku_idx (sku);
ALTER IGNORE TABLE plugin_products_product ADD INDEX product_code_idx (product_code);
ALTER IGNORE TABLE plugin_sict_product DROP COLUMN category_id;
CREATE TABLE IF NOT EXISTS plugin_sict_product_category(sitc_id INT, category_id INT, PRIMARY KEY (sitc_id, category_id));
CREATE TABLE IF NOT EXISTS plugin_sict_product_pictures(sitc_id INT, picture_url TEXT, thumb_url TEXT, KEY sitc_idx(sitc_id));

-- PCSYS-74
ALTER IGNORE TABLE `plugin_products_product` ADD FULLTEXT INDEX (`title`, `product_code`);

-- PCSYS-126
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sitc_local_image', 'Use Local Images', 0, 0, 0, 0, 0, 'both', '', 'toggle_button', 'SITC', 0, 'Model_Settings,on_or_off');

UPDATE `plugins` SET `show_on_dashboard` = 0 WHERE `name` = 'sitc';

ALTER TABLE plugin_sict_product ADD INDEX product_idx( product_id );
