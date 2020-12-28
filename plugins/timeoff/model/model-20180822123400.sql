/*
ts:2018-08-22 12:34:00
*/
CREATE TABLE `plugin_timeoff_requests` (
	`id` INT(11) NOT NULL,
	`staff_id` INT(11) NOT NULL,
	`department_id` INT(11) NOT NULL,
	`days` FLOAT NOT NULL,
	`period_start_date` INT(11) NOT NULL,
	`period_end_date` INT(11) NOT NULL,
	`type` ENUM('annual','bereavement','sick','other') NOT NULL,
	`status` ENUM('pending','approved','declined','cancelled') NOT NULL,
	`created_at` INT(11) NOT NULL,
	`staff_updated_at` INT(11) NOT NULL,
	`manager_updated_at` INT(11) NULL,
	PRIMARY KEY (`id`),
	INDEX `staff_id` (`staff_id`),
	INDEX `department_id` (`department_id`),
	INDEX `period_start_date_period_end_date` (`period_start_date`, `period_end_date`),
	INDEX `type` (`type`),
	INDEX `status` (`status`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

  CREATE TABLE `plugin_timeoff_notes` (
	`id` INT NOT NULL,
	`request_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`created_at` INT NOT NULL,
	`content` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `request_id` (`request_id`)
)
COLLATE='utf8_general_ci';

CREATE TABLE `plugin_timeoff_departments` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci';

CREATE TABLE `plugin_timeoff_departments_staff` (
	`department_id` INT(11) NOT NULL,
	`staff_id` INT(11) NOT NULL,
	`role` SET('staff','manager') NOT NULL,
	`position` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`department_id`, `staff_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `plugin_timeoff_generator` (
	`id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
