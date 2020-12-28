SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `plugin_locations_location`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_locations_location` ;

CREATE  TABLE IF NOT EXISTS `plugin_locations_location` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(255) NOT NULL ,
  `address_1` VARCHAR(255) NOT NULL ,
  `address_2` VARCHAR(255) NOT NULL ,
  `address_3` VARCHAR(255) NOT NULL ,
  `county` VARCHAR(255) NOT NULL ,
  `phone` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `map_reference` VARCHAR(255) NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `modified_by` INT NOT NULL ,
  `created_by` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


-- -----------------------------------------------------
-- Activate plugin
-- -----------------------------------------------------
DELETE FROM `plugins` WHERE `name` = 'locations';
INSERT INTO `plugins`(`name`, `version`, `folder`, `menu`, `is_frontend`, `is_backend`, `enabled`) VALUES('locations', 'development', 'locations', 'Locations', 1, 1, 1);
