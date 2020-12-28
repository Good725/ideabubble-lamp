/*
ts:2020-02-20 11:00:00
*/

-- Add types to questionnaires
ALTER TABLE `plugin_survey` ADD COLUMN `type` ENUM('Exam', 'Pre-check', 'Quiz', 'Survey') NULL AFTER `title`;

-- Add scores to answer options
ALTER TABLE `plugin_survey_answer_options` ADD COLUMN `score` INT(11) NULL DEFAULT NULL AFTER `value`;

-- Rename answer types
UPDATE `plugin_survey_answer_types` SET `title` = 'Radio group'    WHERE `stub` = 'radio'; -- was "Radio Button"
UPDATE `plugin_survey_answer_types` SET `title` = 'Textarea'       WHERE `stub` = 'textarea'; -- was "Text Area"
UPDATE `plugin_survey_answer_types` SET `title` = 'Dropdown'       WHERE `stub` = 'select'; -- was "Selection Box"
UPDATE `plugin_survey_answer_types` SET `title` = 'Checkbox group' WHERE `stub` = 'checkbox'; -- was "Check Box"


UPDATE `plugin_survey_answer_types` SET `title` = 'Text box' WHERE `stub` = 'input'; -- was "Input"
