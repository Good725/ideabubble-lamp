/*
ts:2020-06-10 09:45:00
*/

INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('navapi', 'navapi_client_id', 'Client ID', '', '', '',  '',  '',  'both', '', 'text', 'NAVISION API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('navapi', 'navapi_client_secret', 'Client Secret', '', '', '',  '',  '',  'both', '', 'text', 'NAVISION API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('navapi', 'navapi_scope', 'Scope', '', '', '',  '',  '',  'both', '', 'text', 'NAVISION API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('navapi', 'navapi_ms_auth_url', 'Microsoft Authentication URL', '', '', '',  '',  '',  'both', '', 'text', 'NAVISION API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('navapi', 'navapi_api_url', 'NAVISION API URL', '', '', '',  '',  '',  'both', '', 'text', 'NAVISION API', 0, '');

ALTER TABLE `plugin_navapi_events` ADD INDEX (`remote_event_no`);

INSERT INTO `engine_cron_tasks` (`title`, `frequency`, `plugin_id`, `publish`, `action`) VALUES ('Navision send bookings/transactions/payments', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where name='navapi'), '0', 'cron_send_bookings');

ALTER TABLE plugin_navapi_events ADD created_by INT;
ALTER TABLE plugin_navapi_events ADD date_created DATETIME;
ALTER TABLE plugin_navapi_events ADD modified_by INT;
ALTER TABLE plugin_navapi_events ADD date_modified DATETIME;
ALTER TABLE plugin_navapi_events ADD deleted TINYINT NOT NULL DEFAULT 0;
ALTER TABLE plugin_navapi_events ADD published TINYINT NOT NULL DEFAULT 1;
