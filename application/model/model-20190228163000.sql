/*
ts:2019-02-28 16:30:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'course_rescheduler_enabled',
  'Rescheduler enabled',
  'courses',
  '0',
  '0',
  '0',
  '0',
  '0',
  'Include a section on the <a href=&quot;/admin/courses/schedules&quot;>schedules</a> form that allows users to update existing timeslots to suit a new schedule.',
  'toggle_button',
  'Courses',
  'Model_Settings,on_or_off'
);