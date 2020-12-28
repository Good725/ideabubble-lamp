/*
ts:2018-10-25 01:01:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
  VALUES
  ('events', 'checkout_max_queue_count', 'Checkout Max Queue Count', '6', '6', '6', '6', '6', 'Checkout Max Queue Count', 'text', 'Events');
