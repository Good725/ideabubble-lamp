/*
ts:2017-10-26 09:57:00
*/

ALTER TABLE plugin_courses_courses ADD COLUMN display_availability ENUM('per_course', 'per_schedule') DEFAULT 'per_schedule';
