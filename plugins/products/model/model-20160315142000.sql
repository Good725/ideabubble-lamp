/*
ts:2016-02-15 14:20:00
*/
INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
  'Products Menu',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  'productsmenu',
  'Model_Product,render_products_menu'
);
