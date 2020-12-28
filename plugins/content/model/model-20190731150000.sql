/*
ts:2019-07-31 15:00:00
*/

INSERT IGNORE INTO `plugin_content_types` (`name`, `friendly_name`) VALUES ('audio', 'Audio');

UPDATE `plugin_content_types` SET `publish` = 1 WHERE `name` = 'audio';

ALTER TABLE `plugin_content_content`
ADD COLUMN `available_from` TIMESTAMP NULL DEFAULT NULL AFTER `survey_id`,
ADD COLUMN `available_to`   TIMESTAMP NULL DEFAULT NULL AFTER `available_from`;

ALTER TABLE `plugin_content_content`
ADD COLUMN `allow_skipping` TINYINT(1) DEFAULT NULL AFTER `survey_id`;

-- Set order
ALTER TABLE `plugin_content_types` ADD COLUMN `order` INT DEFAULT NULL AFTER `friendly_name`;
UPDATE `plugin_content_types` SET `order` = 1 WHERE `name` = 'text';
UPDATE `plugin_content_types` SET `order` = 2 WHERE `name` = 'pdf';
UPDATE `plugin_content_types` SET `order` = 3 WHERE `name` = 'video';
UPDATE `plugin_content_types` SET `order` = 4 WHERE `name` = 'audio';
UPDATE `plugin_content_types` SET `order` = 5 WHERE `name` = 'scorm';


ALTER TABLE `plugin_content_content` ADD COLUMN `label` VARCHAR(100) DEFAULT NULL AFTER `name`;