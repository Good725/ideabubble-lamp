/*
ts:2017-12-19 13:13:00
*/

ALTER TABLE plugin_events_checkout_details ADD COLUMN firstname VARCHAR(25);
ALTER TABLE plugin_events_checkout_details ADD COLUMN lastname VARCHAR(25);

UPDATE plugin_events_checkout_details SET firstname = ccName;

UPDATE plugin_events_checkout_details SET firstname = SUBSTR(ccName, 1, LENGTH(SUBSTRING_INDEX(ccName, ' ', 1))), lastname = SUBSTR(ccName, LENGTH(SUBSTRING_INDEX(ccName, ' ', 1)) + 2);

