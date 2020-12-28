/*
ts:2020-10-07 15:11:00
*/

INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES
(
  1,
  'booking_add_discount',
  'Bookings add discount',
  'Ability to apply discounts to a booking',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
);

INSERT IGNORE INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  (SELECT r.id, o.id FROM `engine_project_role` r, engine_resources o WHERE r.`role` = 'Administrator' and o.alias = 'booking_add_discount');
