/*
ts:2018-04-29 18:35:01
*/

CREATE TABLE plugin_ib_educate_bookings_has_applications
(
  booking_id  INT PRIMARY KEY,
  status_id INT,
  data MEDIUMTEXT -- json stored
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_ib_educate_bookings_has_courses
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  booking_id  INT NOT NULL,
  course_id INT NOT NULL,
  paymentoption_id INT,
  deleted TINYINT NOT NULL DEFAULT 0,
  booking_status  INT,

  KEY (booking_id),
  KEY (course_id)
)
ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE plugin_ib_educate_booking_has_schedules ADD COLUMN paymentoption_id INT;

INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('fulltime-course-application-admin', 'EMAIL', '1', 'New fulltime course application', 'A new fulltime course application has been created. <br />\r\nGuardian: $guardian <br />\r\nStudent: $student <br />\r\nCourse: $course <br />\r\n', 'Booking', '$guardian,$student,$course', 'bookings');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('fulltime-course-application-customer', 'EMAIL', '1', 'Course application has been received', 'Hello, <br />\r\nWe have received your course application: <br />\r\nGuardian: $guardian <br />\r\nStudent: $student <br />\r\nCourse: $course <br />', 'Booking', '$guardian,$student,$course', 'bookings');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('fulltime-course-application-approved-customer', 'EMAIL', '1', 'Course application has been approved', 'Hello,\r\nYour course application has been approved.\r\nGuardian: $guardian <br />\r\nStudent: $student <br />\r\nCourse: $course <br />\r\n<br />\r\nClick <a href=\"$link\">link</a> to pay <br />\r\nThanks', 'Booking', '$guardian,$student,$course,$link', 'bookings');
