/*
ts:2019-09-17 07:51:00
*/

INSERT IGNORE INTO `engine_plugins`
  (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUES
  ('activecampaign', 'Active Campaign', '0', '0', NULL);

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('activecampaign', 'activecampaign_key', 'Active Campaign API Key', '', '', '',  '',  '',  'both', '', 'text', 'Active Campaign', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('activecampaign', 'activecampaign_url', 'Active Campaign API Url', '', '', '',  '',  '',  'both', '', 'text', 'Active Campaign', 0, '');

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('activecampaign_sync_on', 'Active Campaign Contact Sync On', 'activecampaign', '0', '0', '0', '0', '0', 'Active Campaign Contact Sync On', 'toggle_button', 'Active Campaign', 'Model_Settings,on_or_off');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES ('0', 'activecampaign', 'Active Campaign', 'Active Campaign');

INSERT INTO `engine_automations_actions_triggers` (`action`, `trigger`) VALUES ('Active Campaign Save Contact', 'Contact Save');
INSERT INTO `engine_automations_actions_triggers` (`action`, `trigger`) VALUES ('Active Campaign Delete Contact', 'Contact Delete');

