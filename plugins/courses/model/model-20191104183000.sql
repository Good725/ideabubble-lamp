/*
ts:2019-11-04 18:30:00
*/

CREATE TABLE `plugin_courses_specs` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`             VARCHAR(100) NULL,
  `title`            VARCHAR(100) NULL,
  `course_id`        INT          NULL,
  `provider_id`      INT          NULL,
  `summary`          BLOB         NULL,
  `qqi_component_id` INT          NULL,
  `published`        TINYINT(1)   NULL DEFAULT 1,
  `deleted`          TINYINT(1)   NULL DEFAULT 0,
  `date_created`     TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified`    TIMESTAMP    NULL,
  `created_by`       INT          NULL,
  `modified_by`      INT          NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `plugin_courses_specs_have_delivery_modes` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `spec_id`          INT          NOT NULL,
  `delivery_mode_id` INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `spec_delivery_mode_UNIQUE` (`spec_id` ASC, `delivery_mode_id` ASC)
);

CREATE TABLE `plugin_courses_specs_have_recommended_material` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `spec_id`    INT          NOT NULL,
  `product_id` INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `spec_recommended_material_UNIQUE` (`spec_id` ASC, `product_id` ASC)
);

ALTER TABLE `plugin_courses_specs` ADD COLUMN `version` VARCHAR(100) NULL AFTER `title`;
ALTER TABLE `plugin_courses_specs` ADD COLUMN `requirement_type_id` INT NULL AFTER `qqi_component_id`;

INSERT INTO `engine_lookup_fields` (`name`) VALUES ('QQI component');
INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Delivery mode');
INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Requirement type');

