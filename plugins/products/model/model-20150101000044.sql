/*
ts:2015-01-01 00:00:44
*/

-- Set a URL name for all products that do not have one
UPDATE IGNORE `plugin_products_product` SET `url_title` = format_url_name(`title`) WHERE `url_title` IS NULL;

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
 VALUES ('purchase_disabled_show_prices', 'Show prices when purchases are disabled', '0', '0', '0', '0', '0', 'Show the price on products when the purchase option has been disabled', 'toggle_button', 'Products', 'Model_Settings,on_or_off');

ALTER IGNORE TABLE `plugin_products_matrices` ADD COLUMN `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`) VALUES
('shipping_information_string', '\"Shipping Information\" text', 'Shipping Information', 'Shipping Information', 'Shipping Information', 'Shipping Information', 'Shipping Information', 'both', 'Text to be displayed on the checkout, above to the shipping address',            'text', 'Shop Checkout'),
('postal_destination_string',   '\"Postal Destination\" text',   'Postal Destination',   'Postal Destination',   'Postal Destination',   'Postal Destination',   'Postal Destination',   'both', 'Text to be displayed on the checkout, next to the postal destinations dropdown', 'text', 'Shop Checkout');

ALTER IGNORE TABLE `plugin_products_option` ADD COLUMN `default` INT(1) NULL DEFAULT 0 AFTER `price` ;
ALTER IGNORE TABLE `plugin_products_carts` ADD COLUMN `user_id` INT(11) NULL DEFAULT NULL  AFTER `ip_address` ;

INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES (
  'products_plugin_page',
  'Default Products Page',
  'both',
  'Select the page that will be used to load products information',
  'select',
  'Products',
  '0',
  'Model_Pages,get_pages_as_options'
);

UPDATE `settings` JOIN `ppages`
SET
  `settings`.`value_live`  = `ppages`.`id`,
  `settings`.`value_stage` = `ppages`.`id`,
  `settings`.`value_test`  = `ppages`.`id`,
  `settings`.`value_dev`   = `ppages`.`id`,
  `settings`.`default`     = `ppages`.`id`
WHERE `ppages`.`name_tag`  = 'products.html' AND `settings`.`variable` = 'products_plugin_page';

INSERT IGNORE INTO ppages
(name_tag, title, content, banner_photo, seo_keywords, seo_description, footer, date_entered, last_modified, created_by, modified_by, publish, deleted, include_sitemap, layout_id, category_id)
VALUES
('subscription-thank-you.html', 'subscription-thank-you', '<h1>Thank you for subscribing</h1>\n\n<p>We will be in touch with our news letter over the coming year.</p>\n', null,  '',  '',  '',  '2013-12-19 09:53:40',  '2013-12-19 09:54:09',  '2',  '2', '1', '0', '1', '1', '1'),
('products.html', 'products', '', '', '', '', '', '2014-09-03 12:34:24', '2014-09-03 12:46:57', '2', '2', '1', '0', '1', '1', '1'),
('checkout.html', 'checkout', '<h1>YOUR CART</h1>\n', '', '', '', '', '2014-09-17 13:06:00', '2014-12-22 11:02:20', '2', '2', '1', '0', '1', '1', '1'),
('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us', '<p><strong>Thanks for shopping with us</strong></p>\n', '', '', '', '', '2015-04-10 09:54:25', '2015-04-10 09:55:21', '2', '2', '1', '0', '1', '1', '1');

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('products_infinite_scroller', 'Infinite Scrolling', '0', '0', '0', '0', '0', 'both', 'Enable infinite scrolling on the products feed', 'toggle_button', 'Products', '', 'Model_Settings,on_or_off');

CREATE TABLE IF NOT EXISTS `plugin_products_product_documents` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT ,
  `product_id`    INT(11)      NOT NULL ,
  `document_name` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) );

ALTER IGNORE TABLE `plugin_products_category` ADD COLUMN `theme` VARCHAR(100) NULL AFTER `parent_id` ;

INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES (
  'default_size_guide',
  'Size Guide',
  'both',
  'Select the page that will be used as the default size guide on product pages.',
  'select',
  'Products',
  '0',
  'Model_Pages,get_pages_as_options'
);

