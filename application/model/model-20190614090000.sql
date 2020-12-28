/*
ts:2019-06-14 09:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('link_contacts_to_bookings', 'Link Contacts to Bookings', '0', '0', '0', '0', '0', 'Allows you to Link Contacts to a Booking', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');

CREATE TABLE `plugin_ib_educate_bookings_has_linked_contacts`
(
    `booking_id` INT(11) NOT NULL,
    `contact_id` INT(11) NOT NULL,
    PRIMARY KEY (`booking_id`, `contact_id`)
);

