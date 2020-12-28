/*
ts:2016-04-07 02:40:14
*/

CREATE TABLE IF NOT EXISTS `plugin_products_discount_displayed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cart_id` varchar(100) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `displayed_discount_type_ids` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL COMMENT '0->pending,1->approved',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='table for stroing the cart discounts' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plugin_products_discount_displayed_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_displayed_id` int(11) NOT NULL COMMENT 'this is the primary key of plugin_products_discount_displayed table and here is foreign key',
  `discount_type_id` int(11) NOT NULL COMMENT 'which discount is applied',
  `discount` float NOT NULL,
  `discount_percentage` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
