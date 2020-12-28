/*
ts:2015-01-01 00:00:06
*/
CREATE TABLE IF NOT EXISTS `plugin_cardbuilder_cards` (
  `id`            INT       UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id`       INT       UNSIGNED NOT NULL ,
  `form_data`     BLOB               NULL ,
  `employee_name` VARCHAR(255)       NULL ,
  `title`         VARCHAR(63)        NULL ,
  `department`    VARCHAR(255)       NULL ,
  `telephone`     VARCHAR(217)       NULL ,
  `fax`           VARCHAR(127)       NULL ,
  `mobile`        VARCHAR(63)        NULL ,
  `email`         VARCHAR(127)       NULL ,
  `office_id`     INT(11)            NULL ,
  `approved`      INT(1)    UNSIGNED NOT NULL DEFAULT 0 ,
  `created_by`    INT(11)   UNSIGNED NULL ,
  `modified_by`   INT(11)   UNSIGNED NULL ,
  `date_created`  TIMESTAMP          NULL     DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP          NULL ,
  `publish`       INT(1)             NOT NULL DEFAULT 1 ,
  `deleted`       INT(1)             NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) );

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('enable_card_builder', 'Enable Card Builder', '0', '0', '0', '0', '0', 'both', 'Enable the Card Builder', 'toggle_button', 'Products', 'Model_Settings,on_or_off');

ALTER IGNORE TABLE `plugin_cardbuilder_cards` ADD COLUMN `printed` INT(1) NULL DEFAULT 0;

-- --------------------------------------------------------------
-- REG-39 Email orders issued with PDF attached
-- --------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugin_cardbuilder_orders` (
  `id`             INT       UNSIGNED NOT NULL AUTO_INCREMENT ,
  `printed`        INT(1)    NULL DEFAULT 0 ,
  `created_by`     INT(11)   NOT NULL ,
  `modified_by`    INT(11)   NULL ,
  `date_created`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified`  TIMESTAMP NULL ,
  `deleted`        INT(1)    NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) );

ALTER IGNORE TABLE `plugin_cardbuilder_cards` ADD COLUMN `order_id` INT(11) NULL  AFTER `id` ;

ALTER IGNORE TABLE `plugin_cardbuilder_cards` ADD COLUMN `post_nominal_letters` VARCHAR(127) NULL  AFTER `title` ;
