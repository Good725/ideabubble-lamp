/*
ts:2018-09-27 08:23:00
*/

ALTER TABLE plugin_ib_educate_booking_items ADD COLUMN status_updated DATETIME;
ALTER TABLE plugin_ib_educate_booking_items ADD COLUMN temporary_absences TEXT;
ALTER TABLE plugin_ib_educate_booking_items DROP COLUMN absence_left;
ALTER TABLE plugin_ib_educate_booking_items DROP COLUMN absence_returned;
