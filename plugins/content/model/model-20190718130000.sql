/*
ts:2019-07-18 13:00:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('content', 'Content', '0', '0');

CREATE TABLE `plugin_content_content` (
  `id`            INT NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(255) NOT NULL,
  `parent_id`     INT NULL,
  `type_id`       INT NULL,
  `duration`      INT NULL,
  `text`          MEDIUMTEXT NULL,
  `file_id`       INT NULL,
  `file_url`      VARCHAR(255) NULL,
  `survey_id`     INT NULL,
  `publish`       TINYINT(1) NULL DEFAULT 1,
  `deleted`       TINYINT(1) NULL DEFAULT 0,
  `date_created`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` TIMESTAMP NULL,
  `created_by`    INT NULL,
  `modified_by`   INT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `plugin_content_content` ADD COLUMN `order` INT NULL AFTER `parent_id`;

CREATE TABLE `plugin_content_types` (
  `id`            INT NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(255) NOT NULL,
  `friendly_name` VARCHAR(255) NOT NULL,
  `publish`       TINYINT(1) NULL DEFAULT 1,
  `deleted`       TINYINT(1) NULL DEFAULT 0,
  `date_created`  TIMESTAMP NULL,
  `date_modified` TIMESTAMP NULL,
  `created_by`    INT NULL,
  `modified_by`   INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC));

INSERT INTO `plugin_content_types` (`name`, `friendly_name`) VALUES
('pdf',     'PDF'),
('youtube', 'YouTube link'),
('text',    'text'),
('scorm',   'SCORM file');

-- Allow a schedule to be linked to a content tree
ALTER TABLE `plugin_courses_schedules` ADD COLUMN `content_id` INT NULL AFTER `subject_id`;

UPDATE `plugin_content_types` SET `friendly_name` = 'Text' WHERE `name` = 'text';

UPDATE `plugin_content_types` SET `name` = 'video', `friendly_name` = 'Video' WHERE `name` = 'youtube';
UPDATE `plugin_content_types` SET `publish` = '0' WHERE `name` = 'scorm';


CREATE TABLE `plugin_content_progress` (
  `id`            INT        UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       INT        UNSIGNED NOT NULL,
  `content_id`    INT        UNSIGNED NOT NULL,
  `section_id`    INT        UNSIGNED NOT NULL,
  `is_complete`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,

  `publish`       TINYINT(1) NULL DEFAULT 1,
  `deleted`       TINYINT(1) NULL DEFAULT 0,
  `date_created`  TIMESTAMP NULL,
  `date_modified` TIMESTAMP NULL,
  `created_by`    INT NULL,
  `modified_by`   INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_content` (`user_id`, `content_id`, `section_id`)
);

