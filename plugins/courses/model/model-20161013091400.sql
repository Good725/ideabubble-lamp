/*
ts:2016-10-13 09:14:00
*/

ALTER TABLE plugin_courses_schedules ADD COLUMN payg_period ENUM('timeslot', 'week');
UPDATE plugin_courses_schedules SET payg_period = 'timeslot' WHERE payment_type = 2;
