/*
ts:2020-02-20 08:14:00
*/

ALTER TABLE `plugin_todos_todos2`
    ADD COLUMN `results_published_datetime` DATETIME NULL DEFAULT NULL AFTER `grading_type`;
