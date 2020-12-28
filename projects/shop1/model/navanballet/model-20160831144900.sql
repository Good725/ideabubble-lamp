/*
ts:2016-08-31 14:49:00
*/

UPDATE plugin_messaging_notification_templates SET `deleted` = 1, `publish` = 0 WHERE `name` IN ('new_booking_admin', 'new_booking_customer', 'booking-balance-payment-admin', 'booking-balance-payment-customer', 'outstanding-bookings-8w-reminder');
