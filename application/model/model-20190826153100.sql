/*
ts:2019-08-26 15:31:00
*/

INSERT IGNORE INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`, `expose_to_api`)
  VALUES
  ('app_theme_color', 'App Theme Color', '#f8961d', '#f8961d', '#f8961d', '#f8961d', '', 'both', 'App Theme Color', 'color_picker', 'App Settings', '0', '', 1);
