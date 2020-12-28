/*
ts:2017-12-10 16:29:00
*/

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('products', 'product_shipping_price_mode', 'Shipping Price Mode', 'Stack', 'Stack', 'Stack', 'Stack', '0', 'both', '', 'select', 'Products', '0', 'Model_Checkout,shipping_modes');
