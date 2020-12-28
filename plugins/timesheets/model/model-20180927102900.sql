/*
ts:2018-09-27 10:29:00
*/

CREATE TABLE `plugin_timesheets_requests` (
	`id` INT(11) NOT NULL,
	`staff_id` INT(11) NOT NULL,
	`department_id` INT(11) NOT NULL,
	`business_id` INT(11) NOT NULL,
	`timesheet_id` INT(11) NOT NULL,
	`todo_id` INT(11) NOT NULL,
	`schedule_id` INT(11) NOT NULL,
	`duration` INT(11) NOT NULL,
	`period_start_date` DATETIME NOT NULL,
	`period_end_date` DATETIME NOT NULL,
	`type` ENUM('course','internal') NOT NULL,
	`deleted` TINYINT(1) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`staff_updated_at` DATETIME NOT NULL,
	`manager_updated_at` DATETIME NULL DEFAULT NULL,
	`description` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `staff_id` (`staff_id`),
	INDEX `department_id` (`department_id`),
	INDEX `timesheet_id` (`timesheet_id`),
	INDEX `period_start_date_period_end_date` (`period_start_date`, `period_end_date`),
	INDEX `type` (`type`),
	INDEX `organization_id` (`business_id`),
	INDEX `todo_id` (`todo_id`),
	INDEX `schedule_id` (`schedule_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `plugin_timesheets_generator` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `plugin_timesheets_timesheets` (
	`id` INT NOT NULL,
	`staff_id` INT NOT NULL,
	`status` ENUM('open','peding','declined','approved') NOT NULL,
	`period_start_date` DATE NOT NULL,
	`period_end_date` DATE NOT NULL,
	`note` TEXT NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci';