-- MHI-14
SELECT CAST(`value_test` AS SIGNED) INTO @test_fix_products_plugin_page_id FROM `settings` WHERE `variable` = 'products_plugin_page';
SELECT COUNT(id) INTO @test_fix_products_plugin_page_id_exists FROM ppages WHERE id=@test_fix_products_plugin_page_id;
SELECT id INTO @test_fix_products_plugin_page_id_to_set FROM ppages WHERE name_tag='products.html' ORDER BY id ASC LIMIT 1;
UPDATE `settings`
SET `value_test` = @test_fix_products_plugin_page_id_to_set
WHERE `variable` = 'products_plugin_page' AND
      @test_fix_products_plugin_page_id IS NOT NULL AND
      @test_fix_products_plugin_page_id_exists=0;
SELECT CAST(`value_stage` AS SIGNED) INTO @stage_fix_products_plugin_page_id FROM `settings` WHERE `variable` = 'products_plugin_page';
SELECT COUNT(id) INTO @stage_fix_products_plugin_page_id_exists FROM ppages WHERE id=@stage_fix_products_plugin_page_id;
SELECT id INTO @stage_fix_products_plugin_page_id_to_set FROM ppages WHERE name_tag='products.html' ORDER BY id ASC LIMIT 1;
UPDATE `settings`
SET `value_stage` = @stage_fix_products_plugin_page_id_to_set
WHERE `variable` = 'products_plugin_page' AND
      @stage_fix_products_plugin_page_id IS NOT NULL AND
      @stage_fix_products_plugin_page_id_exists=0;
SELECT CAST(`value_live` AS SIGNED) INTO @live_fix_products_plugin_page_id FROM `settings` WHERE `variable` = 'products_plugin_page';
SELECT COUNT(id) INTO @live_fix_products_plugin_page_id_exists FROM ppages WHERE id=@live_fix_products_plugin_page_id;
SELECT id INTO @live_fix_products_plugin_page_id_to_set FROM ppages WHERE name_tag='products.html' ORDER BY id ASC LIMIT 1;
UPDATE `settings`
SET `value_live` = @live_fix_products_plugin_page_id_to_set
WHERE `variable` = 'products_plugin_page' AND
      @live_fix_products_plugin_page_id IS NOT NULL AND
      @live_fix_products_plugin_page_id_exists=0;

UPDATE IGNORE `settings`
  SET `name` = 'Product Enquiry', `type`='toggle_button', `options`='Model_Settings,on_or_off', `default`='0', `note`='This will replace the &quot;add to cart&quot; button with an &quot;enquire now&quot; button'
  WHERE `variable` = 'product_enquiry';
UPDATE IGNORE `settings` SET `value_dev`  ='1' WHERE `variable` = 'product_enquiry' AND value_dev   = 'TRUE';
UPDATE IGNORE `settings` SET `value_dev`  ='0' WHERE `variable` = 'product_enquiry' AND value_dev   = 'FALSE';
UPDATE IGNORE `settings` SET `value_test` ='1' WHERE `variable` = 'product_enquiry' AND value_test  = 'TRUE';
UPDATE IGNORE `settings` SET `value_test` ='0' WHERE `variable` = 'product_enquiry' AND value_test  = 'FALSE';
UPDATE IGNORE `settings` SET `value_stage`='1' WHERE `variable` = 'product_enquiry' AND value_stage = 'TRUE';
UPDATE IGNORE `settings` SET `value_stage`='0' WHERE `variable` = 'product_enquiry' AND value_stage = 'FALSE';
UPDATE IGNORE `settings` SET `value_live` ='1' WHERE `variable` = 'product_enquiry' AND value_live  = 'TRUE';
UPDATE IGNORE `settings` SET `value_live` ='0' WHERE `variable` = 'product_enquiry' AND value_live  = 'FALSE';

-- LB-41
INSERT IGNORE INTO engine_localisation_custom_scanners (`scanner`) VALUES ('Model_Category::get_localisation_messages');
INSERT IGNORE INTO engine_localisation_custom_scanners (`scanner`) VALUES ('Model_Product::get_localisation_messages');

INSERT IGNORE INTO `activities_item_types` (`stub`, `name`, `table_name`) values ('product', 'Product', 'plugin_products_product');

ALTER IGNORE TABLE `plugin_products_product` CHANGE COLUMN `brief_description` `brief_description` BLOB NOT NULL;
