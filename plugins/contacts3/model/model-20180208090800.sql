/*
ts:2018-02-08 09:08:00
*/

DELETE FROM plugin_news_categories WHERE `category` IN ('Offers', 'Quotes');

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('courses', 'countdown_title', 'Count Down Title', '', '', '', '', '', 'both', 'Count Down Title', 'text', 'Courses', '0', '');

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('courses', 'countdown_datetime', 'Count Down Date Time', '', '', '', '', '', 'both', 'Count Down Date Time (YYYY-MM-DD HH:II:SS)', 'text', 'Courses', '0', '');
