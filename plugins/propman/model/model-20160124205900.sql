/*
ts:2016-01-24 20:59:00
*/

INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('propman_minstay_high', 'Min Stay HIGH', 'propman', '7', '7', '7', '7', '7', 'both', '', 'text', 'Properties', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('propman_arrival_high', 'Arrival HIGH', 'propman', 'Saturday', 'Saturday', 'Saturday', 'Saturday', 'Saturday', 'both', '', 'select', 'Properties', 0, 'Model_Propman,getArrivalOptions');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('propman_minstay_low', 'Min Stay LOW', 'propman', '2', '2', '2', '2', '2', 'both', '', 'text', 'Properties', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('propman_arrival_low', 'Arrival LOW', 'propman', 'Any', 'Any', 'Any', 'Any', 'Any', 'both', '', 'select', 'Properties', 0, 'Model_Propman,getArrivalOptions');
