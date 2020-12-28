/*
ts:2020-08-26 13:27:01
*/

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `date_modified`, `publish`, `delete`, `report_type`) VALUES ('Trainer Timeslots', 'select rollcall.booking_id,\r\nconcat_ws(\' \', student.first_name, student.last_name) as student, schedules.`name` as `schedule`, courses.title as course, timeslots.datetime_start, \r\nif (timeslots.trainer_id is null, concat_ws(\' \', strainer.first_name, strainer.last_name), concat_ws(\' \', ttrainer.first_name, ttrainer.last_name)) as trainer,\r\nifnull (timeslots.trainer_id, schedules.trainer_id) as trainerid,\r\nif (tlocations.id is null, slocations.`name`, tlocations.name) as location\r\nfrom plugin_courses_schedules schedules\r\ninner join plugin_courses_schedules_events timeslots on schedules.id = timeslots.schedule_id\r\ninner join plugin_courses_courses courses  on schedules.course_id = courses.id\r\ninner join plugin_ib_educate_bookings_rollcall rollcall on timeslots.id = rollcall.timeslot_id\r\ninner join plugin_contacts3_contacts student on rollcall.delegate_id = student.id\r\nleft join plugin_contacts3_contacts strainer on schedules.trainer_id = strainer.id\r\nleft join plugin_contacts3_contacts ttrainer on timeslots.trainer_id = ttrainer.id\r\nleft join plugin_courses_locations slocations on schedules.location_id = slocations.id\r\nleft join plugin_courses_locations tlocations on timeslots.location_id = tlocations.id\r\nwhere rollcall.`delete` = 0 and timeslots.datetime_start >= \'{!From!}\' and timeslots.datetime_start < \'{!To!}\'\r\nhaving trainerid=\'{!Trainer!}\'\r\norder by datetime_start, student', '2020-08-26 14:13:22', '1', '0', 'sql');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Trainer Timeslots'), 'date', 'From');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Trainer Timeslots'), 'date', 'To');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Trainer Timeslots'), 'custom', 'Trainer', 'select distinct trainers.id, concat_ws(\' \', trainers.first_name, trainers.last_name) as trainer from plugin_courses_schedules schedules\r\ninner join plugin_contacts3_contacts trainers on schedules.trainer_id = trainers.id\r\norder by trainer');

UPDATE `plugin_reports_reports` SET `sql`='select rollcall.booking_id,\r\nconcat_ws(\' \', student.first_name, student.last_name) as student, schedules.`name` as `schedule`, courses.title as course, timeslots.datetime_start, \r\nif (timeslots.trainer_id is null, concat_ws(\' \', strainer.first_name, strainer.last_name), concat_ws(\' \', ttrainer.first_name, ttrainer.last_name)) as trainer,\r\nifnull (timeslots.trainer_id, schedules.trainer_id) as trainerid,\r\nif (tlocations.id is null, concat_ws(\' / \',slocations.`name`, slocationsp.`name`), concat_ws(\' / \', tlocations.name, tlocationsp.name)) as location\r\nfrom plugin_courses_schedules schedules\r\ninner join plugin_courses_schedules_events timeslots on schedules.id = timeslots.schedule_id\r\ninner join plugin_courses_courses courses  on schedules.course_id = courses.id\r\ninner join plugin_ib_educate_bookings_rollcall rollcall on timeslots.id = rollcall.timeslot_id\r\ninner join plugin_contacts3_contacts student on rollcall.delegate_id = student.id\r\nleft join plugin_contacts3_contacts strainer on schedules.trainer_id = strainer.id\r\nleft join plugin_contacts3_contacts ttrainer on timeslots.trainer_id = ttrainer.id\r\nleft join plugin_courses_locations slocations on schedules.location_id = slocations.id\r\nleft join plugin_courses_locations slocationsp on slocationsp.id = slocations.parent_id\r\nleft join plugin_courses_locations tlocations on timeslots.location_id = tlocations.id\r\nleft join plugin_courses_locations tlocationsp on tlocationsp.id = tlocations.parent_id\r\nwhere rollcall.`delete` = 0 and timeslots.datetime_start >= \'{!From!}\' and timeslots.datetime_start < \'{!To!}\'\r\nhaving trainerid=\'{!Trainer!}\'\r\norder by datetime_start, student' WHERE (name='Trainer Timeslots');