/*
ts:2015-01-01 00:00:23
*/
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

INSERT IGNORE INTO `plugins_per_role`
  SELECT `plugins`.`id`, `project_role`.`id`, 1 FROM `plugins`, `project_role` WHERE `plugins`.`name` = 'notifications' AND `project_role`.`role` != 'External User';

UPDATE `plugins` SET icon = 'notifications.png' WHERE friendly_name = 'Notifications';
UPDATE `plugins` SET `plugins`.`order` = 11 WHERE friendly_name = 'Notifications';

INSERT IGNORE INTO `plugin_notifications_event` (`name`,`description`,`from`,`subject`) VALUES('reset_cms_password','Notification to send the reset email for the CMS.','','Password Reset Email');

-- --------------------------------------------------------------
-- IBCMS-243 Members Registration, Approval & Management (back end part)
-- --------------------------------------------------------------
INSERT IGNORE INTO `plugin_notifications_event` (`id`,`name`,`description`,`from`,`subject`,`header`,`footer`) VALUES (NULL,'approval_discount','Approval Discount','','You got discount',NULL,NULL);

-- --------------------------------------------------------------
-- PCSYS-143 Members Registration, Approval & Management (back end part)
-- --------------------------------------------------------------

SELECT id INTO @event_id FROM plugin_notifications_event where `name` = 'approval_discount';
	delete from `plugin_notifications_to` where `id_event` = @event_id;
	delete from `plugin_notifications_cc` where `id_event` = @event_id;
	delete from `plugin_notifications_bcc` where `id_event` = @event_id;
DELETE FROM `plugin_notifications_event` where id = @event_id;

ALTER IGNORE TABLE `plugin_notifications_event` ADD COLUMN `deleted` INT(1) NOT NULL DEFAULT 0 AFTER `footer` ;
