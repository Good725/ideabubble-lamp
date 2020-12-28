/*
ts:2016-12-19 12:29:00
*/

CREATE TABLE `plugin_courses_discounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `summary` text,
  `type` int(1) DEFAULT '0',
  `code` varchar(255) DEFAULT NULL,
  `from` decimal(10,2) DEFAULT NULL,
  `to` decimal(10,2) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_to` timestamp NULL DEFAULT NULL,
  `publish` int(1) DEFAULT '1',
  `delete` int(1) DEFAULT '0',
  `categories` text,
  `amount_type` enum('Percent','Fixed','Quantity') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `schedule_type` set('Prepay','PAYG') NOT NULL DEFAULT 'Prepay',
  `item_quantity_min` int(11) DEFAULT NULL,
  `item_quantity_max` int(11) DEFAULT NULL,
  `min_students_in_family` tinyint(4) DEFAULT NULL,
  `max_students_in_family` tinyint(4) DEFAULT NULL,
  `item_quantity_scope` enum('Booking','Contact','Family') DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `is_package` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB
CHARSET=utf8;

CREATE TABLE `plugin_courses_discounts_for_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `contact_id` (`contact_id`)
)
ENGINE=InnoDB
CHARSET=utf8;

CREATE TABLE `plugin_courses_discounts_has_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `course_id` (`course_id`)
)
ENGINE=InnoDB
CHARSET=utf8;

CREATE TABLE `plugin_courses_discounts_has_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `schedule_id` (`schedule_id`)
)
ENGINE=InnoDB
CHARSET=utf8;


CREATE TABLE `plugin_courses_bookings_has_discounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) unsigned NOT NULL,
  `discount_id` int(11) DEFAULT NULL COMMENT 'ifnull => custom',
  `created_by` int(10) DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `amount` float(5,2) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `memo` text,
  `deleted` tinyint NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;
