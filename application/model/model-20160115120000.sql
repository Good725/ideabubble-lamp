/*
ts:2016-01-15 12:00:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`, `note`, `type`, `group`, `options`, `linked_plugin_name`) VALUES
('course_website_display', 'Website display',5,5,5,5,5, 'Display options for the courses on the front end', 'select', 'Courses', 'Model_Settings,course_display_options', 'courses');

ALTER IGNORE TABLE `plugin_courses_schedules` DROP COLUMN `calendar_display` ;

SELECT id FROM engine_dalm_model WHERE `name` = 'application' AND `version` = '20160114114000' INTO @model;
DELETE FROM engine_dalm_statement WHERE model_id = @model;
DELETE FROM engine_dalm_model WHERE `name` = 'application' AND `version` = '20160114114000';
