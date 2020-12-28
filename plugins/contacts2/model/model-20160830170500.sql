/*
ts:2016-08-30 17:05:00
*/

SELECT  `id` INTO @nbs148_contacts_resource_id     FROM `engine_resources`    `o` WHERE `o`.`alias` = 'contacts2';
SELECT  `id` INTO @nbs148_admin_role_id            FROM `engine_project_role` `r` WHERE `r`.`role`  = 'Administrator';
SELECT  `id` INTO @nbs148_super_role_id            FROM `engine_project_role` `r` WHERE `r`.`role`  = 'Super User';
INSERT  IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller)    VALUES (1, 'contacts2_alert_menu', 'Contacts / Alert Menu', 'Ability to view contacts in the alert menu',  @nbs148_contacts_resource_id);
SELECT  `id` INTO @contacts_alert_menu_resource_id FROM `engine_resources` `o` WHERE `o`.`alias` =  'contacts2_alert_menu';
INSERT  IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES (@nbs148_admin_role_id, @contacts_alert_menu_resource_id);
INSERT  IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES (@nbs148_super_role_id, @contacts_alert_menu_resource_id);
