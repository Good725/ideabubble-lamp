/*
ts:2015-01-01 00:00:40
*/

-- -----------------------------------------------------
-- Table `plugin_products_category`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_category` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `information` BLOB NOT NULL ,
  `image` VARCHAR(255) NOT NULL ,
  `order` SMALLINT NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `parent_id` INT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ,
  `date_entered` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_postage_format`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_postage_format` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_product`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_product` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `category_id` INT NULL ,
  `price` DECIMAL(10,2) NOT NULL ,
  `display_price` TINYINT NOT NULL ,
  `offer_price` DECIMAL(10,2) NOT NULL ,
  `display_offer` TINYINT NOT NULL ,
  `featured` TINYINT NOT NULL ,
  `brief_description` VARCHAR(255) NOT NULL ,
  `description` BLOB NOT NULL ,
  `product_code` VARCHAR(255) NOT NULL ,
  `ref_code` VARCHAR(255) NOT NULL ,
  `weight` INT NOT NULL ,
  `postal_format_id` INT NULL ,
  `out_of_stock` TINYINT NOT NULL ,
  `out_of_stock_msg` VARCHAR(255) NOT NULL ,
  `size_guide` VARCHAR(255) NOT NULL ,
  `document` VARCHAR(255) NOT NULL ,
  `order` SMALLINT NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `product_idx_1` (`category_id` ASC) ,
  INDEX `product_idx_2` (`postal_format_id` ASC) ,
  CONSTRAINT `product_fk_1`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_products_category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `product_fk_2`
  FOREIGN KEY (`postal_format_id` )
  REFERENCES `plugin_products_postage_format` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_product_images`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_product_images` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `product_id` INT NOT NULL ,
  `file_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `product_images_idx_1` (`product_id` ASC) ,
  CONSTRAINT `product_images_fk_1`
  FOREIGN KEY (`product_id` )
  REFERENCES `plugin_products_product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_postage_zone`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_postage_zone` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_postage_rate`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_postage_rate` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `format_id` INT NULL ,
  `zone_id` INT NULL ,
  `weight_from` DECIMAL(10,2) NOT NULL ,
  `weight_to` DECIMAL(10,2) NOT NULL ,
  `price` DECIMAL(10,2) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `postage_rate_idx_1` (`format_id` ASC) ,
  INDEX `postage_rate_idx_2` (`zone_id` ASC) ,
  CONSTRAINT `postage_rate_fk_1`
  FOREIGN KEY (`format_id` )
  REFERENCES `plugin_products_postage_format` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `postage_rate_fk_2`
  FOREIGN KEY (`zone_id` )
  REFERENCES `plugin_products_postage_zone` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_option`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_option` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `label` VARCHAR(255) NOT NULL ,
  `group` VARCHAR(255) NOT NULL ,
  `value` INT NOT NULL ,
  `image` VARCHAR(255) NOT NULL ,
  `price` DECIMAL(10,2) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `option_idx_1` (`group` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_product_options`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_product_options` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `product_id` INT NOT NULL ,
  `option_group` VARCHAR(255) NOT NULL ,
  `required` TINYINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `product_options_idx_1` (`product_id` ASC) ,
  INDEX `product_options_idx_2` (`option_group` ASC) ,
  CONSTRAINT `product_options_fk_1`
  FOREIGN KEY (`product_id` )
  REFERENCES `plugin_products_product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `product_options_fk_2`
  FOREIGN KEY (`option_group` )
  REFERENCES `plugin_products_option` (`group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_product_related_to`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_product_related_to` (
  `product_id_1` INT NOT NULL ,
  `product_id_2` INT NOT NULL ,
  PRIMARY KEY (`product_id_1`, `product_id_2`) ,
  INDEX `product_related_to_idx_1` (`product_id_2` ASC) ,
  INDEX `product_related_to_idx_2` (`product_id_1` ASC) ,
  CONSTRAINT `product_related_to_fk_1`
  FOREIGN KEY (`product_id_1` )
  REFERENCES `plugin_products_product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `product_related_to_fk_2`
  FOREIGN KEY (`product_id_2` )
  REFERENCES `plugin_products_product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_discount_format`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_discount_format` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `type_id` TINYINT NULL ,
  `code` VARCHAR(255) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_products_discount_rate`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_products_discount_rate` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `format_id` INT NULL ,
  `range_from` DECIMAL(10,2) NOT NULL ,
  `range_to` DECIMAL(10,2) NOT NULL ,
  `discount_rate` DECIMAL(5,2) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `discount_rate_idx_1` (`format_id` ASC) ,
  CONSTRAINT `discount_rate_fk_1`
  FOREIGN KEY (`format_id` )
  REFERENCES `plugin_products_discount_format` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('product', 'Products', '1', '1', 'products');

DELETE IGNORE FROM `plugins` WHERE `name` = 'product';

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('products', 'Products', '1', '1', 'products');

-- -----------------------------------------------------
-- WPPROD-113 SEO fields added
-- -----------------------------------------------------
alter table plugin_products_product
 add column `seo_title` varchar(255) DEFAULT NULL AFTER `weight`,
 add column `seo_keywords` varchar(255) DEFAULT NULL AFTER `seo_title`,
 add column `seo_description` varchar(255) DEFAULT NULL AFTER `seo_keywords`,
 add column `seo_footer` varchar(255) DEFAULT NULL AFTER `seo_description`;


-- -----------------------------------------------------
-- WPPROD-151 Setting to control number of items in feed
-- -----------------------------------------------------

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`,`note`, `type`, `group`, `required`, `options`)
  VALUES ('products_per_page', 'No. of Products Displayed in Feed', '', '', '', '', '', 'both','Enter the maximum number of products you would like to see in your product feed', 'text', 'Products', '0', '');


