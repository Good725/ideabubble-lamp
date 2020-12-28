/*
ts:2020-07-24 02:10:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (1, 'show_accounts_tab_bookings', 'Bookings / Show Accounts Data', 'Show Accounts Data, Balance Overall etc.',
       (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'bookings'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'show_accounts_tab_bookings')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'show_accounts_tab_bookings')
);