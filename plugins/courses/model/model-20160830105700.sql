/*
ts:2016-08-30 10:57:00
*/

INSERT INTO engine_notes_types
  (`type`, `referenced_table`, `referenced_table_id`, `referenced_table_deleted`)
  VALUES
  ('Roll Call', 'plugin_courses_rollcall', 'id', 'deleted');

INSERT INTO engine_notes_types
  (`type`, `referenced_table`, `referenced_table_id`, `referenced_table_deleted`)
  VALUES
  ('Course Booking Timeslot', 'plugin_courses_bookings_has_schedules_has_timeslots', 'id', 'deleted');

