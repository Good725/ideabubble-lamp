/*
ts:2018-09-11 20:26:00
*/
insert into plugin_messaging_notification_templates
  (name, description, driver, type_id, subject, sender, message, linked_plugin_name, usable_parameters_in_template)
values
  (
    'timeoff-manager-request-created',
    'Email sent to manager when new timeoff request is submitted',
    'EMAIL',
    '1',
    'New timeoff request',
    'testing@websitecms.ie',
    '$staff submitted new request: $link',
    'timeoff',
    '$staff, $link'
  ),
  (
    'timeoff-staff-approved',
    'Email for staff that request was approved',
    'EMAIL',
    '1',
    'Request approved',
    'testing@websitecms.ie',
    'Your request was approved: $link',
    'timeoff',
    '$link'
  ),
  (
    'timeoff-staff-declined',
    'Email for staff that request was declined',
    'EMAIL',
    '1',
    'Request declined',
    'testing@websitecms.ie',
    'Your request was declined: $link',
    'timeoff',
    '$link'
  );

CREATE TABLE `plugin_timeoff_config` (
	`name` VARCHAR(50) NOT NULL,
	`level` ENUM('global','organization','department','contact') NOT NULL,
	`item_id` INT NOT NULL,
	`value` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`name`, `level`, `item_id`)
)
COLLATE='utf8_general_ci' ENGINE=InnoDB;

INSERT INTO `plugin_timeoff_config` (`name`, `level`, `item_id`, `value`) VALUES
	('timeoff.days_available', 'global', 0, '20'),
	('timeoff.day_length', 'global', 0, '8');


ALTER TABLE `plugin_timeoff_requests`
	ALTER `days` DROP DEFAULT;
ALTER TABLE `plugin_timeoff_requests`
	CHANGE COLUMN `days` `duration` INT(11) NOT NULL AFTER `department_id`;

ALTER TABLE `plugin_timeoff_requests`
CHANGE COLUMN `type` `type` ENUM('annual','bereavement','sick','other','lieu') NOT NULL;

ALTER TABLE `plugin_timeoff_requests`
	ADD COLUMN `organization_id` INT(11) NOT NULL AFTER `department_id`,
	ADD INDEX `organization_id` (`organization_id`);


ALTER TABLE `plugin_timeoff_requests`
	ALTER `period_start_date` DROP DEFAULT,
	ALTER `period_end_date` DROP DEFAULT,
	ALTER `created_at` DROP DEFAULT,
	ALTER `staff_updated_at` DROP DEFAULT;
ALTER TABLE `plugin_timeoff_requests`
	CHANGE COLUMN `period_start_date` `period_start_date` DATETIME NOT NULL AFTER `duration`,
	CHANGE COLUMN `period_end_date` `period_end_date` DATETIME NOT NULL AFTER `period_start_date`,
	CHANGE COLUMN `created_at` `created_at` DATETIME NOT NULL AFTER `status`,
	CHANGE COLUMN `staff_updated_at` `staff_updated_at` DATETIME NOT NULL AFTER `created_at`,
	CHANGE COLUMN `manager_updated_at` `manager_updated_at` DATETIME NULL DEFAULT NULL AFTER `staff_updated_at`;

ALTER TABLE `plugin_timeoff_notes`
	ALTER `created_at` DROP DEFAULT;
ALTER TABLE `plugin_timeoff_notes`
	CHANGE COLUMN `created_at` `created_at` DATETIME NOT NULL AFTER `user_id`;



