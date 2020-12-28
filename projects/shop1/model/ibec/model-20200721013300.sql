INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (1, 'show_notification_profile', 'User / Profile / Show notification settings in profile', 'Enable to show menu and see the page with notifications in profile',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user')) ON DUPLICATE KEY UPDATE `parent_controller` = (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'show_notification_profile')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'show_notification_profile')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Org Rep'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'show_notification_profile')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Teacher'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'show_notification_profile')
);