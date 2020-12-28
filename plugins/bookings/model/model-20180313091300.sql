/*
ts:2018-03-13 09:13:00
*/

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_bookings` (
  `booking_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `booking_status` int(11) DEFAULT '1',
  `created_date` timestamp NULL DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `publish` int(1) DEFAULT '1',
  `delete` int(1) DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `custom_discount` float(5,2) DEFAULT NULL,
  `discount_memo` varchar(1000) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `bill_payer` int(11) DEFAULT NULL,
  `amendable` tinyint(4) NOT NULL DEFAULT '0',
  `payg_booking_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cc_booking_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sms_booking_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cc','sms') DEFAULT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_booking_items` (
  `booking_item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) unsigned DEFAULT NULL,
  `period_id` int(11) unsigned DEFAULT NULL,
  `seat_row_id` int(11) DEFAULT NULL,
  `seat_fee` int(11) DEFAULT NULL,
  `attending` int(1) NOT NULL DEFAULT '1',
  `delete` int(1) DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeslot_status` set('Present','Late','Early Departures','Paid') DEFAULT NULL,
  `booking_status` int(11) DEFAULT NULL,
  `amendable` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`booking_item_id`),
  KEY `booking_id` (`booking_id`),
  KEY `period_id` (`period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_booking_has_schedules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) unsigned DEFAULT NULL,
  `schedule_id` int(10) unsigned DEFAULT NULL,
  `deleted` int(1) DEFAULT '0',
  `publish` int(1) DEFAULT '1',
  `transaction_id` int(10) DEFAULT NULL,
  `booking_status` int(11) DEFAULT NULL,
  `amendable` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_booking_schedule_has_label` (
  `booking_schedule_label_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `publish` int(1) DEFAULT '1',
  `delete` int(1) DEFAULT '0',
  PRIMARY KEY (`booking_schedule_label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_bookings_discounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) unsigned NOT NULL,
  `discount_id` int(11) DEFAULT NULL COMMENT 'ifnull => custom',
  `status` varchar(50) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `amount` float(5,2) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_bookings_ignored_discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) DEFAULT NULL,
  `discount_id` int(10) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_bookings_labels` (
  `label_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `colour` varchar(255) DEFAULT 'Transparent',
  `publish` int(1) DEFAULT NULL,
  `delete` int(1) DEFAULT NULL,
  PRIMARY KEY (`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_bookings_status` (
  `status_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `publish` int(1) DEFAULT NULL,
  `delete` int(1) DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_ib_educate_bookings_status` (`status_id`, `title`, `publish`, `delete`) VALUES (1, 'Enquiry', 1, 0);
INSERT IGNORE INTO `plugin_ib_educate_bookings_status` (`status_id`, `title`, `publish`, `delete`) VALUES (2, 'Confirmed', 1, 0);
INSERT IGNORE INTO `plugin_ib_educate_bookings_status` (`status_id`, `title`, `publish`, `delete`) VALUES (3, 'Cancelled', 1, 0);
INSERT IGNORE INTO `plugin_ib_educate_bookings_status` (`status_id`, `title`, `publish`, `delete`) VALUES (4, 'In Progress', 1, 0);
INSERT IGNORE INTO `plugin_ib_educate_bookings_status` (`status_id`, `title`, `publish`, `delete`) VALUES (5, 'Completed', 1, 0);

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_payment_methods` (
  `method_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `order` int(2) DEFAULT '99',
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_ib_educate_payment_methods` (`method_id`, `title`, `order`) VALUES (1, 'Cash', 1);
INSERT IGNORE INTO `plugin_ib_educate_payment_methods` (`method_id`, `title`, `order`) VALUES (2, 'VISA', 2);
INSERT IGNORE INTO `plugin_ib_educate_payment_methods` (`method_id`, `title`, `order`) VALUES (3, 'MasterCard', 3);
INSERT IGNORE INTO `plugin_ib_educate_payment_methods` (`method_id`, `title`, `order`) VALUES (4, 'Cheque', 4);

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_payments` (
  `payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) unsigned DEFAULT NULL,
  `amount` double DEFAULT '0',
  `method` int(2) DEFAULT '1',
  `delete` int(1) DEFAULT '0',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_transaction_types` (
  `transaction_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `order` int(2) DEFAULT '99',
  PRIMARY KEY (`transaction_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_ib_educate_transaction_types` (`transaction_type_id`, `title`, `order`) VALUES (1, 'Pay Now', 99);
INSERT IGNORE INTO `plugin_ib_educate_transaction_types` (`transaction_type_id`, `title`, `order`) VALUES (2, 'Pay as you go', 99);

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_transactions` (
  `transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) unsigned DEFAULT NULL,
  `amount` double DEFAULT '0',
  `completed` int(1) DEFAULT '0',
  `delete` int(1) DEFAULT '0',
  `transaction_type` int(11) DEFAULT '1',
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) DEFAULT '0',
  `contact_id` int(11) unsigned DEFAULT NULL,
  `family_id` int(11) unsigned DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `payment_due_date` timestamp NULL DEFAULT NULL,
  `discount` float(8,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_has_schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) unsigned NOT NULL,
  `schedule_id` int(10) DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `publish` int(1) DEFAULT '1',
  `deleted` int(1) DEFAULT '0',
  `event_id` int(11) DEFAULT NULL,
  `payg_period` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(10) unsigned NOT NULL,
  `booking_id` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `operation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  `discount` float(8,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_journal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `journaled_transaction_id` int(11) unsigned NOT NULL,
  `transaction_id` int(11) unsigned NOT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_payment_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `outstanding` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `term` int(11) DEFAULT NULL,
  `interest_type` enum('Fixed','Percent') DEFAULT NULL,
  `interest` decimal(10,2) DEFAULT NULL,
  `adjustment` decimal(10,2) DEFAULT NULL,
  `starts` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `status` enum('Outstanding','Completed','Cancelled') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_payment_plans_has_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_plan_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `adjustment` decimal(10,2) DEFAULT NULL,
  `interest` decimal(10,2) DEFAULT NULL,
  `penalty` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `payment_plan_id` (`payment_plan_id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(10) unsigned NOT NULL,
  `type` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bank_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) NOT NULL DEFAULT 'EUR',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` text,
  `deleted` tinyint(1) DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_payments_cheque` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `name_cheque` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_payments_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `transaction_id` int(10) unsigned NOT NULL,
  `type` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bank_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) NOT NULL DEFAULT 'EUR',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `note` text,
  `deleted` tinyint(1) DEFAULT '0',
  `operation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_payments_journal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `journaled_payment_id` int(10) unsigned NOT NULL,
  `payment_id` int(10) unsigned NOT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE If NOT EXISTS `plugin_bookings_transactions_payments_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(255) DEFAULT NULL,
  `credit` int(1) DEFAULT NULL,
  `publish` int(1) DEFAULT NULL,
  `delete` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_transactions_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `credit` int(1) DEFAULT NULL,
  `publish` int(1) DEFAULT NULL,
  `delete` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (1, 'Booking - Pay Now', 1, 1, 0);
INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (2, 'Booking - PAYG', 1, 1, 0);
INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (3, 'Journal Credit', 0, 1, 0);
INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (4, 'Journal Cancel Booking', 0, 1, 0);
INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (5, 'Journal Refund Booking', 0, 1, 0);
INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (6, 'Journal Absent Credit', 0, 1, 0);
INSERT IGNORE INTO `plugin_bookings_transactions_types` (`id`, `type`, `credit`, `publish`, `delete`) VALUES (7, 'Billed Booking', 1, 1, 0);

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_has_schedules`
(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_has_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_action_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_daily_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `min_days` int(11) NOT NULL,
  `max_days` int(11) NOT NULL,
  `is_consecutive` tinyint(4) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_for_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_has_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_has_previous_booking_condition` (
  `discount_id` int(10) unsigned NOT NULL,
  `ref_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `discount_id_2` (`discount_id`,`ref_id`,`type_id`),
  KEY `discount_id` (`discount_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_has_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `delete` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `summary` text,
  `image_id` int(11) DEFAULT NULL,
  `type` int(1) DEFAULT '0',
  `code` varchar(255) DEFAULT NULL,
  `x` varchar(255) DEFAULT '0',
  `y` varchar(255) DEFAULT '0',
  `z` varchar(255) DEFAULT '0',
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
  `item_quantity_scope` enum('Booking','Contact','Family','Schedule') DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `is_package` tinyint(4) NOT NULL DEFAULT '0',
  `days_of_the_week` set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `min_number_of_classes` int(11) DEFAULT '0',
  `action_type` int(10) unsigned DEFAULT NULL COMMENT '1 for regular booking 2 for applying this object as price additon',
  `term_type` int(10) unsigned DEFAULT NULL COMMENT '1 for yearly 2 for half year 3 for term only.',
  `previous_term_type` int(10) unsigned DEFAULT NULL,
  `previous_term_paid_from` datetime DEFAULT NULL,
  `previous_term_paid_to` datetime DEFAULT NULL,
  `publish_on_web` tinyint(4) NOT NULL DEFAULT '0',
  `min_days` int(11) DEFAULT NULL,
  `min_days_is_consecutive` tinyint(4) NOT NULL DEFAULT '0',
  `item_quantity_type` enum('Courses','Classes') DEFAULT NULL,
  `course_date_from` date DEFAULT NULL,
  `course_date_to` date DEFAULT NULL,
  `class_time_from` time DEFAULT NULL,
  `class_time_to` time DEFAULT NULL,
  `max_usage_per` enum('Cart','Contact','Family','GLOBAL') DEFAULT NULL,
  `apply_to` enum('Schedule','Cart') DEFAULT 'Schedule',
  PRIMARY KEY (`id`),
  KEY `action_type` (`action_type`),
  KEY `previouse_term_type` (`previous_term_type`),
  KEY `term_type` (`term_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_per_day_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `min_timeslots` int(11) NOT NULL,
  `max_timeslots` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discount_id` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_bookings_discounts_previous_booking_condition_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

