/*
ts:2016-07-13 14:50:00
*/

ALTER IGNORE TABLE `plugin_events_events`
ADD COLUMN `featured` INT(1) NOT NULL DEFAULT 0 AFTER `quantity` ;

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES
(
  1,
  'events_feature',
  'Events / Feature',
  'Feature events',
  (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'events')
);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'events_feature')
),
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'events_feature')
);
