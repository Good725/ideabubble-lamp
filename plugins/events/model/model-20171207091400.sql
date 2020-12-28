/*
ts:2017-12-07 09:14:00
*/

ALTER TABLE plugin_events_orders ADD COLUMN email_id INT;
INSERT INTO `engine_cron_tasks` (`title`, `frequency`, `plugin_id`, `publish`, `action`) VALUES ('Event Order Email', '{\"minute\":[\"*\"],\"hour\":[\"*\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where `name` = 'events'), '0', 'cron_email_order_check');
