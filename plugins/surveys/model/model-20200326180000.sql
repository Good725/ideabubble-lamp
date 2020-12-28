/*
ts:2020-03-26 18:00:00
*/

ALTER TABLE `plugin_survey` ADD COLUMN `url_title` VARCHAR(45) NULL AFTER `title`;

-- Longer titles
ALTER TABLE `plugin_survey`
CHANGE COLUMN `title`     `title`     VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `url_title` `url_title` VARCHAR(255) NULL DEFAULT NULL ;

ALTER TABLE `plugin_survey_questions`      CHANGE COLUMN `title` `title` VARCHAR(255) NULL DEFAULT NULL ;
ALTER TABLE `plugin_survey_answers`        CHANGE COLUMN `title` `title` VARCHAR(255) NULL DEFAULT NULL ;
ALTER TABLE `plugin_survey_answer_options` CHANGE COLUMN `label` `label` VARCHAR(255) NULL DEFAULT NULL ;


-- Fields for adding course and schedule selectors
ALTER TABLE `plugin_survey` ADD COLUMN `has_course_selector`   INT(1) DEFAULT 0 NULL AFTER `thank_you_page_id`;
ALTER TABLE `plugin_survey` ADD COLUMN `has_schedule_selector` INT(1) DEFAULT 0 NULL AFTER `has_course_selector`;

ALTER TABLE `plugin_survey_result`
ADD COLUMN `course_id`   INT(11) NULL DEFAULT NULL AFTER `survey_author`,
ADD COLUMN `schedule_id` INT(11) NULL DEFAULT NULL AFTER `course_id`;

-- Fields for adding a stock selector to a survey/precheck
ALTER TABLE `plugin_survey` ADD COLUMN `has_stock_selector`  INT(1)       NULL DEFAULT 0    AFTER `has_schedule_selector`;
ALTER TABLE `plugin_survey` ADD COLUMN `stock_selector_text` VARCHAR(255) NULL DEFAULT NULL AFTER `has_stock_selector`;
ALTER TABLE `plugin_survey` ADD COLUMN `stock_category_id`   INT(11)      NULL DEFAULT NULL AFTER `stock_selector_text`;

ALTER TABLE `plugin_survey_result` ADD COLUMN `stock_id` INT(11) NULL DEFAULT NULL AFTER `survey_id`;

-- Corrective actions
ALTER TABLE `plugin_survey_answer_result` ADD COLUMN `todo_id` INT(1) NOT NULL DEFAULT 0 AFTER `textbox_value`;
ALTER TABLE `plugin_survey_answer_result` CHANGE COLUMN `todo_id` `todo_id` INT(1) NULL DEFAULT NULL ;

