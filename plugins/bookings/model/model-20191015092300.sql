/*
ts:2019-10-15 09:23:00
*/

CREATE TABLE plugin_ib_educate_bookings_has_delegates
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  contact_id INT NOT NULL,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (booking_id),
  KEY (contact_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('bookings_enable_credit_booking', 'Enable Credit Booking', 'bookings', '0', '0', '0', '0', '0', 'Enable Credit Booking', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off', 1);
