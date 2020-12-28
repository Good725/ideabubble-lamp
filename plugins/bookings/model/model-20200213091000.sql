/*
ts:2020-02-13 09:10:00
*/

INSERT INTO `engine_cron_tasks`
(`title`, `frequency`, `plugin_id`, `publish`, `action`)
VALUES
('Update booking status', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where `name` = 'bookings'), '0', 'cron_update_booking_status');
