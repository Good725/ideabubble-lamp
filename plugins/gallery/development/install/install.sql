SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `plugin_gallery_gallery`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_gallery_gallery` ;

CREATE  TABLE IF NOT EXISTS `plugin_gallery_gallery` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `photo_name` VARCHAR(100) NULL ,
  `category` VARCHAR(100) NULL ,
  `title` TEXT NULL ,
  `order` SMALLINT NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `created_by` INT(10) NOT NULL ,
  `modified_by` INT(10) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Activate plugin
-- -----------------------------------------------------
DELETE FROM `plugins` WHERE `name` = 'gallery';
INSERT INTO `plugins`(`name`, `version`, `folder`, `menu`, `is_backend`, `enabled`, `requires_media`, `media_folder`) VALUES('gallery', 'development', 'gallery', 'Gallery', 1, 1, 1, 'gallery');