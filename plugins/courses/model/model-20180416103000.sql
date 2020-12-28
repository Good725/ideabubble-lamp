/*
ts:2018-04-16 10:30:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
  ('courses_in_calendar', 'Courses in Calendar', 'courses', '0', '0', '0', '0', '0', 'Display courses in the calendar that appears on the site front end', 'toggle_button', 'Courses', 'Model_Settings,on_or_off')
;

ALTER TABLE
  `plugin_courses_categories`
ADD COLUMN
  `display_in_calendar` INT(1) NOT NULL DEFAULT 1 AFTER `end_time`
;
