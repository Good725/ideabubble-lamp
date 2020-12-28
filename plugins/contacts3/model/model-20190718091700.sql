/*
ts:2019-07-18 09:17:00
*/

ALTER TABLE `plugin_contacts3_contacts`
    ADD COLUMN `hourly_rate` FLOAT(5, 2) NULL DEFAULT NULL AFTER `occupation`;

INSERT INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'contacts3_billing', 'KES Contacts / View & Edit Billing Section', 'KES Contacts / View & Edit Billing Section',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'contacts3_billing'));