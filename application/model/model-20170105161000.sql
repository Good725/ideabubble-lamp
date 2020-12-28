/*
ts:2017-01-05 16:10:00
*/
INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('frontend_login_link', 'Display a log-in button on the front end', '0', '0', '0', '0', '0', '', 'text', 'Website', 0, '');

UPDATE
  `engine_settings`
SET
  `type`    = 'toggle_button',
  `options` = 'Model_Settings,on_or_off'
WHERE
  `variable` = 'frontend_login_link'
;
