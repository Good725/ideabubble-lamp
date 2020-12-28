/*
ts:2020-07-14 12:59:01
*/

UPDATE engine_settings SET value_live = 1, value_stage = 1, value_test = 1, value_dev = 1 WHERE `variable` = 'bookings_billing_address_readonly';