INSERT INTO `engine_lookup_values` (`field_id`, `label`) VALUES (
  (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Requirement type' LIMIT 1),
  'Elective'
),
(
  (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Requirement type' LIMIT 1),
  'Mandatory'
);

ALTER TABLE `plugin_courses_specs` DROP COLUMN `course_id`;

ALTER TABLE `plugin_courses_courses` ADD COLUMN `curriculum_id` INT NULL AFTER `schedule_allow_price_override`;

CREATE TABLE `plugin_courses_curriculums` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(100) NULL,
  `summary`          BLOB         NULL,
  `published`        TINYINT(1)   NULL DEFAULT 1,
  `deleted`          TINYINT(1)   NULL DEFAULT 0,
  `date_created`     TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified`    TIMESTAMP    NULL,
  `created_by`       INT          NULL,
  `modified_by`      INT          NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `plugin_courses_curriculums_have_specs` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `curriculum_id` INT          NOT NULL,
  `spec_id`       INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `curriculum_spec_UNIQUE` (`curriculum_id` ASC, `spec_id` ASC)
);

ALTER TABLE `plugin_courses_subjects` ADD COLUMN `aims` BLOB NULL DEFAULT NULL AFTER `summary`;

ALTER TABLE `plugin_courses_specs` ADD COLUMN `subject_id` INT NULL DEFAULT NULL AFTER `version`;

ALTER TABLE `plugin_courses_curriculums` ADD COLUMN `content_id` INT NULL DEFAULT NULL AFTER `summary`;

CREATE TABLE `plugin_courses_learning_outcomes` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(100) NULL,
  `published`        TINYINT(1)   NULL DEFAULT 1,
  `deleted`          TINYINT(1)   NULL DEFAULT 0,
  `date_created`     TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified`    TIMESTAMP    NULL,
  `created_by`       INT          NULL,
  `modified_by`      INT          NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `plugin_content_has_learning_outcomes` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_id`          INT          NOT NULL,
  `learning_outcome_id` INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `content_learning_outcome_UNIQUE` (`content_id` ASC, `learning_outcome_id` ASC)
);

CREATE TABLE `plugin_courses_curriculums_have_learning_outcomes` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order`               INT          NULL,
  `curriculum_id`       INT          NOT NULL,
  `learning_outcome_id` INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `curriculum_learning_outcome_UNIQUE` (`curriculum_id` ASC, `learning_outcome_id` ASC)
);

ALTER TABLE `plugin_courses_learning_outcomes` CHANGE COLUMN `title` `title` VARCHAR(1023) NULL DEFAULT NULL ;

INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Learning methodology');

CREATE TABLE `plugin_courses_subjects_have_learning_methodologies` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order`                   INT          NULL,
  `subject_id`              INT          NOT NULL,
  `learning_methodology_id` INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `subject_learning_methodology_UNIQUE` (`subject_id` ASC, `learning_methodology_id` ASC)
);

ALTER TABLE `plugin_courses_specs` ADD COLUMN `grading_schema_id` INT NULL AFTER `requirement_type_id`;

ALTER TABLE `plugin_courses_subjects` ADD COLUMN `assessment_methods` BLOB NULL DEFAULT NULL AFTER `aims`;

ALTER TABLE `plugin_courses_credits` CHANGE COLUMN `type` `type` ENUM('Practical', 'Theory', 'Assignment', 'Additional Study', 'Exam') NULL DEFAULT NULL ;

ALTER TABLE `plugin_courses_credits`  ADD COLUMN `spec_id` INT NULL DEFAULT NULL AFTER `subject_id`;

ALTER TABLE `plugin_courses_credits`  ADD COLUMN `study_mode_id` INT NULL DEFAULT NULL AFTER `spec_id`;

CREATE TABLE `plugin_courses_spec_marks` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `spec_id`    INT          NOT NULL,
  `type`       VARCHAR(100) NULL,
  `mark`       INT(3)       NULL,
  PRIMARY KEY (`id`),
  INDEX `spec_type_UNIQUE` (`spec_id` ASC, `type` ASC)
);

ALTER TABLE `plugin_courses_specs` ADD COLUMN `number_of_credits` INT NULL AFTER `grading_schema_id`;
ALTER TABLE `plugin_courses_specs` ADD COLUMN `number_of_exams`   INT NULL AFTER `number_of_credits`;
ALTER TABLE `plugin_courses_specs` ADD COLUMN `exam_duration`     INT NULL AFTER `number_of_exams`;

-- Move "aims", "assessment methods" and "learning methodologies" from subject-level to spec-level - START
ALTER TABLE `plugin_courses_specs` ADD COLUMN `aims`              BLOB NULL DEFAULT NULL AFTER `summary`;
ALTER TABLE `plugin_courses_specs` ADD COLUMN `assessment_methods` BLOB NULL DEFAULT NULL AFTER `aims`;

ALTER TABLE `plugin_courses_subjects` DROP COLUMN `aims`;
ALTER TABLE `plugin_courses_subjects` DROP COLUMN `assessment_methods`;

CREATE TABLE `plugin_courses_specs_have_learning_methodologies` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order`                   INT          NULL,
  `spec_id`                 INT          NOT NULL,
  `learning_methodology_id` INT          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `spec_learning_methodology_UNIQUE` (`spec_id` ASC, `learning_methodology_id` ASC)
);

DROP TABLE `plugin_courses_subjects_have_learning_methodologies`;
-- Move "aims", "assessment methods" and "learning methodologies" from subject-level to spec-level - END


-- Add ability to set a URL, instead of a product, as recommended material and ability to control the order
ALTER TABLE `plugin_courses_specs_have_recommended_material`
ADD COLUMN `url`   VARCHAR(45) NULL AFTER `product_id`,
ADD COLUMN `order` VARCHAR(45) NULL AFTER `url`,
CHANGE COLUMN `product_id` `product_id` INT(11) NULL ,
DROP INDEX `spec_recommended_material_UNIQUE` ,
ADD INDEX `spec_recommended_material_UNIQUE` (`spec_id` ASC, `product_id` ASC, `url` ASC);
