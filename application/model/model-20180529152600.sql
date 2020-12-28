/*
ts:2018-05-29 15:26:00
*/

UPDATE plugin_messaging_notification_templates SET message=concat(message, '<p>HOST: $host</p>') WHERE name='newsletter-signup';
UPDATE plugin_messaging_notification_templates SET message=concat(message, '<p>REFERER: $referer</p>') WHERE name='newsletter-signup';
