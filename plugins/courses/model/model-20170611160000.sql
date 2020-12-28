/*
ts:2017-06-11 16:00:00
*/

ALTER TABLE plugin_courses_schedules ADD COLUMN amendable TINYINT NOT NULL DEFAULT 0;
INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
  VALUES
  ('course_amend_fee_percent', 'Amend Fee Percent', 'courses', '10', '10', '10', '10', '10', 'Amendable Fee', 'text', 'Courses');
