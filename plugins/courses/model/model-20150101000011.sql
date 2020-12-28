/*
ts:2015-01-01 00:00:11
*/
-- -----------------------------------------------------
-- Table `plugin_courses_counties`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_counties` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 33
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(200) NOT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `file_id` TEXT NULL DEFAULT NULL ,
  `position` INT(3) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_categories_plugin_courses_categories` (`parent_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_categories_plugin_courses_categories`
  FOREIGN KEY (`parent_id` )
  REFERENCES `plugin_courses_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_levels`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_levels` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `level` VARCHAR(200) NOT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_cities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_cities` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `county_id` INT(10) UNSIGNED NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_cities_plugin_courses_counties` (`county_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_cities_plugin_courses_counties`
  FOREIGN KEY (`county_id` )
  REFERENCES `plugin_courses_counties` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_providers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_providers` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `address1` VARCHAR(200) NULL DEFAULT NULL ,
  `address2` VARCHAR(200) NULL DEFAULT NULL ,
  `address3` VARCHAR(200) NULL DEFAULT NULL ,
  `county_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `city_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `web_address` VARCHAR(200) NULL DEFAULT NULL ,
  `email` VARCHAR(200) NULL DEFAULT NULL ,
  `phone` VARCHAR(20) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_counties` (`county_id` ASC) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_cities` (`city_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_cities`
  FOREIGN KEY (`city_id` )
  REFERENCES `plugin_courses_cities` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_counties`
  FOREIGN KEY (`county_id` )
  REFERENCES `plugin_courses_counties` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_study_modes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_study_modes` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `study_mode` VARCHAR(200) NOT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_types` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(200) NOT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_years`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_years` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `year` VARCHAR(200) NOT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_courses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_courses` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `code` VARCHAR(64) NULL DEFAULT NULL ,
  `year_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `type_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `study_mode_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `provider_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `file_id` VARCHAR(200) NULL DEFAULT NULL ,
  `level_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `summary` LONGTEXT NULL DEFAULT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_courses_plugin_courses_categories` (`category_id` ASC) ,
  INDEX `fk_plugin_courses_courses_plugin_courses_years` (`year_id` ASC) ,
  INDEX `fk_plugin_courses_courses_plugin_courses_types` (`type_id` ASC) ,
  INDEX `fk_plugin_courses_courses_plugin_courses_levels` (`level_id` ASC) ,
  INDEX `fk_plugin_courses_courses_plugin_courses_study_modes` (`study_mode_id` ASC) ,
  INDEX `fk_plugin_courses_courses_plugin_courses_providers` (`provider_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_courses_plugin_courses_categories`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_courses_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_plugin_courses_levels`
  FOREIGN KEY (`level_id` )
  REFERENCES `plugin_courses_levels` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_plugin_courses_providers`
  FOREIGN KEY (`provider_id` )
  REFERENCES `plugin_courses_providers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_plugin_courses_study_modes`
  FOREIGN KEY (`study_mode_id` )
  REFERENCES `plugin_courses_study_modes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_plugin_courses_types`
  FOREIGN KEY (`type_id` )
  REFERENCES `plugin_courses_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_plugin_courses_years`
  FOREIGN KEY (`year_id` )
  REFERENCES `plugin_courses_years` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_location_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_location_types` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(200) NOT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_locations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_locations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `address1` VARCHAR(200) NOT NULL ,
  `address2` VARCHAR(200) NOT NULL ,
  `address3` VARCHAR(200) NOT NULL ,
  `county_id` INT(10) UNSIGNED NOT NULL ,
  `city_id` INT(10) UNSIGNED NOT NULL ,
  `capacity` INT(8) NULL DEFAULT NULL ,
  `location_type_id` INT(10) UNSIGNED NOT NULL ,
  `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `email` VARCHAR(200) NULL DEFAULT 'NULL' ,
  `phone` VARCHAR(100) NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_counties0` (`county_id` ASC) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_cities0` (`city_id` ASC) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_location_types0` (`location_type_id` ASC) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_locations0` (`parent_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_cities0`
  FOREIGN KEY (`city_id` )
  REFERENCES `plugin_courses_cities` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_counties0`
  FOREIGN KEY (`county_id` )
  REFERENCES `plugin_courses_counties` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_locations0`
  FOREIGN KEY (`parent_id` )
  REFERENCES `plugin_courses_locations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_location_types0`
  FOREIGN KEY (`location_type_id` )
  REFERENCES `plugin_courses_location_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_schedule_frequencies`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_schedule_frequencies` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `frequency` VARCHAR(200) NOT NULL ,
  `days` INT(3) NOT NULL ,
  `comment` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_schedules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_schedules` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `course_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `frequency_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `duration` VARCHAR(200) NOT NULL ,
  `start_date` DATETIME NULL DEFAULT NULL ,
  `end_date` DATETIME NULL DEFAULT NULL ,
  `weekdays_monday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `weekdays_tuesday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `weekdays_wednesday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `weekdays_thursday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `weekdays_friday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `weekdays_saturday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `weekdays_sunday` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `min_capacity` INT(10) NULL DEFAULT '0' ,
  `max_capacity` INT(10) NULL DEFAULT NULL ,
  `is_confirmed` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `location_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `trainer_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `is_fee_required` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `fee_amount` DECIMAL(7,2) NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_courses_locations_plugin_courses_courses1` (`course_id` ASC) ,
  INDEX `fk_plugin_courses_schedules_plugin_courses_schedule_frequencies0` (`frequency_id` ASC) ,
  INDEX `fk_plugin_courses_schedules_plugin_courses_schedule_providers0` (`trainer_id` ASC) ,
  INDEX `fk_plugin_courses_courses_locations_plugin_courses_locations1` (`location_id` ASC) ,
  INDEX `trainer_id` (`trainer_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_courses_locations_plugin_courses_courses1`
  FOREIGN KEY (`course_id` )
  REFERENCES `plugin_courses_courses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_locations_plugin_courses_locations1`
  FOREIGN KEY (`location_id` )
  REFERENCES `plugin_courses_locations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_schedules_plugin_courses_schedule_frequencies0`
  FOREIGN KEY (`frequency_id` )
  REFERENCES `plugin_courses_schedule_frequencies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 12
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_bookings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_bookings` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(100) NOT NULL ,
  `last_name` VARCHAR(100) NOT NULL ,
  `address` TEXT NULL DEFAULT NULL ,
  `email` VARCHAR(200) NULL DEFAULT NULL ,
  `gender` VARCHAR(1) NULL DEFAULT NULL ,
  `schedule_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `payment_details` text DEFAULT NULL,
  `paid` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `mobile` VARCHAR(20) NULL DEFAULT NULL ,
  `phone` VARCHAR(20) NULL DEFAULT NULL ,
  `teaching_co_reg` TINYINT(1) NULL DEFAULT NULL ,
  `teaching_co_number` VARCHAR(20) NULL DEFAULT NULL ,
  `comments` TEXT NULL DEFAULT NULL ,
  `school` VARCHAR(200) NULL DEFAULT NULL ,
  `school_address` TEXT NULL DEFAULT NULL ,
  `roll_no` TEXT NULL DEFAULT NULL ,
  `school_phone` VARCHAR(20) NULL DEFAULT NULL ,
  `school_county` VARCHAR(100) NULL DEFAULT NULL ,
  `county` VARCHAR(200) NULL DEFAULT NULL ,
  `county_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `key` VARCHAR(100) NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_counties` (`county_id` ASC) ,
  INDEX `fk_plugin_courses_schedules` (`schedule_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_counties`
  FOREIGN KEY (`county_id` )
  REFERENCES `plugin_courses_counties` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_schedules`
  FOREIGN KEY (`schedule_id` )
  REFERENCES `plugin_courses_schedules` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 12
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_courses_locations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_courses_locations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `course_id` INT(10) UNSIGNED NOT NULL ,
  `location_id` INT(10) UNSIGNED NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_courses_locations_plugin_courses_courses0` (`course_id` ASC) ,
  INDEX `fk_plugin_courses_courses_locations_plugin_courses_locations0` (`location_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_courses_locations_plugin_courses_courses0`
  FOREIGN KEY (`course_id` )
  REFERENCES `plugin_courses_courses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_courses_locations_plugin_courses_locations0`
  FOREIGN KEY (`location_id` )
  REFERENCES `plugin_courses_locations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugin_courses_trainers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_trainers` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(100) NOT NULL ,
  `last_name` VARCHAR(100) NOT NULL ,
  `address1` VARCHAR(200) NOT NULL ,
  `address2` VARCHAR(200) NOT NULL ,
  `address3` VARCHAR(200) NOT NULL ,
  `county_id` INT(10) UNSIGNED NOT NULL ,
  `city_id` INT(10) UNSIGNED NOT NULL ,
  `email` VARCHAR(200) NOT NULL ,
  `phone` VARCHAR(20) NOT NULL ,
  `details` TEXT NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_counties1` (`county_id` ASC) ,
  INDEX `fk_plugin_courses_providers_plugin_courses_cities1` (`city_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_cities1`
  FOREIGN KEY (`city_id` )
  REFERENCES `plugin_courses_cities` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plugin_courses_providers_plugin_courses_counties1`
  FOREIGN KEY (`county_id` )
  REFERENCES `plugin_courses_counties` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `plugin_courses_courses_images` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `course_id` int(10) UNSIGNED NOT NULL,
  `image` varchar(255),
  `date_created` datetime,
  `date_modified` timestamp NULL,
  `created_by` int(10),
  `updated_by` int(10),
  PRIMARY KEY (`id`),
  INDEX `fk_courses_to_courses_images` (`course_id` ASC) ,
  CONSTRAINT `fk_courses_to_courses_images`
  FOREIGN KEY (`course_id`)
  REFERENCES `plugin_courses_courses` (`id`)
    ON UPDATE CASCADE
    ON DELETE NO ACTION
);

-- -----------------------------------------------------
-- Table `plugin_courses_schedules_events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_schedules_events` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `schedule_id` INT(10) UNSIGNED NOT NULL ,
  `datetime_start` DATETIME NOT NULL ,
  `datetime_end` DATETIME NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` DATETIME NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_courses_schedules_to_events` (`schedule_id` ASC) ,
  CONSTRAINT `fk_plugin_courses_schedules_to_events`
  FOREIGN KEY (`schedule_id` )
  REFERENCES `plugin_courses_schedules` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;
-- INSERT DATA TO PLUGINS

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`) VALUES ('courses', 'Courses', '1');


-- FILL UP TABLE WITH COUNTY DATA

INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Antrim');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Armagh');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Carlow');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Cavan');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Clare');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Cork');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Derry');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Donegal');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Down');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Dublin');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Fermanagh');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Galway');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Kerry');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Kildare');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Kilkenny');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Laois');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Leitrim');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Limerick');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Longford');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Louth');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Mayo');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Meath');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Monaghan');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Offaly');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Roscommon');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Sligo');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Tipperary');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Tyrone');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Waterford');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Westmeath');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Wexford');
INSERT INTO `plugin_courses_counties` (`name`) VALUES ('Wicklow');

ALTER IGNORE TABLE plugin_courses_courses ADD book_button TinyInt(1) NOT NULL default '0';

ALTER IGNORE TABLE plugin_courses_schedules MODIFY plugin_courses_schedules.`start_date` datetime;
ALTER IGNORE TABLE plugin_courses_schedules MODIFY plugin_courses_schedules.`end_date` datetime;
ALTER IGNORE TABLE plugin_courses_schedules_events ADD timetable_id INT(4);

-- -----------------------------------------------------
-- Table `plugin_courses_timetable`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugin_courses_timetable` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `timetable_name` varchar (60) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8;

ALTER IGNORE TABLE plugin_courses_timetable ADD `date_created` DATETIME NULL DEFAULT NULL;
ALTER IGNORE TABLE plugin_courses_timetable ADD `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER IGNORE TABLE plugin_courses_timetable ADD `created_by` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER IGNORE TABLE plugin_courses_timetable ADD `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER IGNORE TABLE plugin_courses_timetable ADD `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1';
ALTER IGNORE TABLE plugin_courses_timetable ADD `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0';

UPDATE `plugins` SET icon = 'courses.png' WHERE friendly_name = 'Courses';
UPDATE `plugins` SET `plugins`.`order` = 1 WHERE friendly_name = 'Courses';

UPDATE `plugins` SET `plugins`.`requires_media` = 1 and `plugins`.`media_folder` = 'courses' WHERE friendly_name = 'Courses';

-- -----------------------------------------------------
-- WPPROD-267 - Services option for extrabubble
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugin_courses_autotimetables` (
 `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR(200) NULL ,
 `category_id` INT(10) UNSIGNED NULL ,
 `location_id` INT(10) UNSIGNED NULL ,
 `description` BLOB NULL ,
 `date_start` DATETIME NULL ,
 `date_end` DATETIME NULL ,
 `created_by` INT(11) UNSIGNED NULL DEFAULT NULL ,
 `modified_by` INT(11) UNSIGNED NULL DEFAULT NULL ,
 `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
 `date_modified` DATETIME NULL DEFAULT NULL ,
 `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
 `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
 PRIMARY KEY (`id`) ,
 FOREIGN KEY (`category_id`)
    REFERENCES `plugin_courses_categories`(`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION ,
 FOREIGN KEY (`location_id`)
    REFERENCES `plugin_courses_locations`(`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION ,
 FOREIGN KEY (`created_by`)
    REFERENCES `users`(`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION ,
 FOREIGN KEY (`modified_by`)
    REFERENCES `users`(`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
 )
 ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_courses_autotimetables_years` (
 `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `autotimetable_id` INT(11) UNSIGNED NOT NULL ,
 `year_id` INT(10) UNSIGNED NOT NULL ,
 PRIMARY KEY (`id`) ,
 FOREIGN KEY (`autotimetable_id`)
 REFERENCES `plugin_courses_autotimetables`(`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION ,
 FOREIGN KEY (`year_id`)
 REFERENCES `plugin_courses_years`(`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
 )
 ENGINE = InnoDB;

-- ------------------------------------------
-- KES-247 Fwd: 5th yr Art Portfolio
-- ------------------------------------------
ALTER IGNORE TABLE `plugin_courses_schedules_events` ADD `trainer_id` INT;

CREATE TABLE IF NOT EXISTS `plugin_courses_repeat` (
 `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR(255),
 PRIMARY KEY (`id`)
 )
 ENGINE = InnoDB;

ALTER IGNORE TABLE `plugin_courses_schedules` ADD `repeat` INT;

INSERT IGNORE INTO `plugin_courses_repeat` (`id`,`name`) VALUES ('1','Daily - No Weekends'),('2','Daily - Including Weekends'),('3','Weekly'),('4','Fortnightly'),('5','Monthly'),('6','Custom');

ALTER IGNORE TABLE plugin_courses_timetable MODIFY plugin_courses_timetable.`timetable_name` VARCHAR(255);

-- ------------------------------------------
-- WPPROD-574 Subjects CRUD for courses plugin
-- ------------------------------------------
 CREATE TABLE IF NOT EXISTS `plugin_courses_subjects` (
   `id`            INT          UNSIGNED NOT NULL AUTO_INCREMENT,
   `name`          VARCHAR(255)          NOT NULL ,
   `summary`       TEXT                  NULL     DEFAULT NULL ,
   `date_created`  TIMESTAMP             NULL     DEFAULT CURRENT_TIMESTAMP ,
   `date_modified` TIMESTAMP             NULL ,
   `created_by`    INT(11)               NULL ,
   `modified_by`   INT(11)               NULL ,
   `publish`       TINYINT(1)            NOT NULL DEFAULT 1 ,
   `deleted`       TINYINT(1)            NOT NULL DEFAULT 0 ,
   PRIMARY KEY (`id`) );

ALTER IGNORE TABLE plugin_courses_locations ADD `online_capacity` INT(11) DEFAULT 0;

ALTER IGNORE TABLE plugin_courses_schedules ADD `payment_type` INT(11) DEFAULT 1;

ALTER IGNORE TABLE plugin_courses_schedules ADD `run_off_location` INT(11) DEFAULT 0;

-- ------------------------------------------
-- Move study_mode_id from courses to schedules
-- ------------------------------------------
ALTER IGNORE TABLE `plugin_courses_schedules` ADD COLUMN `study_mode_id` INT(10) UNSIGNED NULL DEFAULT NULL;

UPDATE `plugin_courses_schedules` `schedule`
LEFT JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id`
SET `schedule`.`study_mode_id` = `course`.`study_mode_id`;

ALTER IGNORE TABLE `plugin_courses_courses` DROP FOREIGN KEY `fk_plugin_courses_courses_plugin_courses_study_modes` ;
ALTER IGNORE TABLE `plugin_courses_courses` DROP COLUMN `study_mode_id` ;

ALTER IGNORE TABLE plugin_courses_schedules ADD `run_off_schedule` INT(11) DEFAULT 0;

-- ------------------------------------------
-- KES-42 Subjects to have colour selector
-- ------------------------------------------
ALTER IGNORE TABLE `plugin_courses_subjects` ADD COLUMN `color` VARCHAR(127) NULL AFTER `summary` ;
ALTER IGNORE TABLE `plugin_courses_courses`  ADD COLUMN `subject_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `level_id`;

ALTER IGNORE TABLE `plugin_courses_courses_images` ADD COLUMN `deleted` INT(1) NOT NULL DEFAULT 0  AFTER `updated_by` ;


-- --------------------------------------------
-- KES-242 - Grinds Payment
-- --------------------------------------------
ALTER IGNORE TABLE `plugin_courses_categories` ADD COLUMN  `grinds_tutorial` TINYINT NOT NULL DEFAULT '0';

ALTER IGNORE TABLE `plugin_courses_schedules` ADD COLUMN `rental_fee` FLOAT(4,2) DEFAULT 0.0 AFTER `fee_amount`;

ALTER IGNORE TABLE `plugin_courses_providers` ADD COLUMN `type_id` INT(10)DEFAULT NULL AFTER `name` ;
-- -----------------------------------------------------
-- Table `plugin_courses_providers_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_courses_providers_types` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(200) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = utf8;

  INSERT IGNORE INTO `plugin_courses_location_types` (`type`) VALUE ('Buisiness');
  INSERT IGNORE INTO `plugin_courses_location_types` (`type`) VALUE ('School');
  DELETE IGNORE FROM `plugin_courses_location_types` WHERE `type`='Buisiness';
  DELETE IGNORE FROM `plugin_courses_location_types` WHERE `type` ='School';
  INSERT IGNORE INTO `plugin_courses_providers_types` (`type`) VALUE ('Buisiness');
  INSERT IGNORE INTO `plugin_courses_providers_types` (`type`) VALUE ('School');

ALTER IGNORE TABLE `plugin_courses_schedules` ADD COLUMN `start_date_only` INT(1) NOT NULL DEFAULT 0  AFTER `run_off_schedule` ;

DELETE FROM plugin_courses_schedules_events WHERE `delete` = 1 AND `publish` = 1;

ALTER IGNORE TABLE plugin_courses_schedules_events MODIFY COLUMN `date_modified` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP();

UPDATE plugin_courses_schedules_events SET plugin_courses_schedules_events.trainer_id = (SELECT plugin_courses_schedules.trainer_id FROM plugin_courses_schedules WHERE plugin_courses_schedules_events.schedule_id = plugin_courses_schedules.id);

CREATE TABLE  IF NOT EXISTS `plugin_courses_schedules_has_intervals`(
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `schedule_id` INT(10) UNSIGNED,
    `custom_frequency` INT(11) UNSIGNED,
    PRIMARY KEY (`id`)
    )
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = utf8;

CREATE TABLE  IF NOT EXISTS `plugin_courses_schedules_intervals` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `interval_id` INT(10),
    `day` VARCHAR(20),
    `start_time` VARCHAR(5),
    `end_time` VARCHAR(5),
    `trainer_id` INT(10),
    PRIMARY KEY (`id`)
    )
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = utf8;

UPDATE plugin_courses_schedules SET plugin_courses_schedules.end_date = (SELECT MAX(plugin_courses_schedules_events.datetime_end) FROM plugin_courses_schedules_events WHERE plugin_courses_schedules.id = plugin_courses_schedules_events.schedule_id AND plugin_courses_schedules_events.publish = 1 AND plugin_courses_schedules_events.`delete`=0);

CREATE TABLE IF NOT EXISTS `plugin_courses_academic_year`
(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR(127),
    `start_date` DATETIME,
    `end_date` DATETIME,
	`status` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
	`publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
	`deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
	`created_by` INT(11) NULL DEFAULT NULL,
	`created_on` DATETIME NOT NULL,
	`updated_by` INT(11) NULL DEFAULT NULL,
	`updated_on` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET = utf8;

ALTER IGNORE TABLE `plugin_courses_schedules` ADD COLUMN `academic_year_id` INT(11) NULL DEFAULT NULL;

ALTER IGNORE TABLE `plugin_courses_schedules_intervals` ADD COLUMN `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1';
ALTER IGNORE TABLE `plugin_courses_schedules_intervals` ADD COLUMN `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ;

ALTER IGNORE TABLE `plugin_courses_schedules` CHANGE `start_date_only` `calendar_display` INT(10) DEFAULT 0;
