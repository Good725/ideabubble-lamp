/*
ts:2019-02-06 09:11:00
*/

CREATE TABLE `plugin_bookings_discounts_quantity_rates`
(
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `discount_id` INT NOT NULL,
  `min_qty` INT NOT NULL,
  `max_qty` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `deleted` TINYINT NOT NULL DEFAULT 0,
  KEY `discount_id` (`discount_id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;

