/*
ts:2019-10-14 11:55:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `subject`, `message`, `usable_parameters_in_template`) VALUES ('api-feedback', 'API Feedback', 'EMAIL', 'APP Feedback\r\n', 'Subject: $subject\r\nMessage: $message', '$subject,$message');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `subject`, `message`, `usable_parameters_in_template`) VALUES ('api-contact', 'API Contact', 'EMAIL', 'APP Contact\r\n', 'Name: $name\r\nEmail: $email\r\nSubject: $subject\r\nMessage: $message', '$name,$email,$subject,$message');
