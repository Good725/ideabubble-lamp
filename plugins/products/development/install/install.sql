SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `plugin_products_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_products_category` ;

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
DROP TABLE IF EXISTS `plugin_products_postage_format` ;

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
DROP TABLE IF EXISTS `plugin_products_product` ;

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
DROP TABLE IF EXISTS `plugin_products_product_images` ;

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
DROP TABLE IF EXISTS `plugin_products_postage_zone` ;

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
DROP TABLE IF EXISTS `plugin_products_postage_rate` ;

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
DROP TABLE IF EXISTS `plugin_products_option` ;

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
DROP TABLE IF EXISTS `plugin_products_product_options` ;

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
DROP TABLE IF EXISTS `plugin_products_product_related_to` ;

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
DROP TABLE IF EXISTS `plugin_products_discount_format` ;

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
DROP TABLE IF EXISTS `plugin_products_discount_rate` ;

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


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


-- -----------------------------------------------------
-- Activate plugin
-- -----------------------------------------------------
DELETE FROM `plugins` WHERE `name` = 'products';
INSERT INTO `plugins`(`name`, `version`, `folder`, `menu`, `is_backend`, `enabled`, `requires_media`, `media_folder`) VALUES('products', 'development', 'products', 'Products', 1, 1, 1, 'products');
