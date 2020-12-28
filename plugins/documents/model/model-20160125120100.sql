/*
ts:2016-01-25 12:01:00
*/

INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('print_office', 'Office Print', 'documents', '', '', '', '', '', '', '', 'toggle_button', 'Print', 0, 'Model_Settings,on_or_off');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('print_tray1_paper_type', 'Tray1 Paper Type', 'documents', 'PLAIN', 'PLAIN', 'PLAIN', 'PLAIN', 'PLAIN', '', '', 'select', 'Print', 0, 'Model_Document,getPaperTypeOptions');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('print_tray1_email', 'Tray1 Email', 'documents', '', '', '', '', '', 'both', '', 'text', 'Print', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('print_tray2_paper_type', 'Tray2 Paper Type', 'documents', 'PLAIN', 'PLAIN', 'PLAIN', 'PLAIN', 'PLAIN', '', '', 'select', 'Print', 0, 'Model_Document,getPaperTypeOptions');
INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('print_tray2_email', 'Tray2 Email', 'documents', '', '', '', '', '', 'both', '', 'text', 'Print', 0, '');
  INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('print_backup_email', 'Backup Email', 'documents', '', '', '', '', '', 'both', '', 'text', 'Print', 0, '');
