/*
ts: 2019-11-12 18:00:00
*/

CREATE TABLE `plugin_todos_grading_schema` (
  `id`            INT        UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(100) NOT NULL,
  `published`     TINYINT(1)   NULL DEFAULT 1,
  `deleted`       TINYINT(1)   NULL DEFAULT 0,
  `date_created`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` TIMESTAMP    NULL,
  `created_by`    INT          NULL,
  `modified_by`   INT          NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `plugin_todos_grades` ADD COLUMN `schema_id` INT(11) NULL AFTER `grade`;
ALTER TABLE `plugin_todos_grades` DROP COLUMN `schema_id`;

CREATE TABLE `plugin_todos_schemas_have_grades` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `schema_id` INT          NOT NULL,
  `grade_id`  INT          NOT NULL,
  `order`     INT          NULL,
  PRIMARY KEY (`id`),
  INDEX `schema_grade_UNIQUE` (`schema_id` ASC, `grade_id` ASC)
);

ALTER TABLE `plugin_todos_grades` CHANGE COLUMN `grade` `grade` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE `plugin_todos_grading_schema` RENAME TO  `plugin_todos_grading_schemas` ;

