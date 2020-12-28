/*
ts:2020-07-21 12:00:00
*/

DELETE FROM `engine_settings` WHERE `variable` = '';

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `required`, `options`)
VALUES (
  'upcoming_course_feed_order',
  'Upcoming feed order',
  'courses',
  'next_timeslot',
  'next_timeslot',
  'next_timeslot',
  'next_timeslot',
  'next_timeslot',
  'The order of courses in the &quot;upcoming courses&quot; feed',
  'dropdown',
  'Courses',
  '0',
  '{"next_timeslot":"Next timeslot","start_date":"Start date"}'
);