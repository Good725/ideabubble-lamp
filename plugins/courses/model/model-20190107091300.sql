/*
ts:2019-01-07 09:13:00
*/

ALTER TABLE plugin_courses_schedules ADD COLUMN is_interview ENUM('YES', 'NO') NOT NULL DEFAULT 'NO';
ALTER TABLE plugin_courses_schedules_events ADD COLUMN max_capacity INT;
ALTER TABLE plugin_courses_schedules_events ADD COLUMN min_capacity INT;
ALTER TABLE plugin_ib_educate_bookings_has_applications ADD COLUMN interview_status ENUM('Scheduled', 'No Follow Up', 'Interviewed', 'Accepted', 'Rejected', 'Cancelled');
-- ALTER TABLE plugin_ib_educate_bookings_has_applications ADD COLUMN post MEDIUMTEXT;

CREATE TABLE plugin_courses_schedules_has_courses
(
  schedule_id INT,
  course_id INT,
  KEY (schedule_id),
  KEY (course_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('courses_schedule_interviews_enabled', 'Interview Schedules Enable', 'courses', '0', '0', '0', '0', '0', 'Interview Schedules Enable', 'toggle_button', 'Courses', 'Model_Settings,on_or_off');

INSERT INTO `plugin_messaging_notification_templates`
  (`driver`, `name`, `description`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`, `date_updated`)
  VALUES
  ('EMAIL', 'course-interview-schedule', 'Course Interview Schedule', 'Course Interview Date', '$name<br />\r\nYour Interview date for $code $course is on $date $time<br />\r\nSincerely\r\n', 'Bookings', '$name,$code,$course,$date,$time	', 'bookings', NOW());

ALTER TABLE `plugin_ib_educate_bookings_has_applications` MODIFY COLUMN `interview_status`  ENUM('Scheduled','No Follow Up','Interviewed','Accepted','Rejected','Cancelled','On Hold');

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `date_modified`, `publish`, `delete`, `report_type`) VALUES ('Interviews By Course', 'select \r\n		c.title as `Course`, count(*) as `Interview Count`\r\n	from plugin_ib_educate_bookings b \r\n		inner join plugin_ib_educate_bookings_has_courses hc on b.booking_id = hc.booking_id\r\n		inner join plugin_ib_educate_bookings_has_applications ha on b.booking_id = ha.booking_id and ha.interview_status is not null\r\n		inner join plugin_courses_courses c on hc.course_id = c.id\r\n	where b.`delete` = 0 and b.booking_status <> 3 and hc.booking_status <> 3 and hc.deleted = 0\r\n	group by c.title;', '2019-01-25 11:08:44', '1', '0', 'sql');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('course-timeslots-full', 'Course Timeslots Full', 'EMAIL', 'Course Timeslot Full', 'All timeslots for $course are full.\r\nPlease create new timeslots.', 'bookings', '$course', 'bookings');



