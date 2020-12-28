/*
ts:2018-07-04 09:55:00
*/

ALTER TABLE plugin_courses_schedules ADD COLUMN attend_all_default  ENUM('YES', 'NO') DEFAULT 'YES';
