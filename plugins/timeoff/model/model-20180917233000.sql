/*
ts:2018-09-17 23:30:00
*/

ALTER TABLE `plugin_timeoff_requests`
	ALTER `organization_id` DROP DEFAULT;
ALTER TABLE `plugin_timeoff_requests`
	CHANGE COLUMN `organization_id` `business_id` INT(11) NOT NULL AFTER `department_id`;

CREATE TABLE `plugin_contacts3_relations` (
	`child_id` INT(11) NOT NULL,
	`parent_id` INT(11) NOT NULL,
	`role` ENUM('staff','manager') NULL,
	`position` VARCHAR(50) NULL,
	PRIMARY KEY (`child_id`, `parent_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
