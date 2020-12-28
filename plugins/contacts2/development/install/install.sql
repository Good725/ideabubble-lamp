SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `plugin_contacts_mailing_list`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_contacts_mailing_list` ;

CREATE  TABLE IF NOT EXISTS `plugin_contacts_mailing_list` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin_contacts_contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_contacts_contact` ;

CREATE  TABLE IF NOT EXISTS `plugin_contacts_contact` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `mailing_list` INT NOT NULL ,
  `phone` VARCHAR(15) NULL ,
  `mobile` VARCHAR(15) NULL ,
  `notes` VARCHAR(255) NULL ,
  `publish` TINYINT NOT NULL ,
  `last_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_contact_mailing_list_idx` (`mailing_list` ASC) ,
  CONSTRAINT `fk_contact_mailing_list`
    FOREIGN KEY (`mailing_list` )
    REFERENCES `plugin_contacts_mailing_list` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `plugin_contacts_mailing_list`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `plugin_contacts_mailing_list` (`id`, `name`) VALUES (NULL, 'Default');

COMMIT;
