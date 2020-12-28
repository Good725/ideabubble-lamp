/*
ts:2018-04-22 11:22:00
*/

ALTER TABLE plugin_events_events_dates ADD COLUMN is_onsale TINYINT NOT NULL DEFAULT 0;
UPDATE
  plugin_events_events_dates
    INNER JOIN plugin_events_events ON plugin_events_events_dates.event_id = plugin_events_events.id
  SET plugin_events_events_dates.is_onsale = plugin_events_events.is_onsale;
