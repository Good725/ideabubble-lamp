/*
ts:2015-01-01 00:00:29
*/

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('surveys', 'Surveys', '1', '0', NULL);

DROP TABLE IF EXISTS `plugin_survey`;
CREATE TABLE `plugin_survey`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR(127),
    `start_date` DATETIME,
    `end_date` DATETIME,
    `store_answer` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
	`result_pdf_download` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
	`result_template_id` INT(11) NULL DEFAULT NULL,
	`publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
	`expiry` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
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

DROP TABLE IF EXISTS `plugin_survey_questions`;
CREATE TABLE `plugin_survey_questions`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR(127),
    `answer_id` INT(11) NULL DEFAULT NULL,
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

DROP TABLE IF EXISTS `plugin_survey_answers`;
CREATE TABLE IF NOT EXISTS `plugin_survey_answers`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR(127),
    `type_id` INT(11) NULL DEFAULT NULL,
    `group_name` VARCHAR(127),
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

INSERT INTO `plugin_survey_answers` VALUES (1,'Yes / No',1,'yes/no',1,0,1,NOW(),1,NOW()),(2,'Comment',2,'',1,0,1,NOW(),1,NOW()),(3,'Input',3,'',1,0,1,NOW(),1,NOW());

DROP TABLE IF EXISTS `plugin_survey_answer_options`;
CREATE TABLE `plugin_survey_answer_options`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `label` VARCHAR(127),
	`value` INT(11) NULL DEFAULT NULL ,
    `answer_id` INT(11) NULL DEFAULT NULL,
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

INSERT INTO `plugin_survey_answer_options` VALUES (1,'Yes',1,1,1,0,1,NOW(),1,NOW()),(2,'No',0,1,1,0,1,NOW(),1,NOW());

DROP TABLE IF EXISTS `plugin_survey_answer_types`;
CREATE TABLE `plugin_survey_answer_types`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `stub` VARCHAR(127) ,
    `title` VARCHAR(127) ,
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

INSERT INTO `plugin_survey_answer_types` VALUES (1,'radio','Radio Button',1,0,1,NOW(),1,NOW()),(2,'textarea','Text Area',1,0,1,NOW(),1,NOW()),(3,'input','Input',0,0,1,NOW(),1,NOW()),(4,'select','Selection Box',0,0,1,NOW(),1,NOW()),(5,'checkbox','Check Box',0,0,1,NOW(),1,NOW());

DROP TABLE IF EXISTS `plugin_survey_sequence`;
CREATE TABLE `plugin_survey_sequence`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR(127),
    `survey_id` INT(11) NULL DEFAULT NULL,
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

DROP TABLE IF EXISTS `plugin_survey_sequence_items`;
CREATE TABLE `plugin_survey_sequence_items`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `sequence_id` INT(11)  NULL DEFAULT NULL,
	`question_id` INT(11)  NULL DEFAULT NULL,
	`answer_option_id` INT(11)  NULL DEFAULT NULL,
	`action` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
	`target_id` INT(11)  NULL DEFAULT NULL,
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

DROP TABLE IF EXISTS `plugin_survey_has_questions`;
CREATE TABLE `plugin_survey_has_questions`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `survey_id` INT(11)  NULL DEFAULT NULL,
	`question_id` INT(11)  NULL DEFAULT NULL,
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

ALTER IGNORE  TABLE `plugin_survey_answer_options` ADD COLUMN `order_id` INT(11)  NULL DEFAULT NULL AFTER `answer_id`;

ALTER IGNORE TABLE `plugin_survey_sequence_items` DROP COLUMN `action`;
ALTER IGNORE TABLE `plugin_survey_sequence_items` ADD COLUMN `survey_action` INT(11)  NULL DEFAULT NULL;

-- ERROR! the query below wont work: no column named group in above queries
-- ALTER IGNORE TABLE `plugin_survey_answers` DROP COLUMN `group`;
-- END ERROR!

-- ERROR! the query below wont work: group_name already added in above queries
-- ALTER IGNORE TABLE `plugin_survey_answers` ADD COLUMN `group_name` INT(11)  NULL DEFAULT NULL;
-- END ERROR!

DELETE FROM `plugin_survey_answers`;
ALTER IGNORE TABLE `plugin_survey_answers` AUTO_INCREMENT=1;

UPDATE plugin_survey_answer_types SET publish=1;
