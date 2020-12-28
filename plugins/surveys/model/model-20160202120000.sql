/*
ts:2016-02-02 12:00:00
*/

DROP TABLE IF EXISTS `plugin_survey_groups`;
CREATE TABLE IF NOT EXISTS `plugin_survey_groups`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR(127),
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

INSERT INTO `plugin_survey_groups` (`title`,`created_on`,`updated_on`) VALUES ('Group 1',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP()),('Group 2',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP()),('Group 3',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP()),('Group 4',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP()),('Group 5',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP());

ALTER IGNORE TABLE `plugin_survey` ADD COLUMN `pagination`  TINYINT(1) UNSIGNED NULL DEFAULT '0';
ALTER IGNORE TABLE `plugin_survey_has_questions` ADD COLUMN `group_id`  INT(11) NULL DEFAULT NULL AFTER `survey_id`;

DROP TABLE IF EXISTS `plugin_survey_has_groups`;
CREATE TABLE IF NOT EXISTS `plugin_survey_has_groups`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `survey_id` INT(11) NULL DEFAULT NULL,
    `group_id` INT(11) NULL DEFAULT NULL,
    `order_id` INT(11) NULL DEFAULT NULL,
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