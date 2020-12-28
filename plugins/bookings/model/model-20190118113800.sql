/*
ts:2019-01-18 11:38:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('course-timeslot-changed', 'Course Timeslot Changed', 'EMAIL', '0', 'Course Date/Time changed', 'Hello $student,<br />\r\n<br />\r\nYour class  $schedule on $date $time by $trainer has been changed to $newschedule on $newdate - $newtime by $newtrainer.<br />\r\n<br />\r\nSincerely', '1', 'bookings', '$schedule,$date,$time,$trainer,$newschedule,$newdate,$newtime,$newtrainer', 'bookings');
