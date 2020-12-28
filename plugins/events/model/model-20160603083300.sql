/*
ts:2016-06-03 08:33:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('events', 'events_fixed_charge_amount', 'Fixed Charge Amount', '0.65', '0.65', '0.65',  '0.65',  '0.65',  'both', '', 'text', 'Events', 0, '');

UPDATE `engine_settings` SET `value_live`='5', `value_stage`='5', `value_test`='5', `value_dev`='5', `default`='5' WHERE `variable`='events_commission_amount';
UPDATE `engine_settings` SET `value_live`='Percent', `value_stage`='Percent', `value_test`='Percent', `value_dev`='Percent', `default`='Percent' WHERE `variable`='events_commission_type';

ALTER TABLE `plugin_events_accounts` ADD COLUMN `fixed_charge_amount` DECIMAL(10, 2);
ALTER TABLE `plugin_events_orders` ADD COLUMN `fixed_charge_amount` DECIMAL(10, 2);
