/*
ts:2017-06-11 16:00:00
*/

ALTER TABLE plugin_ib_educate_bookings ADD COLUMN amendable TINYINT NOT NULL DEFAULT 0;
ALTER TABLE plugin_ib_educate_booking_has_schedules ADD COLUMN amendable TINYINT NOT NULL DEFAULT 0;
ALTER TABLE plugin_ib_educate_booking_items ADD COLUMN amendable TINYINT NOT NULL DEFAULT 0;
