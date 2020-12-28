/*
ts:2019-03-05 10:34:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('timesheet-request-created', 'Timesheet Request Created', 'EMAIL', '0', 'Timesheet Request Created', 'A Timesheet for period<br />\r\n$period has been submitted<br />\r\n<br />\r\nName: $name<br />\r\nDepartment: $department<br />\r\nWorked: $duration<br />\r\nComment: $comment<br />', 'timesheets', '$period,$name,$department,$duration,$comment', 'timesheets');
