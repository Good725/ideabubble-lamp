/*
ts:2016-04-28 14:45:00
*/

INSERT IGNORE INTO `engine_settings`(`variable`, `name`, `note`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
(
  'shopping_thank_you_page',
  'Shopping Thank You Page',
  'Page the user is sent to after completing a purchase',
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us') LIMIT 1),
  'combobox',
  'Products',
  'Model_Pages,get_pages_as_options'
);
