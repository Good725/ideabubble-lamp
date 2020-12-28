/*
ts:2020-04-17 17:00:00
*/

ALTER TABLE `plugin_todos_todos2_has_assigned_contacts`
ADD COLUMN `status`        ENUM('Open', 'Done', 'In progress') NULL DEFAULT 'Open' AFTER `contact_id`,
ADD COLUMN `date_created`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`,
ADD COLUMN `date_modified` TIMESTAMP NULL AFTER `date_created`
;

ALTER TABLE `plugin_todos_todos2_has_assigned_contacts`
ADD COLUMN `created_by`  INT NULL AFTER `date_modified`,
ADD COLUMN `modified_by` INT NULL AFTER `created_by`
;

ALTER TABLE `plugin_todos_todos2_has_assigned_contacts`
ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST,
ADD PRIMARY KEY (`id`);

CREATE TABLE `plugin_todos_todos2_file_submissions` (
  `id`            INT NOT NULL AUTO_INCREMENT,
  `todo_id`       INT(11) NULL,
  `contact_id`    INT(11) NULL,
  `file_id`       INT(11) NULL,
  `version`       INT(5) NULL,
  `date_created`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` TIMESTAMP NULL,
  `created_by`    INT NULL,
  `modified_by`   INT NULL,
  `deleted`       INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`));

ALTER TABLE `plugin_todos_todos2`
ADD COLUMN `file_uploads` INT(1) NOT NULL DEFAULT 0 AFTER `datetime`;
