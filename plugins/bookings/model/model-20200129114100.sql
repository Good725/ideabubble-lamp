/*
ts:2020-01-29 11:41:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('course-subscription-confirm-completed', 'Course Subscription Confirm completed', 'EMAIL', 'Subscription Completed', '<p>\r\nHello,<br />\r\n$student subcription to $schedule has been completed.<br />\r\nThanks\r\n</p>', 'bookings', '$student,$schedule', 'bookings');
