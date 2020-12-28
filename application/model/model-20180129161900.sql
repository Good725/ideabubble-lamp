/*
ts:2018-01-29 16:19:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)

  VALUES

  ('dalm_run_once', 'Run DALM Once After Release', '1', '1', '1', '1', '0', 'both', 'Dalm will run on all requests when disabled', 'toggle_button', 'Engine', '0', 'Model_Settings,on_or_off');
