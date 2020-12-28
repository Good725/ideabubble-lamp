/*
ts:2018-08-23 11:58:00
*/

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_student_years`
(
  `discount_id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  KEY `discount_id` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE plugin_ib_educate_bookings_has_applications ADD COLUMN photo_id INT;
ALTER TABLE plugin_ib_educate_bookings_has_applications ADD COLUMN student TEXT;
