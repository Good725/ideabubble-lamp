SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `plugin_notifications_event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_notifications_event` ;

CREATE  TABLE IF NOT EXISTS `plugin_notifications_event` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `from` VARCHAR(255) NOT NULL ,
  `subject` VARCHAR(78) NULL COMMENT 'See RFC-2822, Section 2.1.1.' ,
  `header` BLOB NULL ,
  `footer` BLOB NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin_notifications_to`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_notifications_to` ;

CREATE  TABLE IF NOT EXISTS `plugin_notifications_to` (
  `id_event` INT NOT NULL ,
  `id_contact` INT NOT NULL ,
  PRIMARY KEY (`id_event`, `id_contact`) ,
  INDEX `fk_to_id_email_idx` (`id_event` ASC) ,
  CONSTRAINT `fk_plugin_notifications_to_plugin_notifications_notification`
    FOREIGN KEY (`id_event` )
    REFERENCES `plugin_notifications_event` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin_notifications_cc`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_notifications_cc` ;

CREATE  TABLE IF NOT EXISTS `plugin_notifications_cc` (
  `id_event` INT NOT NULL ,
  `id_contact` INT NOT NULL ,
  PRIMARY KEY (`id_event`, `id_contact`) ,
  INDEX `fk_cc_id_email_idx` (`id_event` ASC) ,
  CONSTRAINT `fk_plugin_notifications_cc_plugin_notifications_notification`
    FOREIGN KEY (`id_event` )
    REFERENCES `plugin_notifications_event` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin_notifications_bcc`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin_notifications_bcc` ;

CREATE  TABLE IF NOT EXISTS `plugin_notifications_bcc` (
  `id_event` INT NOT NULL ,
  `id_contact` INT NOT NULL ,
  PRIMARY KEY (`id_event`, `id_contact`) ,
  INDEX `fk_bcc_id_email_idx` (`id_event` ASC) ,
  CONSTRAINT `fk_plugin_notifications_bcc_plugin_notifications_notification`
    FOREIGN KEY (`id_event` )
    REFERENCES `plugin_notifications_event` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
