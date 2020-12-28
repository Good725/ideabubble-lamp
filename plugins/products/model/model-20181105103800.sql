/*
ts:2018-10-05 10:38:00
*/

CREATE TABLE plugin_products_discount_format_has_categories
(
  discountformat_id INT NOT NULL,
  category_id INT NOT NULL,

  KEY (discountformat_id),
  KEY (category_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('products', 'product_discount_category_enabled', 'Category Discounts', '0', '0', '0',  '0',  '0',  'both', '', 'toggle_button', 'Products', 0, 'Model_Settings,on_or_off');

