/*
ts:2020-04-22 19:21:00
*/
INSERT INTO
    `plugin_messaging_notification_templates` (
    `name`,
    `driver`,
    `type_id`,
    `subject`,
    `message`,
    `create_via_code`,
    `usable_parameters_in_template`,
    `linked_plugin_name`)
VALUES (
           'course-waitlist-admin',
           'EMAIL',
           '1',
           'New waitilist record',
           'A new record on waitlist has beed created on the course<br /> : $course <br /> for $name ($email)\r\n',
           'Booking',
           '$course,$name,$email',
           'bookings');
