/*
ts:2019-12-12 17:00:01
*/


UPDATE plugin_messaging_notification_templates SET `publish` = 1 WHERE `name` = 'booking-schedule-start-reminder' /*1*/;
UPDATE plugin_messaging_notification_templates SET message = REPLACE(message, 'Kilmartin Educational Services', 'Brookfield College');
UPDATE plugin_messaging_notification_templates SET message = REPLACE(message, '061-444989', '066-7145896');
