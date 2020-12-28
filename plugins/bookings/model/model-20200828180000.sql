/*
ts:2020-08-28 18:00:00
*/

-- New permissions for actions on the booking editor
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (
  1,
  'bookings_discounts',
  'Bookings / Discounts',
  'Ability to manage booking discounts',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
), (
  1,
  'bookings_transfer',
  'Bookings / Transfer',
  'Ability to transfer a booking from one schedule to another',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
), (
  1,
  'bookings_add_schedule',
  'Bookings / Add schedule',
  'Ability to add a schedule to an existing booking',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
), (
  1,
  'bookings_view_additional',
  'Bookings / View additional',
  'Access the "Additional details" feature for bookings.',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
), (
  1,
  'bookings_book_and_pay',
  'Bookings / Book and pay',
  'Access the "book and pay" feature on a booking.',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
), (
  1,
  'bookings_book_and_bill',
  'Bookings / Book and bill',
  'Access the "book and bill" feature on a booking',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
);

-- Enable the permissions for administrators.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    `id`
  FROM
    `engine_resources`
  WHERE
    `alias` in ('bookings_transfer', 'bookings_add_schedule', 'bookings_view_additional', 'bookings_book_and_pay', 'bookings_book_and_bill')
;

-- Setting for toggling the purchase-order number field on/off
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'edit_booking_po_number',
  'Enable purchase-order number field',
  'bookings',
  '1',
  '1',
  '1',
  '1',
  '1',
  'both',
  'Display a purchase-order number field in the booking editor.',
  'toggle_button',
  '0',
  'Bookings',
  '0',
  'Model_Settings,on_or_off'
);

-- Permissions for the create-booking form
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (
  1,
  'bookings_create_for_delegates',
  'Bookings / Create for delegates',
  'Ability to create organisation bookings for multiple delegates via the backoffice',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
), (
  1,
  'bookings_create_for_multiple',
  'Bookings / Create for multiple',
  'Ability to create bookings for multiple students via the backoffice',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'bookings' LIMIT 1)
);

-- Enable the permissions for administrators.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    `id`
  FROM
    `engine_resources`
  WHERE
    `alias` IN ('bookings_create_for_delegates', 'bookings_create_for_multiple')
;