/*
ts:2020-07-17 15:00:00
*/

ALTER TABLE `plugin_survey_questions` ADD COLUMN `required` INT(1) NOT NULL DEFAULT 1 AFTER `child_survey_id`;
