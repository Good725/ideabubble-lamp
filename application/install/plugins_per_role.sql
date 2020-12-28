SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `plugins_per_role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugins_per_role` ;

CREATE  TABLE IF NOT EXISTS `plugins_per_role` (
  `plugin_id` INT NOT NULL ,
  `role_id` INT NOT NULL ,
  `enabled` TINYINT NOT NULL ,
  PRIMARY KEY (`role_id`, `plugin_id`) )
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
