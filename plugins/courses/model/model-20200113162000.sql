/*
ts:2020-01-13 16:20:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'show_start_date_for_repeating_timeslots',
  'Show start date for repeating timeslots',
  'courses',
  '0',
  '0',
  '0',
  '0',
  '0',
  'When enabled, a repeating timeslot will show its start date in the search results. When disabled, the repeating timeslot will show its weekday and time.',
  'toggle_button',
  'Courses',
  'Model_Settings,on_or_off'
);