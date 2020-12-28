--
-- CONTACTS
--

-- -----------------------------------------------------
-- Table `plugin_contacts_mailing_list`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_contacts_mailing_list` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_contacts_contact`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_contacts_contact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `mailing_list` INT(11) NOT NULL ,
  `phone` VARCHAR(15) NULL DEFAULT NULL ,
  `mobile` VARCHAR(15) NULL DEFAULT NULL ,
  `notes` VARCHAR(255) NULL DEFAULT NULL ,
  `publish` TINYINT(4) NULL DEFAULT NULL ,
  `last_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_contact_mailing_list_idx` (`mailing_list` ASC) ,
  CONSTRAINT `fk_contact_mailing_list`
  FOREIGN KEY (`mailing_list` )
  REFERENCES `plugin_contacts_mailing_list` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('contacts2', 'Contacts', '1', '0', NULL);

--
-- LOCATIONS
--

-- -----------------------------------------------------
-- Table `plugin_locations_location`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_locations_location` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(255) NOT NULL ,
  `address_1` VARCHAR(255) NOT NULL ,
  `address_2` VARCHAR(255) NOT NULL ,
  `address_3` VARCHAR(255) NOT NULL ,
  `county` VARCHAR(255) NOT NULL ,
  `phone` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `map_reference` VARCHAR(255) NOT NULL ,
  `publish` TINYINT(4) NOT NULL ,
  `deleted` TINYINT(4) NOT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL DEFAULT NULL ,
  `modified_by` INT(11) NOT NULL ,
  `created_by` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('locations', 'Locations', '1', '0', NULL);

--
-- NEWS
--

-- -----------------------------------------------------
-- Table `plugin_news_categories`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_news_categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(200) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_news`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_news` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `content` LONGTEXT NULL DEFAULT NULL ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `event_date` DATETIME NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_news_plugin_news_categories_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_plugin_news_plugin_news_categories`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_news_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('news', 'News', '1', '1', 'news');

--
-- NOTIFICATIONS
--

-- -----------------------------------------------------
-- Table `plugin_notifications_event`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_notifications_event` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `from` VARCHAR(255) NOT NULL ,
  `subject` VARCHAR(78) NULL DEFAULT NULL COMMENT 'See RFC-2822, Section 2.1.1.' ,
  `header` BLOB NULL DEFAULT NULL ,
  `footer` BLOB NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_notifications_bcc`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_notifications_bcc` (
  `id_event` INT(11) NOT NULL ,
  `id_contact` INT(11) NOT NULL ,
  PRIMARY KEY (`id_event`, `id_contact`) ,
  INDEX `fk_bcc_id_email_idx` (`id_event` ASC) ,
  CONSTRAINT `fk_plugin_notifications_bcc_plugin_notifications_notification`
  FOREIGN KEY (`id_event` )
  REFERENCES `plugin_notifications_event` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_notifications_cc`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_notifications_cc` (
  `id_event` INT(11) NOT NULL ,
  `id_contact` INT(11) NOT NULL ,
  PRIMARY KEY (`id_event`, `id_contact`) ,
  INDEX `fk_cc_id_email_idx` (`id_event` ASC) ,
  CONSTRAINT `fk_plugin_notifications_cc_plugin_notifications_notification`
  FOREIGN KEY (`id_event` )
  REFERENCES `plugin_notifications_event` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_notifications_to`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_notifications_to` (
  `id_event` INT(11) NOT NULL ,
  `id_contact` INT(11) NOT NULL ,
  PRIMARY KEY (`id_event`, `id_contact`) ,
  INDEX `fk_to_id_email_idx` (`id_event` ASC) ,
  CONSTRAINT `fk_plugin_notifications_to_plugin_notifications_notification`
  FOREIGN KEY (`id_event` )
  REFERENCES `plugin_notifications_event` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('notifications', 'Notifications', '1', '0', NULL);

--
-- PANELS
--

-- -----------------------------------------------------
-- Table `plugin_panels`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_panels` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `page_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `position` VARCHAR(45) NULL DEFAULT NULL ,
  `order_no` INT(3) UNSIGNED NULL DEFAULT '0' ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `text` TEXT NULL DEFAULT NULL ,
  `link_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `link_url` VARCHAR(255) NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('panels', 'Panels', '1', '1', 'panels');

