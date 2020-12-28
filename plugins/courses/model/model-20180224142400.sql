/*
ts:2018-02-24 14:24:00
*/

ALTER IGNORE TABLE plugin_courses_courses ADD FULLTEXT KEY (title);
ALTER IGNORE TABLE plugin_courses_schedules ADD FULLTEXT KEY (name);
