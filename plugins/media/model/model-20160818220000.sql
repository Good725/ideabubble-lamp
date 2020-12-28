/*
ts:2016-08-18 22:00:00
*/
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'media', 'Media', 'Media');
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'media')
);

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'media_limited', 'Media Limited', 'Media Limited');
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Basic'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'media_limited')
);
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'media_limited')
);

ALTER TABLE `plugin_media_shared_media` ADD COLUMN `owner_id` INT(11) NOT NULL;
UPDATE `plugin_media_shared_media` SET owner_id = created_by;