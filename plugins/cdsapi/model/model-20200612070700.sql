/*
ts: 2020-06-12 07:07:00
*/

INSERT INTO `engine_plugins`
  (`name`, `friendly_name`, `icon`, `flaticon`, `svg`, `show_on_dashboard`)
  VALUES
  ('cdsapi', 'CDS API', 'contacts', 'business-card', 'contacts', 0);

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`)
  VALUES
  ('0', 'cdsapi', 'CDS API', 'CDS API');

INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('cdsapi', 'cdsapi_client_id', 'Client ID', '', '', '',  '',  '',  'both', '', 'text', 'CDS API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('cdsapi', 'cdsapi_client_secret', 'Client Secret', '', '', '',  '',  '',  'both', '', 'text', 'CDS API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('cdsapi', 'cdsapi_scope', 'Scope', '', '', '',  '',  '',  'both', '', 'text', 'CDS API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('cdsapi', 'cdsapi_ms_auth_url', 'Microsoft Authentication URL', '', '', '',  '',  '',  'both', '', 'text', 'CDS API', 0, '');
INSERT INTO `engine_settings` (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('cdsapi', 'cdsapi_api_url', 'CDS API URL', '', '', '',  '',  '',  'both', '', 'text', 'CDS API', 0, '');
