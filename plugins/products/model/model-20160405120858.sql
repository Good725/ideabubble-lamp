/*
ts:2016-04-05 12:08:58
*/
CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `delete` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `plugin_bookings_discounts_types` set `title` = 'Cart Based, % Price Discount', `delete` = '0';
