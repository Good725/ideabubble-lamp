/*
ts:2019-10-08 10:38:00
*/

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Teacher'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Teacher'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_request'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Teacher'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_my_stock'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_edit'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_request'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_approve'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_checkin'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_checkout'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_my_stock'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_all_stock'));