/*
ts:2019-10-01 08:25:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('engine_enable_org_register', 'Enable organisation register', '', 'toggle_button', 'User Registration',
 'Model_Settings,on_or_off');

UPDATE `engine_settings`
SET `value_live`  = '0',
    `value_test`  = '0',
    `value_dev`   = '0',
    `value_stage` = '0'
WHERE `variable` = 'engine_enable_org_register';

CREATE TABLE `plugin_contacts3_organisation_industries`
(
    `id`    INT         NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(55) NOT NULL,
    `label` VARCHAR(55) NULL,
    `publish` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name_UNIQUE` (`name` ASC));

CREATE TABLE `plugin_contacts3_organisation_sizes`
(
    `id`    INT         NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(55) NOT NULL,
    `label` VARCHAR(55) NULL,
    `publish` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name_UNIQUE` (`name` ASC));

CREATE TABLE `plugin_contacts3_job_functions`
(
    `id`    INT         NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(55) NOT NULL,
    `label` VARCHAR(55) NULL,
    `publish` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name_UNIQUE` (`name` ASC));


ALTER TABLE `plugin_contacts3_contacts`
    ADD COLUMN `job_title` VARCHAR(45) NULL DEFAULT NULL AFTER `hourly_rate`,
    ADD COLUMN `job_function_id` VARCHAR(45) NULL DEFAULT NULL AFTER `job_title`;

INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('1', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('2-5', '2_to_5');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('6-20', '6_to_20');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('21-50', '21_to_50');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('51-100', '51_to_100');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('101-150', '101_to_150');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('151-250', '151_to_250');
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`)
VALUES ('250+', '250_plus');

INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('accounting_financial', 'Accounting / Financial');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('consulting_agency', 'Consulting / Agency');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('blogger_author', 'Blogger / Author');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('ecommerce_retail', 'E-Commerce / Retail');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('entertainment_events', 'Entertainment / Events');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('fitness_nutrition', 'Fitness / Nutrition');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('healthcare', 'Healthcare');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('media_publishing', 'Media / Publishing');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('non_profit', 'Non-profit');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('online_training_education', 'Online Training / Education');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('real_estate', 'Real Estate');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('software', 'Software');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('travel_hospitality', 'Travel / Hospitality');
INSERT INTO `plugin_contacts3_organisation_industries` (`name`, `label`)
VALUES ('other', 'Other');

INSERT INTO `plugin_contacts3_job_functions` (`name`, `label`)
VALUES ('hr', 'HR');
INSERT INTO `plugin_contacts3_job_functions` (`name`, `label`)
VALUES ('accounting', 'Accounting');
INSERT INTO `plugin_contacts3_job_functions` (`name`, `label`)
VALUES ('other', 'Other');

CREATE TABLE `plugin_contacts3_organisations`
(
    `id`                       INT NOT NULL AUTO_INCREMENT,
    `contact_id`               INT NOT NULL,
    `organisation_size_id`     INT NULL,
    `organisation_industry_id` INT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `contact_id_UNIQUE` (`contact_id` ASC)
);

INSERT IGNORE INTO `engine_settings` (`variable`, `linked_plugin_name`,  `name`, `note`, `type`, `group`, `options`)
VALUES ('display_organisation_industries', 'contacts3', 'Display organisation industries', '', 'toggle_button', 'Contacts',
        'Model_Settings,on_or_off');

UPDATE `engine_settings`
SET `value_live`  = '1',
    `value_test`  = '1',
    `value_dev`   = '1',
    `value_stage` = '1'
WHERE `variable` = 'display_organisation_industries';


UPDATE `plugin_contacts3_organisation_sizes` SET `publish` = '0' WHERE (`name` = '1');
UPDATE `plugin_contacts3_organisation_sizes` SET `publish` = '0' WHERE (`name` = '2_to_5');
UPDATE `plugin_contacts3_organisation_sizes` SET `publish` = '0' WHERE (`name` = '101_to_150');
UPDATE `plugin_contacts3_organisation_sizes` SET `publish` = '0' WHERE (`name` = '151_to_250');
UPDATE `plugin_contacts3_organisation_sizes` SET `publish` = '0' WHERE (`name` = '250_plus');

INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('1_to_5', '1-5', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('101_to_250', '101-250', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('251_to_500', '251-500', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('501_to_1k', '501-1k', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('1k_to_5k', '1k-5k', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('5k_to_10k', '5k-10k', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('10k_to_50k', '10k-50k', '1');
INSERT INTO `plugin_contacts3_organisation_sizes` (`name`, `label`, `publish`) VALUES ('50k_plus', '50k+', '1');

ALTER TABLE `plugin_contacts3_organisation_sizes`
    ADD COLUMN `order` INT(3) NULL DEFAULT 50 AFTER `publish`;

UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '1' WHERE (`name` = '1_to_5');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '2' WHERE (`name` = '6_to_20');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '3' WHERE (`name` = '21_to_50');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '4' WHERE (`name` = '51_to_100');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '5' WHERE (`name` = '101_to_250');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '6' WHERE (`name` = '251_to_500');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '7' WHERE (`name` = '501_to_1k');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '8' WHERE (`name` = '1k_to_5k');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '9' WHERE (`name` = '5k_to_10k');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '10' WHERE (`name` = '10k_to_50k');
UPDATE `plugin_contacts3_organisation_sizes` SET `order` = '11' WHERE (`name` = '50k_plus');