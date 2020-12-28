/*
ts:2016-10-05 11:25:00
*/

ALTER TABLE plugin_events_events ADD COLUMN currency VARCHAR(3) NOT NULL DEFAULT 'EUR';
ALTER TABLE plugin_events_events ADD COLUMN country VARCHAR(3) NOT NULL DEFAULT 'IE';
ALTER TABLE plugin_events_events DROP COLUMN country;

