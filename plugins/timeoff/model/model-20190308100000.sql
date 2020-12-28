/*
ts: 2019-03-08 10:00:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `subject`, `message`) VALUES ('timeoff-request-created', 'Timeoff Request Created', 'EMAIL', 'Timeoff Request Created', 'A new time off request has been submitted.<br />\r\n<br />\r\nName: $name<br />\r\nDepartment: $department<br />\r\nType: $type<br />\r\nPeriod: $period<br />\r\nDate: $date<br />\r\nDuration: $duration<br />\r\nNotes: $note\r\n\r\n');
