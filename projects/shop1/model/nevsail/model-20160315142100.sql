/*
ts:2016-02-15 14:21:00
*/

-- Turn off the default products menu
UPDATE IGNORE `settings` SET `value_live`='0', `value_stage`='0', `value_test`='0', `value_dev`='0' WHERE `variable`='products_menu';

-- Increase the order number of the existing panels by 1, then put the new panel before all of them
UPDATE IGNORE `plugin_panels` SET `order_no` = `order_no` + 1;

-- Add the products menu panel
INSERT IGNORE INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Products Menu',
  'content_left',
  '0',
  (SELECT `id` FROM `plugin_panels_types` WHERE name = 'static' LIMIT 1),
  '0',
  '<div class=\"specials_offers\">\n<h1>Our Shop</h1>\n</div>\n\n<div class=\"products_menu\">{productsmenu-}</div>\n',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);
