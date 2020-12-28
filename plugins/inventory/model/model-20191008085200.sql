/*
ts:2019-10-08 08:52:00
*/

INSERT IGNORE INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'inventory_view_my_stock', 'Inventory view my stock', 'Inventory view my stock',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

INSERT IGNORE INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'inventory_view_all_stock', 'Inventory view all stock', 'Inventory view all stock',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

-- Manager items
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_edit'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_request'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_approve'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_checkin'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_checkout'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_my_stock'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Manager'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_all_stock'));

-- Supervisor items
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_edit'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_request'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_approve'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_checkin'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_checkout'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_my_stock'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'inventory_view_all_stock'));


