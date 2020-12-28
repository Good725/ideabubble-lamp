/*
ts:2016-07-14 12:55:00
*/

ALTER TABLE `plugin_events_orders_payments` MODIFY COLUMN `status` ENUM('PROCESSING','PAID','VOID','CANCELLED','ERROR','REFUND');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'realex_refund_password', 'Realex Refund Password', '', '', '', '', '', 'both', 'Realex Refund Password', 'text', 'Realex Settings', '1', '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'realex_rebate_password', 'Realex Rebate Password', '', '', '', '', '', 'both', 'Realex Rebate Password', 'text', 'Realex Settings', '1', '');
