/*
ts:2015-01-01 00:00:10
*/
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

CREATE OR REPLACE VIEW `view_plugin_contacts` AS select `plugin_contacts_contact`.`id` AS `id`,`plugin_contacts_contact`.`first_name` AS `first_name`,`plugin_contacts_contact`.`last_name` AS `last_name`,`plugin_contacts_contact`.`email` AS `email`,`plugin_contacts_contact`.`mailing_list` AS `mailing_list`,`plugin_contacts_contact`.`phone` AS `phone`,`plugin_contacts_contact`.`mobile` AS `mobile`,`plugin_contacts_contact`.`notes` AS `notes`,`plugin_contacts_contact`.`last_modification` AS `last_modification`,`plugin_contacts_mailing_list`.`name` AS `mailing_list_name` from (`plugin_contacts_contact` join `plugin_contacts_mailing_list`) where (`plugin_contacts_contact`.`mailing_list` = `plugin_contacts_mailing_list`.`id`) WITH CASCADED CHECK OPTION;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('contacts2', 'Contacts', '1', '0', NULL);

--
--  Set the proper name of this plugin
--

UPDATE IGNORE `plugins` SET `friendly_name` = 'Contacts' WHERE `name` = 'contacts2';

UPDATE `plugins` SET icon = 'contacts.png' WHERE friendly_name = 'Contacts';
UPDATE `plugins` SET `plugins`.`order` = 9 WHERE friendly_name = 'Contacts';

--
-- WPPROD-278 - Deleting a contact erases any trace of them
--

ALTER TABLE `plugin_contacts_contact` ADD COLUMN `deleted` TINYINT(1) NULL DEFAULT NULL AFTER `publish`;
UPDATE `plugin_contacts_contact` SET `deleted` = 0 WHERE `deleted` IS NULL;
ALTER TABLE `plugin_contacts_contact` CHANGE COLUMN `deleted` `deleted` TINYINT(1) NULL DEFAULT 0  ;

INSERT INTO plugin_messaging_recipient_providers  (id, `plugin`, class_name) values ('CMS_CONTACT',  'contacts2', 'Model_MessagingRecipientProviderContact');
INSERT INTO plugin_messaging_recipient_providers  (id, `plugin`, class_name) values ('CMS_CONTACT_LIST',  'contacts2', 'Model_MessagingRecipientProviderContactlist');

INSERT IGNORE INTO `plugin_contacts_mailing_list` (`name`) VALUES ('Newsletter');

INSERT IGNORE INTO `plugin_contacts_mailing_list` (`name`) VALUES ('Admin');
INSERT IGNORE INTO `plugin_contacts_contact` (`first_name`, `email`, `mailing_list`, `publish`, `deleted`, `last_modification`)
SELECT 'Admin', 'admin@ideabubble.ie', `id`, '1', '0', CURRENT_TIMESTAMP FROM `plugin_contacts_mailing_list` WHERE `name` = 'Admin';

