/*
ts:2020-04-07 17:00:00
*/

INSERT INTO `plugin_survey_answer_types` (`stub`, `title`, `publish`, `deleted`, `created_on`, `updated_on`)
VALUES ('yes_or_no', 'Yes/no toggle', 1, 0, NOW(), NOW());

ALTER TABLE `plugin_survey_questions`
CHANGE COLUMN `updated_on` `updated_on` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
ADD COLUMN `child_survey_id` INT(11) NULL AFTER `answer_id`;
