/*
ts:2020-03-03 12:00:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `svg`)
VALUES ('attendance', 'Attendance', '1', 'exams');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES
('0', 'attendance', 'Attendance', 'Access to the attendance plugin');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES (
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'attendance')
);