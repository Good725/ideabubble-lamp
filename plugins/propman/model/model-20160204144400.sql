/*
ts:2016-02-04 14:44:00
*/

ALTER TABLE plugin_propman_properties ADD COLUMN `url` VARCHAR(250);
ALTER TABLE plugin_propman_properties ADD KEY (`url`);
ALTER TABLE `plugin_propman_ratecards` CHANGE COLUMN `discountType` `discount_type`  enum('Fixed','Percent');
UPDATE plugin_propman_ratecards_date_ranges SET discount_type = 'Fixed' WHERE discount_type IS NULL;
UPDATE plugin_propman_ratecards_calendar SET discount_type = 'Fixed' WHERE discount_type IS NULL;

CREATE TABLE plugin_propman_bookings
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  adults  TINYINT,
  children TINYINT,
  infants TINYINT,
  billing_name  VARCHAR(50),
  billing_address  VARCHAR(200),
  billing_town  VARCHAR(100),
  billing_county  VARCHAR(100),
  billing_country  VARCHAR(50),
  billing_phone  VARCHAR(50),
  billing_email  VARCHAR(50),
  comments  TEXT,

  property_id INT NOT NULL,
  checkin DATE NOT NULL,
  checkout DATE NOT NULL,
  guests TINYINT,
  fee DOUBLE NOT NULL,
  discount DOUBLE NOT NULL,
  price DOUBLE NOT NULL,
  status ENUM('New', 'Checked In', 'Checked Out', 'Cancelled', 'Not Arrived'),
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT(1) NOT NULL,

  KEY (`property_id`),
  KEY (`customer_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

ALTER TABLE plugin_propman_bookings AUTO_INCREMENT=100000;

CREATE TABLE plugin_propman_bookings_payments
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT,
  amount DOUBLE NOT NULL,
  status ENUM('Unpaid', 'Processing', 'Redirect', 'Paid', 'Cancelled'),
  gateway VARCHAR(50) NOT NULL,
  gateway_tx TEXT,
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT(1) NOT NULL,

  KEY (`booking_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

ALTER TABLE `plugin_propman_bookings_payments` MODIFY COLUMN `status`  ENUM('Unpaid','Processing','Redirect','Paid','Cancelled','Error');

INSERT INTO `settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
values
  ('propman_min_deposit', 'Deposit', 'propman', '125', '125', '125', '125', '125', 'both', '', 'text', 'Properties', 0, '');

INSERT INTO plugin_contacts_mailing_list (`name`) VALUES ('Customer');
