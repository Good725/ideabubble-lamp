/*
ts:2020-01-28 15:00:00
*/

-- Ensure students have access to "my courses".
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Student'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'courses')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Student'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'courses_view_mycourses')
);

-- Remove the "homework" and "contacts3_frontend_bookings" permission from students. (Remove "Homework" and "Bookings" from the sidebar.)
DELETE FROM `engine_role_permissions`
WHERE `role_id`     = (SELECT IFNULL(`id`, '') FROM `engine_project_role` WHERE `role`  = 'Student'  LIMIT 1)
AND   `resource_id` = (SELECT IFNULL(`id`, '') FROM `engine_resources`    WHERE `alias` = 'homework' LIMIT 1)
;
DELETE FROM `engine_role_permissions`
WHERE `role_id`     = (SELECT IFNULL(`id`, '') FROM `engine_project_role` WHERE `role`  = 'Student'  LIMIT 1)
AND   `resource_id` = (SELECT IFNULL(`id`, '') FROM `engine_resources`    WHERE `alias` = 'contacts3_frontend_bookings' LIMIT 1)
;