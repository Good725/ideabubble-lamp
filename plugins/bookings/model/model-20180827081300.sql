/*
ts:2018-08-27 08:23:00
*/

ALTER TABLE plugin_bookings_transactions_payment_plans ADD COLUMN term_type ENUM('month', 'custom') NOT NULL DEFAULT 'month';
