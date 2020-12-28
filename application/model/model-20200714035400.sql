/*
ts:2020-07-14 03:54:00
*/

UPDATE `plugin_courses_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Down');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Derry');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Tyrone');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Fermanagh');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Antrim');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Armagh');


UPDATE `engine_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Down');
UPDATE `engine_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Derry');
UPDATE `engine_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Tyrone');
UPDATE `engine_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Fermanagh');
UPDATE `engine_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Antrim');
UPDATE `engine_counties` SET `country_code` = 'NIR' WHERE (`name` = 'Armagh');

INSERT IGNORE INTO `engine_counties` (`name`, `region`, `date_created`, `date_modified`, `publish`, `deleted`, `code`, `country_code`) VALUES
('Dublin', 0, NOW(), NOW(), 1, 0, 'DU', 'IRL');