--
-- PRODUCTS
--

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

--
-- TESTIMONIALS
--

-- -----------------------------------------------------
-- Table `plugin_testimonials_categories`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_testimonials_categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(200) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_testimonials`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_testimonials` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `item_signature` TEXT NULL DEFAULT NULL ,
  `item_company` TEXT NULL DEFAULT NULL ,
  `item_website` TEXT NULL DEFAULT NULL ,
  `content` LONGTEXT NULL DEFAULT NULL ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `event_date` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_testimonials_plugin_testimonials_categories_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_plugin_testimonials_plugin_testimonials_categories`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_testimonials_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('testimonials', 'Testimonials', '1', '1', 'testimonials');

--
-- MEDIA
--

-- -----------------------------------------------------
-- Table `shared_media`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `shared_media` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `filename` VARCHAR(200) NOT NULL ,
  `dimensions` VARCHAR(50) NULL DEFAULT NULL ,
  `location` VARCHAR(100) NOT NULL ,
  `size` INT(20) NOT NULL ,
  `mime_type` VARCHAR(50) NOT NULL ,
  `hash` VARCHAR(32) NULL DEFAULT NULL ,
  `preset_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shared_media_photo_presets`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `shared_media_photo_presets` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(50) NOT NULL ,
  `directory` VARCHAR(100) NOT NULL DEFAULT 'content' ,
  `height_large` INT(10) UNSIGNED NOT NULL ,
  `width_large` INT(10) UNSIGNED NOT NULL ,
  `action_large` VARCHAR(10) NOT NULL DEFAULT 'fit' ,
  `thumb` TINYINT(1) NOT NULL ,
  `height_thumb` INT(10) UNSIGNED NOT NULL ,
  `width_thumb` INT(10) UNSIGNED NOT NULL ,
  `action_thumb` VARCHAR(10) NOT NULL DEFAULT 'crop' ,
  `date_created` TIMESTAMP NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NOT NULL ,
  `modified_by` INT(10) UNSIGNED NOT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('media', 'Media', '1', '1', 'media');

--
-- FORM PROCESSOR
--

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('formprocessor', 'Form Processor', '0', '0', NULL);

--
-- UPLOADER
--

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('uploader', 'Uploader', '0', '0', NULL);

--
-- PAYMENTS
--

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('payments', 'Payments', '0', '0', NULL);

--
-- MENUS
--

-- -----------------------------------------------------
-- Table `pmenus`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `pmenus` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(100) NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `link_tag` INT(11) NOT NULL ,
  `link_url` VARCHAR(500) NOT NULL ,
  `has_sub` TINYINT(1) NOT NULL ,
  `parent_id` INT(11) NOT NULL ,
  `menu_order` INT(11) NOT NULL ,
  `publish` TINYINT(1) NOT NULL ,
  `deleted` TINYINT(1) NOT NULL ,
  `date_modified` DATETIME NOT NULL ,
  `date_entered` DATETIME NOT NULL ,
  `created_by` INT(11) NOT NULL ,
  `modified_by` INT(11) NOT NULL ,
  `menus_target` VARCHAR(20) NOT NULL DEFAULT '_top' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('menus', 'Menus', '1', '0', NULL);

--
-- PAGES
--

-- -----------------------------------------------------
-- Table `ppages_categorys`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `ppages_categorys` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_categorys_pages1` (`id` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `ppages_layouts`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `ppages_layouts` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `layout` VARCHAR(25) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_layouts_pages` (`id` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `ppages`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `ppages` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name_tag` VARCHAR(255) NULL DEFAULT NULL ,
  `title` VARCHAR(255) NULL DEFAULT NULL ,
  `content` TEXT NULL DEFAULT NULL ,
  `banner_photo` VARCHAR(500) NULL DEFAULT NULL ,
  `seo_keywords` VARCHAR(500) NULL DEFAULT NULL ,
  `seo_description` VARCHAR(500) NULL DEFAULT NULL ,
  `footer` VARCHAR(500) NULL DEFAULT NULL ,
  `date_entered` DATETIME NULL DEFAULT NULL ,
  `last_modified` DATETIME NULL DEFAULT NULL ,
  `created_by` INT(11) NULL DEFAULT NULL ,
  `modified_by` INT(11) NULL DEFAULT NULL ,
  `publish` TINYINT(1) NOT NULL DEFAULT '0' ,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0' ,
  `include_sitemap` TINYINT(1) NOT NULL DEFAULT '0' ,
  `layout_id` INT(10) UNSIGNED NOT NULL ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ppages_ppages_layouts_idx` (`layout_id` ASC) ,
  INDEX `fk_ppages_ppages_categorys1_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_ppages_ppages_categorys1`
  FOREIGN KEY (`category_id` )
  REFERENCES `ppages_categorys` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ppages_ppages_layouts`
  FOREIGN KEY (`layout_id` )
  REFERENCES `ppages_layouts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('pages', 'Pages', '1', '0', NULL);

--
-- VIEWS
--

-- -----------------------------------------------------
-- Placeholder table for view `pmedia_view_media_presets_list_admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pmedia_view_media_presets_list_admin` (`id` INT, `title` INT, `directory` INT, `height_large` INT, `width_large` INT, `action_large` INT, `thumb` INT, `height_thumb` INT, `width_thumb` INT, `action_thumb` INT, `date_created` INT, `created_by` INT, `created_by_name` INT, `created_by_role` INT, `date_modified` INT, `modified_by` INT, `modified_by_name` INT, `modified_by_role` INT, `publish` INT);

-- -----------------------------------------------------
-- Placeholder table for view `pnews_view_news_list_admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pnews_view_news_list_admin` (`id` INT, `title` INT, `category_id` INT, `category` INT, `summary` INT, `content` INT, `image` INT, `event_date` INT, `date_publish` INT, `date_remove` INT, `date_created` INT, `created_by` INT, `created_by_name` INT, `created_by_role` INT, `date_modified` INT, `modified_by` INT, `modified_by_name` INT, `modified_by_role` INT, `publish` INT);

-- -----------------------------------------------------
-- Placeholder table for view `pnews_view_news_list_front_end`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pnews_view_news_list_front_end` (`id` INT, `title` INT, `category` INT, `summary` INT, `content` INT, `image` INT, `event_date` INT, `date_publish` INT, `date_remove` INT, `date_modified` INT);

-- -----------------------------------------------------
-- Placeholder table for view `ppanels_view_panels_list_admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ppanels_view_panels_list_admin` (`id` INT, `page_id` INT, `title` INT, `position` INT, `order_no` INT, `image` INT, `text` INT, `link_id` INT, `link_url` INT, `date_publish` INT, `date_remove` INT, `date_created` INT, `created_by` INT, `created_by_name` INT, `created_by_role` INT, `date_modified` INT, `modified_by` INT, `modified_by_name` INT, `modified_by_role` INT, `publish` INT);

-- -----------------------------------------------------
-- Placeholder table for view `ppanels_view_panels_list_front_end`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ppanels_view_panels_list_front_end` (`id` INT, `page_id` INT, `title` INT, `position` INT, `order_no` INT, `image` INT, `text` INT, `link_id` INT, `link_url` INT, `date_publish` INT, `date_remove` INT, `publish` INT, `deleted` INT);

-- -----------------------------------------------------
-- Placeholder table for view `ptestimonials_view_testimonials_list_admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ptestimonials_view_testimonials_list_admin` (`id` INT, `title` INT, `category_id` INT, `category` INT, `summary` INT, `content` INT, `image` INT, `event_date` INT, `item_signature` INT, `item_company` INT, `date_created` INT, `created_by` INT, `created_by_name` INT, `created_by_role` INT, `date_modified` INT, `modified_by` INT, `modified_by_name` INT, `modified_by_role` INT, `publish` INT, `item_website` INT);

-- -----------------------------------------------------
-- Placeholder table for view `ptestimonials_view_testimonials_list_front_end`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ptestimonials_view_testimonials_list_front_end` (`id` INT, `title` INT, `category` INT, `summary` INT, `content` INT, `image` INT, `event_date` INT, `item_company` INT, `item_signature` INT, `date_modified` INT, `item_website` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_plugin_contacts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_plugin_contacts` (`id` INT, `first_name` INT, `last_name` INT, `email` INT, `mailing_list` INT, `phone` INT, `mobile` INT, `notes` INT, `last_modification` INT, `mailing_list_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_ppages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_ppages` (`id` INT, `name_tag` INT, `title` INT, `content` INT, `banner_photo` INT, `category_id` INT, `layout_id` INT, `seo_keywords` INT, `seo_description` INT, `date_entered` INT, `footer` INT, `last_modified` INT, `modified_by` INT, `created_by` INT, `publish` INT, `deleted` INT, `category` INT, `layout` INT, `include_sitemap` INT);

-- -----------------------------------------------------
-- View `pmedia_view_media_presets_list_admin`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `pmedia_view_media_presets_list_admin` ;
DROP TABLE IF EXISTS `pmedia_view_media_presets_list_admin`;
CREATE OR REPLACE VIEW `pmedia_view_media_presets_list_admin` AS select `shared_media_photo_presets`.`id` AS `id`,`shared_media_photo_presets`.`title` AS `title`,`shared_media_photo_presets`.`directory` AS `directory`,`shared_media_photo_presets`.`height_large` AS `height_large`,`shared_media_photo_presets`.`width_large` AS `width_large`,`shared_media_photo_presets`.`action_large` AS `action_large`,`shared_media_photo_presets`.`thumb` AS `thumb`,`shared_media_photo_presets`.`height_thumb` AS `height_thumb`,`shared_media_photo_presets`.`width_thumb` AS `width_thumb`,`shared_media_photo_presets`.`action_thumb` AS `action_thumb`,`shared_media_photo_presets`.`date_created` AS `date_created`,`shared_media_photo_presets`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`shared_media_photo_presets`.`date_modified` AS `date_modified`,`shared_media_photo_presets`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`shared_media_photo_presets`.`publish` AS `publish` from ((((`shared_media_photo_presets` left join `users` `users_create` on((`shared_media_photo_presets`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`shared_media_photo_presets`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`shared_media_photo_presets`.`deleted` = 0);

-- -----------------------------------------------------
-- View `pnews_view_news_list_admin`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `pnews_view_news_list_admin` ;
DROP TABLE IF EXISTS `pnews_view_news_list_admin`;
CREATE OR REPLACE VIEW `pnews_view_news_list_admin` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news`.`category_id` AS `category_id`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_created` AS `date_created`,`plugin_news`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_news`.`publish` AS `publish` from (((((`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) left join `users` `users_create` on((`plugin_news`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_news`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_news`.`deleted` = 0);

-- -----------------------------------------------------
-- View `pnews_view_news_list_front_end`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `pnews_view_news_list_front_end` ;
DROP TABLE IF EXISTS `pnews_view_news_list_front_end`;
CREATE OR REPLACE VIEW `pnews_view_news_list_front_end` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_modified` AS `date_modified` from (`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) where ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));

-- -----------------------------------------------------
-- View `ppanels_view_panels_list_admin`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `ppanels_view_panels_list_admin` ;
DROP TABLE IF EXISTS `ppanels_view_panels_list_admin`;
CREATE OR REPLACE VIEW `ppanels_view_panels_list_admin` AS select `plugin_panels`.`id` AS `id`,`plugin_panels`.`page_id` AS `page_id`,`plugin_panels`.`title` AS `title`,`plugin_panels`.`position` AS `position`,`plugin_panels`.`order_no` AS `order_no`,`plugin_panels`.`image` AS `image`,`plugin_panels`.`text` AS `text`,`plugin_panels`.`link_id` AS `link_id`,`plugin_panels`.`link_url` AS `link_url`,`plugin_panels`.`date_publish` AS `date_publish`,`plugin_panels`.`date_remove` AS `date_remove`,`plugin_panels`.`date_created` AS `date_created`,`plugin_panels`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_created`.`role` AS `created_by_role`,`plugin_panels`.`date_modified` AS `date_modified`,`plugin_panels`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modified`.`role` AS `modified_by_role`,`plugin_panels`.`publish` AS `publish` from ((((`plugin_panels` left join `users` `users_create` on((`plugin_panels`.`created_by` = `users_create`.`id`))) left join `project_role` `roles_created` on((`users_create`.`role_id` = `roles_created`.`id`))) left join `users` `users_modify` on((`plugin_panels`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modified` on((`users_modify`.`role_id` = `roles_modified`.`id`))) where (`plugin_panels`.`deleted` = 0);

-- -----------------------------------------------------
-- View `ppanels_view_panels_list_front_end`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `ppanels_view_panels_list_front_end` ;
DROP TABLE IF EXISTS `ppanels_view_panels_list_front_end`;
CREATE OR REPLACE VIEW `ppanels_view_panels_list_front_end` AS select `plugin_panels`.`id` AS `id`,`plugin_panels`.`page_id` AS `page_id`,`plugin_panels`.`title` AS `title`,`plugin_panels`.`position` AS `position`,`plugin_panels`.`order_no` AS `order_no`,`plugin_panels`.`image` AS `image`,`plugin_panels`.`text` AS `text`,`plugin_panels`.`link_id` AS `link_id`,`plugin_panels`.`link_url` AS `link_url`,`plugin_panels`.`date_publish` AS `date_publish`,`plugin_panels`.`date_remove` AS `date_remove`,`plugin_panels`.`publish` AS `publish`,`plugin_panels`.`deleted` AS `deleted` from `plugin_panels` where ((`plugin_panels`.`publish` = 1) and (`plugin_panels`.`deleted` = 0));

-- -----------------------------------------------------
-- View `ptestimonials_view_testimonials_list_admin`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `ptestimonials_view_testimonials_list_admin` ;
DROP TABLE IF EXISTS `ptestimonials_view_testimonials_list_admin`;
CREATE OR REPLACE VIEW `ptestimonials_view_testimonials_list_admin` AS select `plugin_testimonials`.`id` AS `id`,`plugin_testimonials`.`title` AS `title`,`plugin_testimonials`.`category_id` AS `category_id`,`plugin_testimonials_categories`.`category` AS `category`,`plugin_testimonials`.`summary` AS `summary`,`plugin_testimonials`.`content` AS `content`,`plugin_testimonials`.`image` AS `image`,`plugin_testimonials`.`event_date` AS `event_date`,`plugin_testimonials`.`item_signature` AS `item_signature`,`plugin_testimonials`.`item_company` AS `item_company`,`plugin_testimonials`.`date_created` AS `date_created`,`plugin_testimonials`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_testimonials`.`date_modified` AS `date_modified`,`plugin_testimonials`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_testimonials`.`publish` AS `publish`,`plugin_testimonials`.`item_website` AS `item_website` from (((((`plugin_testimonials` left join `plugin_testimonials_categories` on((`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`))) left join `users` `users_create` on((`plugin_testimonials`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_testimonials`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_testimonials`.`deleted` = 0);

-- -----------------------------------------------------
-- View `ptestimonials_view_testimonials_list_front_end`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `ptestimonials_view_testimonials_list_front_end` ;
DROP TABLE IF EXISTS `ptestimonials_view_testimonials_list_front_end`;
CREATE OR REPLACE VIEW `ptestimonials_view_testimonials_list_front_end` AS select `plugin_testimonials`.`id` AS `id`,`plugin_testimonials`.`title` AS `title`,`plugin_testimonials_categories`.`category` AS `category`,`plugin_testimonials`.`summary` AS `summary`,`plugin_testimonials`.`content` AS `content`,`plugin_testimonials`.`image` AS `image`,`plugin_testimonials`.`event_date` AS `event_date`,`plugin_testimonials`.`item_company` AS `item_company`,`plugin_testimonials`.`item_signature` AS `item_signature`,`plugin_testimonials`.`date_modified` AS `date_modified`,`plugin_testimonials`.`item_website` AS `item_website` from (`plugin_testimonials` left join `plugin_testimonials_categories` on((`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`))) where ((`plugin_testimonials`.`publish` = 1) and (`plugin_testimonials`.`deleted` = 0));

-- -----------------------------------------------------
-- View `view_plugin_contacts`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `view_plugin_contacts` ;
DROP TABLE IF EXISTS `view_plugin_contacts`;
CREATE OR REPLACE VIEW `view_plugin_contacts` AS select `plugin_contacts_contact`.`id` AS `id`,`plugin_contacts_contact`.`first_name` AS `first_name`,`plugin_contacts_contact`.`last_name` AS `last_name`,`plugin_contacts_contact`.`email` AS `email`,`plugin_contacts_contact`.`mailing_list` AS `mailing_list`,`plugin_contacts_contact`.`phone` AS `phone`,`plugin_contacts_contact`.`mobile` AS `mobile`,`plugin_contacts_contact`.`notes` AS `notes`,`plugin_contacts_contact`.`last_modification` AS `last_modification`,`plugin_contacts_mailing_list`.`name` AS `mailing_list_name` from (`plugin_contacts_contact` join `plugin_contacts_mailing_list`) where (`plugin_contacts_contact`.`mailing_list` = `plugin_contacts_mailing_list`.`id`) WITH CASCADED CHECK OPTION;

-- -----------------------------------------------------
-- View `view_ppages`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `view_ppages` ;
DROP TABLE IF EXISTS `view_ppages`;
CREATE OR REPLACE VIEW `view_ppages` AS select `ppages`.`id` AS `id`,`ppages`.`name_tag` AS `name_tag`,`ppages`.`title` AS `title`,`ppages`.`content` AS `content`,`ppages`.`banner_photo` AS `banner_photo`,`ppages`.`category_id` AS `category_id`,`ppages`.`layout_id` AS `layout_id`,`ppages`.`seo_keywords` AS `seo_keywords`,`ppages`.`seo_description` AS `seo_description`,`ppages`.`date_entered` AS `date_entered`,`ppages`.`footer` AS `footer`,`ppages`.`last_modified` AS `last_modified`,`ppages`.`modified_by` AS `modified_by`,`ppages`.`created_by` AS `created_by`,`ppages`.`publish` AS `publish`,`ppages`.`deleted` AS `deleted`,`ppages_categorys`.`category` AS `category`,`ppages_layouts`.`layout` AS `layout`,`ppages`.`include_sitemap` AS `include_sitemap` from ((`ppages` left join `ppages_layouts` on((`ppages`.`layout_id` = `ppages_layouts`.`id`))) left join `ppages_categorys` on((`ppages`.`category_id` = `ppages_categorys`.`id`)));

-- -----------------------------------------------------
-- WPPROD-300 CMS icons
-- -----------------------------------------------------
UPDATE `plugins` SET `icon`='services.pnp' WHERE `name`='services';

