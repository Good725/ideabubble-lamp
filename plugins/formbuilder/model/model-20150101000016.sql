/*
ts:2015-01-01 00:00:16
*/
CREATE TABLE IF NOT EXISTS `plugin_formbuilder_forms`(
`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
`form_name` varchar(60),
`action` varchar(60),
`method` bit(1),
`class` varchar(255),
`fields` text,
`options` varchar(255),
`deleted` bit(1),
`publish` bit(1),
`date_created` DATETIME NULL DEFAULT NULL,
`date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
UNIQUE INDEX `id_UNIQUE` (`id` DESC))
ENGINE = InnoDB;

ALTER TABLE `plugin_formbuilder_forms` ADD `summary` TEXT;
ALTER TABLE `plugin_formbuilder_forms` ADD `captcha_enabled` bit(1);
ALTER TABLE `plugin_formbuilder_forms` CHANGE `method` `method` varchar(5) NOT NULL;
ALTER TABLE `plugin_formbuilder_forms` ADD `form_id` VARCHAR(255);

-- WPPROD-669 Change captcha to YES doesn't save when we click SAVE and EXIT
ALTER TABLE `plugin_formbuilder_forms` CHANGE COLUMN `captcha_enabled` `captcha_enabled` INT(1) NULL DEFAULT NULL  ;
ALTER TABLE `plugin_formbuilder_forms` CHANGE COLUMN `deleted`         `deleted`         INT(1) NULL DEFAULT NULL  ;
ALTER TABLE `plugin_formbuilder_forms` CHANGE COLUMN `publish`         `publish`         INT(1) NULL DEFAULT NULL  ;

UPDATE IGNORE `plugins` SET `icon` = 'panels.png' WHERE `name` = 'formbuilder';

ALTER IGNORE TABLE `plugin_formbuilder_forms` ADD COLUMN `use_stripe` INT(1) NOT NULL DEFAULT 0 AFTER `captcha_enabled` ;

INSERT IGNORE INTO `feeds` (`name`, `summary`, `code_path`, `order`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`)
VALUES ('Survey', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0', 'survey', 'Model_Survey,render_survey');


INSERT IGNORE INTO engine_localisation_custom_scanners (`scanner`) VALUES ('Model_Formbuilder::get_localisation_messages');
