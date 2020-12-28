/*
ts: 2019-08-16 14:30:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('show_menu_on_landing_page', 'Show menu on landing pages', '1', '1', '1', '1', '1', 'both', 'Toggle the visibility of the header menu on pages with the &quot;landing page&quot; layout', 'toggle_button', 'Website', '0', 'Model_Settings,on_or_off');
