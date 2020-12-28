/*
ts:2017-02-23 12:37:00
*/

ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `created` DATETIME;
ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `created_by` INT;
ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `updated` DATETIME;
ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `updated_by` INT;
ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN `archived` TINYINT(1) NOT NULL DEFAULT 0;
