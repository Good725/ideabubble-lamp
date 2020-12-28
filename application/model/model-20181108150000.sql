/*
ts:2018-11-08 15:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('google_analytics_backend_tracking', 'Google Analytics backend tracking', '0', '0', '0', '0', '0', 'both', 'Allow backend (/admin) pages to be tracked by Google Analytics.', 'toggle_button', 'Google', 0, 'Model_Settings,on_or_off')
;
