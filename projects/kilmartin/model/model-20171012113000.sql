/*
ts:2017-10-12 11:30:00
*/

INSERT IGNORE INTO `engine_project_role` (`role`, `publish`, `deleted`) VALUES ('Accountant', '1', '0');

-- IGNORE, so there is no error, if they already have the permission
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Accountant'     ), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'contacts3_frontend_timesheets')),
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Accountant'     ), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_list'              )),
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Accountant'     ), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_view_approved'     ));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Parent/Guardian'), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'contacts3_frontend_timesheets'));
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Parent/Guardian'), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_list'              ));
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Parent/Guardian'), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_log_time'          ));
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Parent/Guardian'), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_view_approved'     ));
