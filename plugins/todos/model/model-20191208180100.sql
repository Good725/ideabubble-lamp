/*
ts:2019-12-06 18:01:00
*/
CREATE TABLE `plugin_todos_grading_schema_points` (
  `id`         INT         NOT NULL,
  `schema_id`  INT(11)     NULL,
  `grade_id`   VARCHAR(45) NULL,
  `level_id`   INT(11)     NULL,
  `subject_id` INT(11)     NULL,
  `points`     INT(5)      NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `schema_grade_level_subject` (`schema_id` ASC, `level_id` ASC, `grade_id` ASC, `subject_id` ASC));


ALTER TABLE `plugin_todos_grading_schema_points`
ADD COLUMN `order` INT(5) NULL AFTER `points`,
CHANGE COLUMN `schema_id` `schema_id` INT(11)     NOT NULL ,
CHANGE COLUMN `grade_id`  `grade_id`  VARCHAR(45) NOT NULL ,
CHANGE COLUMN `level_id`  `level_id`  INT(11)     NOT NULL ;

ALTER TABLE `plugin_todos_grading_schema_points`
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `plugin_todos_todos2` ADD COLUMN `grading_schema_id` INT(11) NULL  AFTER `course_id`;

ALTER TABLE `plugin_todos_todos2_has_results`
ADD COLUMN `level_id`  INT(11) NULL AFTER `student_id`,
ADD COLUMN `subject_id` INT(11) NULL AFTER `student_id`;
