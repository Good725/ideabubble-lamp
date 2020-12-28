/*
ts:2019-04-19 11:21:00
*/

create table plugin_courses_schedules_timeslots_ignored_conflicts
(
  slot_1_id int not null,
  slot_2_id int not null,

  key (slot_1_id),
  key (slot_2_id),
  unique key (slot_1_id, slot_2_id)
)

ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE `plugin_courses_schedules_events` ADD INDEX (`datetime_start`), ADD INDEX (`datetime_end`);

