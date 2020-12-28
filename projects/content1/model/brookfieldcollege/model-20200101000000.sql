/*
ts:2020-01-01 00:00:00
*/

update plugin_messaging_notification_templates set message = replace(message, 'Kilmartin Educational Services 061-444989', 'Brookfield College') where message like '%kilmartin%';