-- -----------------------------------------------------
-- WPPROD-229 Product Display Options
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
	VALUES ('product_listing_display', 'Product  listing display', 'horizontal', 'both', 'Choose to display products in the listing horizontally or vertically.<br />Vertically includes a brief description of each product.', 'select', 'Products', '0', 'Model_Product,get_listing_display_types');
INSERT IGNORE INTO `settings` (`id`, `variable`, `name`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
	VALUES ('', 'product_listing_order', 'Listing order', 'date_entered', 'order', 'both', 'Sort products by name, date added or system. To control the system order, fill in the order column in the products page.', 'select', 'Products', '0', 'Model_Product,get_order_types');
INSERT IGNORE INTO `settings` (`id`, `variable`, `name`, `value_dev`, `default`, `location`, `type`, `group`, `required`, `options`)
	VALUES ('', 'product_listing_sort', 'Sorting type', 'DESC', 'ASC', 'both', 'select', 'Products', '0', 'Model_Product,get_sort_types');
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
	VALUES ('product_details_display', 'Product  details display', 'standard', 'both', '', 'select', 'Products', '0', 'Model_Product,get_details_display_types');
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`)
	VALUES ('product_enquiry', 'Product enquiry button', 'FALSE', 'both', 'Remove purchase options from products in favour of an enquiry button.', 'checkbox', 'Products', '0');

UPDATE `plugins` SET icon = 'products.png' WHERE friendly_name = 'Products';
UPDATE `plugins` SET `plugins`.`order` = 6 WHERE friendly_name = 'Products';

-- -----------------------------------------------------
-- WPPROD-244, WPPROD-245, QI-47, TSOS-170
-- Product feed title truncation setting
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('product_feed_title_truncation', 'Feed title length', '20', 'both', 'The length a title in the products feed can be before it gets cut short.', 'text', 'Products', '0', '');

CREATE TABLE IF NOT EXISTS `plugin_products_carts`(
`id` VARCHAR(20),
`user_agent` TEXT,
`ip_address` VARCHAR(15),
`date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
`date_modified` TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_products_cart_items`(
`item_id` INT NOT NULL AUTO_INCREMENT,
`cart_id` VARCHAR(255),
`id` INT,
`title` VARCHAR(255),
`quantity` INT,
`price` DECIMAL(9,2),
`delete` INT DEFAULT '0',
PRIMARY KEY (`item_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_products_cart_items_options`(
`option_id` INT NOT NULL AUTO_INCREMENT,
`cart_id` VARCHAR(20),
`id` INT,
`label` VARCHAR(255),
`group` VARCHAR(255),
`price` DECIMAL(9,2),
`delete` INT DEFAULT '0',
PRIMARY KEY (`option_id`)
);

ALTER TABLE `plugin_payments_log` ADD COLUMN `cart_id` varchar(255);

-- ----------------------------------------------------
-- WPPROD-464, PHE-165
-- Alert on uploader when a photo dimension is too small
-- ----------------------------------------------------
ALTER IGNORE TABLE `plugin_products_product`
ADD COLUMN `min_width`  INT(11) NULL  AFTER `document`,
ADD COLUMN `min_height` INT(11) NULL  AFTER `min_width` ;

CREATE TABLE IF NOT EXISTS `plugin_products_option_details`(
`id` INT NOT NULL AUTO_INCREMENT,
`product_id` INT,
`option_id` INT,
`quantity` INT DEFAULT 0,
`location` INT,
PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `plugin_products_store_location`(
`id` INT NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255),
PRIMARY KEY (`id`)
);

ALTER IGNORE TABLE `plugin_products_product_options` ADD COLUMN `is_stock` INT;

INSERT IGNORE INTO `plugin_products_store_location` VALUES(1,'Webstore (Online)');

ALTER IGNORE TABLE `plugin_products_option_details` ADD COLUMN `price` DECIMAL(10,2) DEFAULT 0;

-- ----------------------------------------------------
-- WPPROD-503 Removing purchase options
-- ----------------------------------------------------
ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `disable_purchase` TINYINT(1) NOT NULL DEFAULT 0  AFTER `price` ;

ALTER IGNORE TABLE `plugin_products_option_details` ADD COLUMN `publish` TINYINT(1) DEFAULT 1;

ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `quantity_enabled` INT DEFAULT 0;
ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `quantity` INT DEFAULT 0;

ALTER IGNORE TABLE `plugin_products_option` ADD COLUMN `group_label` VARCHAR(255);

CREATE TABLE IF NOT EXISTS `plugin_products_youtube_videos`(
`id` INT NOT NULL AUTO_INCREMENT,
`product_id` INT(11),
`video_id` VARCHAR(255),
PRIMARY KEY (`id`)
);


-- ----------------------------------------------------
-- TOF-94 Product to have multiple categories
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugin_products_product_categories` (
  `product_id`  INT NOT NULL ,
  `category_id` INT NOT NULL ,
  PRIMARY KEY (`product_id`, `category_id`) ,
  INDEX `product_categories_idx_1` (`product_id`  ASC) ,
  INDEX `product_categories_idx_2` (`category_id` ASC) ,
  CONSTRAINT `product_categories_fk_1`
  FOREIGN KEY (`product_id` )
  REFERENCES `plugin_products_product` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `product_categories_fk_2`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_products_category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- transfer products using one-to-one product-category system to one-to-many system
INSERT IGNORE INTO `plugin_products_product_categories`
(SELECT `id` AS `product_id`, `category_id` FROM `plugin_products_product` WHERE `category_id` IS NOT NULL);

ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `builder` INT(1) DEFAULT 0;

ALTER IGNORE TABLE `plugin_products_option` MODIFY COLUMN `value` VARCHAR(255);

CREATE TABLE IF NOT EXISTS `plugin_products_matrices` (
`id`  INT(11) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255),
`option_1`  VARCHAR(255) NOT NULL,
`option_2`  VARCHAR(255) NOT NULL,
`enabled`  INT(1) DEFAULT 0,
`delete`  INT(1) DEFAULT 0,
PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_products_matrix_options` (
`id`  INT(11) NOT NULL AUTO_INCREMENT,
`matrix_id` INT(11),
`option1` INT(11),
`option2` INT(11),
`price`  VARCHAR(255),
`price_adjustment`  INT(1),
`publish` INT(11),
PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_products_matrix_options_extended` (
`id`  INT(11) NOT NULL AUTO_INCREMENT,
`data_id` INT(5) NOT NULL,
`option1` INT(11),
`option2` INT(11),
`option3` INT(11),
`option_group` VARCHAR(255),
`publish` INT(1),
`price` INT(11),
`price_adjustment` INT(1),
PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- ----------------------------------------------------
-- LPT-196 turn off products menu and display products on home page
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES ('home_page_products_feed', 'Home Page Products Feed', 'FALSE', 'both', 'Display a products feed on the home page', 'checkbox', 'Products', '0', '');

-- ----------------------------------------------------
-- TOF-102 Product search bar
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`)
VALUES ('product_search_bar', 'Search Bar', 'FALSE', 'both', 'Enable a search bar for searching products', 'checkbox', 'Products', '0');

-- ----------------------------------------------------
-- SCR-14 - Sign Builder frontend todos - tooltip text
-- ----------------------------------------------------
ALTER TABLE `plugin_products_option` ADD COLUMN `description` BLOB NULL  AFTER `value` ;

ALTER IGNORE TABLE `plugin_products_matrix_options` ADD COLUMN `image` INT(11) NULL;
ALTER IGNORE TABLE `plugin_products_matrix_options_extended` ADD COLUMN `image` INT(11) NULL;
ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `matrix` INT(11) DEFAULT 0;


ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `over_18` INT(1) DEFAULT 0;

-- ----------------------------------------------------
-- IBCMS-62 Template Signs
-- ----------------------------------------------------
ALTER TABLE `plugin_products_product` ADD COLUMN `sign_builder_layers` BLOB NULL DEFAULT NULL;

-- ----------------------------------------------------
-- LP-5 - Sign Builder improvements and site snags - min and max dimensions
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `required`) VALUES
('sign_builder_max_width',  'Max Width',  'both', 'The maximum width in millimetres a sign builder can be with the &quot;custom&quot; option selected.',  'text', 'Sign Builder', '0'),
('sign_builder_min_width',  'Min Width',  'both', 'The minimum width in millimetres a sign builder can be with the &quot;custom&quot;  option selected.',  'text', 'Sign Builder', '0'),
('sign_builder_max_height', 'Max Height', 'both', 'The maximum height in millimetres a sign builder can be with the &quot;custom&quot;  option selected.', 'text', 'Sign Builder', '0'),
('sign_builder_min_height', 'Min Height', 'both', 'The minimum height in millimetres a sign builder can be with the &quot;custom&quot;  option selected.', 'text', 'Sign Builder', '0');

-- ----------------------------------------------------
-- IBCMS-143 - Add product menu setting to toggle display
-- ----------------------------------------------------
INSERT INTO `settings` (`id`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('', 'products_menu', 'Products Menu', '1', '1', '1', '1', '1', 'both', 'Toggles the display of the automatically generated products menu.', 'toggle_button', 'Products', '0', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- IBCMS-163, SCR-53 Show Width and Height in Step 1
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('show_dimensions_with_preset_selected', 'Show Dimensions With Preset Selected', '1', '1', '1', '1', '1', 'both', 'Display the width and height input boxes, after a preset with fixed width and height has been selected.', 'toggle_button', 'Sign Builder', '0', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- IBCMS-172, LP-38 Step 5 change
-- ----------------------------------------------------
INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`) VALUES ('sign_builder_finish_on', 'Add Finish step', '1', '1', '1', '1', '1', 'both', 'Include &quot;Add Your Finish&quot; as a step', 'toggle_button', 'Sign Builder', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- IBCMS-173, LP-37 - The words “Product" etc is irrelevant
-- ----------------------------------------------------
INSERT INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('sign_builder_description', 'Description', 'both', 'Text to display on Sign Builder products', 'wysiwyg', 'Sign Builder');
INSERT INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('sign_builder_secondary_description', 'Secondary Description', 'both', 'Secondary text to display on Sign Builder products', 'wysiwyg', 'Sign Builder');

-- ----------------------------------------------------
-- IBCMS-183  - Add left column toggle and footer row toggle
-- ----------------------------------------------------
INSERT INTO `settings` (`id`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('', 'column_menu', 'Menu Column', '1', '1', '1', '1', '1', 'both', 'Toggles the display of the Menu column', 'toggle_button', 'Home Layout', '0', 'Model_Settings,on_or_off');
INSERT INTO `settings` (`id`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('', 'row_bottom', 'Menu Bottom Row', '1', '1', '1', '1', '1', 'both', 'Toggles the display of the Bottom Menu Row', 'toggle_button', 'Home Layout', '0', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- IBCMS-188, SCR-78 - Make background white and not required
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('sign_builder_background_color', 'Default Background', 'both', 'Default background colour for the Sign Builder', 'text', 'Sign Builder');

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('show_sign_select_options', 'Show Sign Select Options', '0', '0', '0', '0', '0', 'both', 'Display the sign select options - whether to show dialog or not.', 'toggle_button', 'Sign Builder', '0', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- SCR-114 5mm bleed around the pdf
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('pdf_bleedline', 'PDF Bleedline gap', 'both', 'Draw a line in the PDF indicating where the sign is to be cut. Enter the distance this line is to be from the edge of the sheet, in mm. If left blank, no line will be drawn.', 'text', 'Sign Builder');

-- ----------------------------------------------------
-- LP-88 CHECKOUT - Country/County
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('checkout_countries', 'Countries at Checkout', 'both', 'A list of countries the user can choose for the shipping address. Put each country on a new line. If empty, a box for the user to type their country is displayed.', 'textarea', 'Products');


-- ----------------------------------------------------
-- LP-84 Sign Builder : Max size of order warning
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `options`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`) VALUES
('sign_builder_area_restriction', 'Restrict by Square Area',       'both', 'Use in conjunction with the Maximum and Minimum square area settings to restrict the size of signs that can be purchased.', 'toggle_button', 'Sign Builder', 'Model_Settings,on_or_off',    '0',  '0',  '0',  '0',  '0');
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES
('sign_builder_max_area',         'Maximum Square Area',           'both', 'Set the maximum square area of a sign. Default units are mm². This can be changed with the Area Units setting.',            'text',          'Sign Builder'),
('sign_builder_min_area',         'Minimum Square Area',           'both', 'Set the minimum square area of a sign. Default units are mm². This can be changed with the Area Units setting.',            'text',          'Sign Builder');
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `options`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`) VALUES
('sign_builder_area_units',       'Square Area Restriction Units', 'both', 'The units, whose square is to use for the maximum and minimum square areas.',                                               'toggle_button', 'Sign Builder', 'Model_Settings,length_units', 'mm', 'mm', 'mm', 'mm', 'mm');

UPDATE IGNORE `settings` SET `note` = 'Set the maximum square area of a sign. Default units are mm&sup2;. This can be changed with the Area Units setting.' WHERE `variable` = 'sign_builder_max_area';
UPDATE IGNORE `settings` SET `note` = 'Set the minimum square area of a sign. Default units are mm&sup2;. This can be changed with the Area Units setting.' WHERE `variable` = 'sign_builder_min_area';
UPDATE IGNORE `settings` SET `note` = 'The units, whose square is to be used for the maximum and minimum square areas.'                                     WHERE `variable` = 'sign_builder_area_units';


-- ----------------------------------------------------
-- IBCMS-241: Offers time and profile based
-- ----------------------------------------------------

ALTER IGNORE TABLE `plugin_products_discount_format` ADD COLUMN `date_available_from` timestamp NULL DEFAULT NULL;
ALTER IGNORE TABLE `plugin_products_discount_format` ADD COLUMN `date_available_till` timestamp NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `plugin_products_discount_format_users_roles` (
  `usersrole_id` int(10) unsigned DEFAULT NULL,
  `discountformat_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB;


-- ----------------------------------------------------
-- IBCMS-256, HPG-14: Search Site via labels
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugin_products_tags` (
  `id`            INT NOT NULL AUTO_INCREMENT ,
  `title`         VARCHAR(255) NOT NULL ,
  `description`   VARCHAR(255) NULL ,
  `information`   BLOB NULL ,
  `order`         INT  NULL ,
  `publish`       TINYINT NOT NULL DEFAULT 1 ,
  `deleted`       TINYINT NOT NULL DEFAULT 0,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP  ,
  `date_entered`  TIMESTAMP NULL,
  `modified_by`   INT NOT NULL ,
  `created_by`    INT NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_products_product_tags` (
  `product_id` INT NOT NULL ,
  `tag_id`     INT NOT NULL ,
  PRIMARY KEY (`product_id`, `tag_id`) )
  ENGINE = InnoDB;

 ALTER IGNORE TABLE `plugin_products_tags` CHANGE COLUMN `date_entered` `date_created` TIMESTAMP NULL DEFAULT NULL  ;
