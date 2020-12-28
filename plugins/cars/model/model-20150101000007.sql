/*
ts:2015-01-01 00:00:07
*/
/* There is no time allotted to properly model this car plugin. We will then dump the data to this table.*/

CREATE TABLE IF NOT EXISTS `plugin_cars_cars`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`title` VARCHAR(255),
`category_id` INT(11),
`publish` INT(1) DEFAULT 1,
`import_overwrite` INT(1) DEFAULT 1,
`make` VARCHAR(255),
`model` VARCHAR(255),
`price` VARCHAR(255) DEFAULT '0',
`engine` VARCHAR(255),
`body_type` VARCHAR(255),
`transmission` VARCHAR(255),
`year` VARCHAR(255),
`color` VARCHAR(255),
`mileage` VARCHAR(255),
`no_of_owners` VARCHAR(255),
`location` VARCHAR(255),
`doors` VARCHAR(255),
`nct_expiry` VARCHAR(255),
`type` VARCHAR(255),
`odometer` VARCHAR(255),
`no_of_seats` VARCHAR(255),
`extra` VARCHAR(255),
`comments` TEXT,
`additional_info` TEXT,
`seo_title` VARCHAR(255),
`seo_keywords` MEDIUMTEXT,
`seo_description` MEDIUMTEXT,
`seo_footer` TEXT,
`delete` INT(1),
PRIMARY KEY (`id`)) ENGINE=InnoDB;

INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`, `note`) VALUES
('car_csv_template','CSV Template for Cars','1','both', 'text','Cars','0', NULL,'The CSV Template for Car Import.');

INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`, `note`) VALUES
('car_csv_url','CSV URL for Car Import','1','both', 'text','Cars','0', NULL,'The CSV URL for Car Import.');

ALTER IGNORE TABLE `plugin_cars_cars`
ADD COLUMN `dealer_id`     INT(11)      NULL DEFAULT NULL  AFTER `additional_info` ,
ADD COLUMN `dealer_domain` VARCHAR(255) NULL DEFAULT NULL  AFTER `dealer_id` ,
ADD COLUMN `description`   VARCHAR(255) NULL DEFAULT NULL  AFTER `dealer_domain` ,
ADD COLUMN `stock`         VARCHAR(255) NULL DEFAULT NULL  AFTER `description` ,
ADD COLUMN `is_kilometer`  INT(1)       NULL DEFAULT NULL  AFTER `stock` ,
ADD COLUMN `options`       VARCHAR(255) NULL DEFAULT NULL  AFTER `is_kilometer` ,
ADD COLUMN `fuel`          VARCHAR(255) NULL DEFAULT NULL  AFTER `options` ,
ADD COLUMN `photo`         BLOB         NULL DEFAULT NULL  AFTER `fuel` ,
ADD COLUMN `category`      VARCHAR(255) NULL DEFAULT NULL  AFTER `photo` ;

CREATE TABLE IF NOT EXISTS `plugin_cars_categories` (
  `id`            INT(11)            NOT NULL AUTO_INCREMENT ,
  `title`         VARCHAR(255)       NOT NULL ,
  `order`         INT(11)            NULL     DEFAULT NULL ,
  `summary`       VARCHAR(255)       NULL     DEFAULT NULL ,
  `description`   VARCHAR(255)       NULL     DEFAULT NULL ,
  `created_by`    INT(11)   UNSIGNED NULL ,
  `modified_by`   INT(11)   UNSIGNED NULL ,
  `date_created`  TIMESTAMP          NULL     DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP          NULL ,
  `publish`       INT(1)             NOT NULL DEFAULT '1' ,
  `deleted`       INT(1)             NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

-- DRM-25 Images not showing on front end
ALTER IGNORE TABLE `plugin_cars_cars` CHANGE COLUMN `photo` `photo` TEXT NULL DEFAULT NULL ;
