/*
ts:2020-08-05 09:12:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (
    `name`,
    `driver`,
    `type_id`,
    `subject`,
    `message`,
    `create_via_code`,
    `usable_parameters_in_template`,
    `linked_plugin_name`
  )
  VALUES
  (
    'course-waitlist-student',
    'EMAIL',
    '1',
    'You have been added on wait list for course $course',
    '<p>Hello, you have been added to wait list on course $course</p>\r\n<p>Schedule: $schedule</p>\r\n<p>We will notify you once we have free places on this course</p>\r\n<p>Warm regards',
    'Booking',
    '$course,$schedule,$name,$email',
    'bookings'
  );
