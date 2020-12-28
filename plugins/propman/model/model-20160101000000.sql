/*
ts:2016-01-01 00:00:00
*/

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`, `icon`, `order`)	VALUES ('propman', 'Property Management', 1, 1, null, '', 99);

CREATE TABLE `plugin_propman_groups`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  address1 VARCHAR(100),
  address2 VARCHAR(100),
  countryId INT,
  countyId  INT,
  city  VARCHAR(100),
  postcode  VARCHAR(20),
  latitude DOUBLE,
  longitude DOUBLE,
  total_properties  INT,
  host_contact_id INT,
  arrival_details TEXT,
  created DATETIME,
  updated DATETIME,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`name`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_ratecards`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL,
  period_id INT NOT NULL,
  weekly_price  DOUBLE,
  midweek_price DOUBLE,
  weekend_price DOUBLE,
  pricing ENUM('Low', 'High'),
  discountType  ENUM('Fixed', 'Percent'),
  discount  DOUBLE,
  created DATETIME,
  updated DATETIME,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`period_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_ratecards_weeks`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  ratecard_id INT NOT NULL,
  starts  DATE NOT NULL,
  ends  DATE NOT NULL,
  weekly_price  DOUBLE,
  midweek_price DOUBLE,
  weekend_price DOUBLE,
  min_stay  TINYINT,
  pricing ENUM('Low', 'High'),
  discount_type  ENUM('Fixed', 'Percent'),
  discount  DOUBLE,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`ratecard_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_ratecards_prices`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  ratecard_id INT NOT NULL,
  date  DATE NOT NULL,
  price DOUBLE NOT NULL,
  arrive_and_depart  TINYINT(1) NOT NULL DEFAULT 0,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`ratecard_id`),
  KEY (`date`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_groups_has_ratecards`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  group_id  INT NOT NULL,
  ratecard_id INT NOT NULL,

  KEY (`group_id`),
  KEY (`ratecard_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_periods`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL,
  starts DATE NOT NULL,
  ends DATE NOT NULL,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_groups_calendar`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  group_id  INT NOT NULL,
  date  DATE NOT NULL,
  available TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`date`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_building_types`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_property_types`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL,
  bedrooms  SMALLINT,
  sleep SMALLINT,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_facility_groups`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort TINYINT DEFAULT 0,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_facility_types`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  facility_group_id INT NOT NULL,
  name  VARCHAR(100) NOT NULL,
  sort TINYINT DEFAULT 0,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_suitability_groups`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort TINYINT DEFAULT 0,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_suitability_types`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  suitability_group_id INT NOT NULL,
  name  VARCHAR(100) NOT NULL,
  sort TINYINT DEFAULT 0,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_properties`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  building_type_id  INT NOT NULL,
  property_type_id  INT NOT NULL,
  ref_code  VARCHAR(50) NOT NULL,
  beds_single SMALLINT NOT NULL DEFAULT 0,
  beds_double SMALLINT NOT NULL DEFAULT 0,
  beds_king SMALLINT NOT NULL DEFAULT 0,
  beds_bunks SMALLINT NOT NULL DEFAULT 0,
  max_occupancy SMALLINT,
  summary MEDIUMTEXT,
  description MEDIUMTEXT,
  address1 VARCHAR(100),
  address2 VARCHAR(100),
  country_id INT,
  county_id  INT,
  city  VARCHAR(100),
  postcode  VARCHAR(20),
  latitude DOUBLE,
  longitude DOUBLE,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`group_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_properties_linked`
(
  property_id_1 INT NOT NULL,
  property_id_2 INT NOT NULL,

  KEY (`property_id_1`),
  KEY (`property_id_2`),
  PRIMARY KEY (`property_id_1`, `property_id_2`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_properties_has_facility`
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  facility_type_id INT NOT NULL,
  surcharge DOUBLE,
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`property_id`),
  KEY (`facility_type_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_properties_has_suitability`
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  suitability_type_id INT NOT NULL,
  surcharge DOUBLE,
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`property_id`),
  KEY (`suitability_type_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_propman_properties_has_media`
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  media_id INT NOT NULL,
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`property_id`),
  KEY (`media_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;

ALTER TABLE `plugin_propman_properties_has_media` ADD COLUMN `sort` INT;
ALTER TABLE `plugin_propman_properties_linked` ADD COLUMN `sort` INT;
