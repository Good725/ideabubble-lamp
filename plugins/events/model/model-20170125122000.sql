/*
ts:2017-01-25 12:20:00
*/

INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
VALUES ('events', 'events_checkout_countdown', 'Checkout Countdown', '00:05:00', '00:05:00', '00:05:00',  '00:05:00',  '00:05:00', 'The amount of time the user has to complete a purchase at the checkout. Use the format hh:mm:ss. e.g. 00:05:00 for five minutes.', 'text', 'Events');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES
(
  1,
  'events_countdown',
  'Events / Countdown',
  'Event countdown',
  (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'events')
);

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'events_countdown')
),
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'events_countdown')
);

-- The default value is controlled by the setting now. Removing the 300 default from the events table.
ALTER TABLE `plugin_events_events` CHANGE COLUMN `count_down_seconds` `count_down_seconds` INT(11) NULL  ;
UPDATE `plugin_events_events` SET `count_down_seconds`='' WHERE `count_down_seconds`='300';
