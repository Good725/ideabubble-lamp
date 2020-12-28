/*
ts:2016-02-11 14:40:00
*/

CREATE TABLE plugin_courses_schedules_has_engine_calendar_events
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  schedule_id INT NOT NULL,
  engine_calendar_event_id INT NOT NULL,

  KEY (schedule_id)
)
ENGINE = InnoDB
CHARSET = UTF8;
