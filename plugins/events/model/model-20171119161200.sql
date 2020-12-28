/*
ts: 2017-11-19 16:12:00
*/

ALTER TABLE plugin_events_accounts ADD COLUMN qr_scan_mode ENUM('Confirmed', 'Fast') NOT NULL DEFAULT 'Confirmed';
