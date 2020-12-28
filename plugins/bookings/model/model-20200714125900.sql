/*
ts:2020-07-14 12:59:00
*/

ALTER TABLE plugin_ib_educate_bookings ADD COLUMN billing_address_id INT;

UPDATE plugin_ib_educate_bookings bookings
		INNER JOIN plugin_contacts3_contacts contacts ON bookings.contact_id = contacts.id
	SET bookings.billing_address_id = IFNULL(contacts.billing_residence_id, contacts.residence)
	WHERE bookings.billing_address_id IS NULL;

INSERT INTO engine_settings
  (`variable`, `name`, linked_plugin_name, value_live, value_stage, value_test, value_dev, `type`, `group`, options)
  VALUES
  ('bookings_billing_address_readonly', 'Checkout Billing Address Read Only', 'bookings', 0, 0, 0, 0, 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');
