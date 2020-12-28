/*
ts:2016-01-29 12:21:00
*/

ALTER TABLE `plugin_propman_ratecards` DROP COLUMN `midweek_price`;
ALTER TABLE `plugin_propman_ratecards` DROP COLUMN `weekend_price`;
ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `starts` DATE;
ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `ends` DATE;
ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `short_stay_price` DOUBLE;
ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `additional_nights_price` DOUBLE;
ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `min_stay` TINYINT;
ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `arrival` ENUM ('Any', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

DROP TABLE `plugin_propman_ratecards_weeks`;
DROP TABLE `plugin_propman_ratecards_prices`;

INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('propman_additional_nights_max', 'Additional Nights Max', 'propman', '2', '2', '2', '2', '2', 'both', '', 'text', 'Properties', 0, '');

INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('propman_short_stay', 'Short Stay Nights Less Than', 'propman', '4', '4', '4', '4', '4', 'both', '', 'text', 'Properties', 0, '');

CREATE TABLE `plugin_propman_ratecards_calendar`
(
  ratecard_id  INT NOT NULL,
  date  DATE NOT NULL,
  weekly_price  DOUBLE,
  short_stay_price DOUBLE,
  min_stay  TINYINT,
  additional_nights_price DOUBLE,
  pricing ENUM('Low', 'High'),
  discount_type  ENUM('Fixed', 'Percent'),
  discount  DOUBLE,
  `arrival` ENUM ('Any', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),

  PRIMARY KEY (`ratecard_id`, `date`),
  KEY (`ratecard_id`),
  KEY (`date`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_ratecards_date_ranges`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  ratecard_id  INT NOT NULL,
  starts  DATE NOT NULL,
  ends  DATE NOT NULL,
  weekly_price  DOUBLE,
  short_stay_price DOUBLE,
  min_stay  TINYINT,
  additional_nights_price DOUBLE,
  pricing ENUM('Low', 'High'),
  discount_type  ENUM('Fixed', 'Percent'),
  discount  DOUBLE,
  `arrival` ENUM ('Any', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),

  KEY (`ratecard_id`),
  KEY (`starts`)
)
ENGINE = InnoDB
CHARSET = UTF8;
