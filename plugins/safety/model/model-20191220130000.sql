/*
ts:2019-12-20 13:00:00
*/


CREATE TABLE `plugin_accidents_accidents` (
  `id`            INT        UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(255) NULL,

  `datetime`            TIMESTAMP NOT NULL,
  `reporter_id`         INT(11) NULL,
  `cause_description`   BLOB NULL,
  `description`         BLOB NULL,
  `able_to_take_action` INT(1) NULL,
  `location`            VARCHAR(1000) NULL,
  `actions_required`    BLOB NULL,
  `weather`             VARCHAR(1000) NULL,
  `severity`            ENUM('death', 'long-term incapacity', 'minor accident', 'near miss') NULL,
  `status`              ENUM('pending', 'duplicate', 'valid', 'invalid') NULL,

  `published`     TINYINT(1) NULL DEFAULT 1,
  `deleted`       TINYINT(1) NULL DEFAULT 0,
  `date_created`  TIMESTAMP  NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` TIMESTAMP  NULL,
  `created_by`    INT NULL,
  `modified_by`   INT NULL,
  PRIMARY KEY (`id`)
);


ALTER IGNORE TABLE `plugin_accidents_accidents`
ADD COLUMN `notes` BLOB NULL AFTER `modified_by`,
CHANGE COLUMN `location` `location_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `severity` `severity` ENUM('Death', 'Out-of-action more than one month', 'Out-of-action more than three days', 'Out-of-action three days or fewer', 'Near miss') NULL DEFAULT NULL ,
CHANGE COLUMN `status` `status` ENUM('Pending', 'Resolved') NULL DEFAULT NULL ;

-- MVP. We might store injured personal individually separately in future
ALTER TABLE `plugin_accidents_accidents` ADD COLUMN `injured_people` BLOB NULL AFTER `reporter_id` ;

-- Permissions
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`)
VALUES (0, 'accidents', 'Accidents', 'Access the accidents plugin');

-- Remove "out-of-action more than one month", relabel others
ALTER IGNORE TABLE `plugin_accidents_accidents`
CHANGE COLUMN `severity` `severity` ENUM('Death', 'Absent > 3 days', 'Absent ≤ 3 days', 'Near miss') NULL DEFAULT NULL
;

UPDATE `engine_plugins` SET `svg` = 'spam' WHERE `name` = 'accidents';


-- MVP. We might store witnesses separately in future
ALTER TABLE `plugin_accidents_accidents` ADD COLUMN `witnesses` BLOB NULL AFTER `injured_people` ;