/*
ts:2019-05-14 08:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'course_new_student_is_flexi',
  'New student is flexi',
  'courses',
  '0',
  '0',
  '0',
  '0',
  '0',
  'New Student is flexi',
  'toggle_button',
  'Courses',
  'Model_Settings,on_or_off'
);

ALTER TABLE plugin_courses_schedules ADD COLUMN display_timeslots_on_frontend TINYINT(1) NOT NULL DEFAULT 1;
