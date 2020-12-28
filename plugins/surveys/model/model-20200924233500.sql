/*
ts:2020-09-24 23:35:00
*/

ALTER TABLE `plugin_survey_questions`
    ADD COLUMN `max_score` INT NULL DEFAULT 0 AFTER `required`;
