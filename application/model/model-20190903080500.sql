/*
ts: 2019-09-03 08:05:00
*/

INSERT IGNORE INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`, `expose_to_api`)
  VALUES
  ('engine_db_transfer_allow_ips', 'Engine DB Transfer Allowed IPS', '', '', '', '', '', 'both', 'Engine DB Transfer Allowed IPS', 'text', 'Engine', '0', '', 0);

INSERT IGNORE INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`, `expose_to_api`)
  VALUES
  ('engine_db_transfer_from_url', 'Engine DB Transfer From URL', '', '', '', '', '', 'both', 'Engine DB Transfer From URL', 'text', 'Engine', '0', '', 0);
