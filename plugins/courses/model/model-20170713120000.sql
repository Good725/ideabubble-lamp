/*
ts:2017-07-13 12:00:00
*/

ALTER TABLE
  `plugin_courses_schedules_events`
ADD COLUMN
  `monitored` INT(1) NOT NULL DEFAULT 1
AFTER
  `datetime_end`
;

CREATE TABLE `plugin_courses_schedules_have_topics` (
  `schedule_id` INT(11) UNSIGNED NOT NULL ,
  `topic_id`    INT(11) UNSIGNED NOT NULL ,
  `deleted`     INT(1) NULL DEFAULT 0 ,
  `deleted_at`  TIMESTAMP NULL DEFAULT NULL ,
  PRIMARY KEY (`topic_id`, `schedule_id`, `deleted_at`)
);