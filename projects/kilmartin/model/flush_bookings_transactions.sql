DELETE IGNORE FROM `plugin_bookings_transactions`;
DELETE IGNORE FROM `plugin_bookings_transactions_history`;
DELETE IGNORE FROM `plugin_bookings_transactions_payments`;
DELETE IGNORE FROM `plugin_bookings_transactions_payments_cheque`;
DELETE IGNORE FROM `plugin_bookings_transactions_payments_history`;

DELETE IGNORE FROM `plugin_ib_educate_booking_items`;
DELETE IGNORE FROM `plugin_ib_educate_bookings`;
DELETE IGNORE FROM `plugin_ib_educate_booking_schedule_has_label`;
DELETE IGNORE FROM `plugin_ib_educate_booking_has_schedules`;
DELETE IGNORE FROM `plugin_ib_educate_bookings_ignored_discounts`;
DELETE IGNORE FROM `plugin_ib_educate_bookings_discounts`;
DELETE IGNORE FROM `plugin_bookings_transactions_has_schedule`;
DELETE IGNORE FROM `plugin_ib_educate_booking_has_schedules`;

ALTER IGNORE TABLE `plugin_ib_educate_booking_items` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_ib_educate_booking_schedule_has_label` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_ib_educate_booking_has_schedules` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_bookings_transactions_history` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_bookings_transactions_payments_cheque` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_bookings_transactions_payments_history` AUTO_INCREMENT=1;

ALTER IGNORE TABLE `plugin_ib_educate_bookings` AUTO_INCREMENT=1000;
ALTER IGNORE TABLE `plugin_bookings_transactions` AUTO_INCREMENT=1000;
ALTER IGNORE TABLE `plugin_bookings_transactions_payments` AUTO_INCREMENT=1000;
ALTER IGNORE TABLE `plugin_ib_educate_bookings_ignored_discounts` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_ib_educate_bookings_discounts` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_bookings_transactions_has_schedule` AUTO_INCREMENT=1;
ALTER IGNORE TABLE `plugin_ib_educate_booking_has_schedules` AUTO_INCREMENT=1;

DELETE FROM `plugin_contacts3_notes`;
 ALTER TABLE `plugin_contacts3_notes`AUTO_INCREMENT = 1;