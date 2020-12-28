/*
ts:2020-07-03 15:22:00
*/

ALTER TABLE `plugin_courses_counties` ADD `code` VARCHAR(45) NULL DEFAULT NULL ;
ALTER TABLE `plugin_courses_counties` ADD `country_code` VARCHAR(45) NULL DEFAULT NULL ;

UPDATE `plugin_courses_counties` SET `country_code` = 'NIR', `code` = 'AM' WHERE (`name` = 'Antrim');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR', `code` = 'AH' WHERE (`name` = 'Armagh');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'CW' WHERE (`name` = 'Carlow');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'CN' WHERE (`name` = 'Cavan');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'CE' WHERE (`name` = 'Clare');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'CC' WHERE (`name` = 'Cork');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'DY' WHERE (`name` = 'Derry');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'DL' WHERE (`name` = 'Donegal');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'DN' WHERE (`name` = 'Down');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR', `code` = 'FH' WHERE (`name` = 'Fermanagh');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'GY' WHERE (`name` = 'Galway');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'KY' WHERE (`name` = 'Kerry');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'KE' WHERE (`name` = 'Kildare');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'KK' WHERE (`name` = 'Kilkenny');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'LS' WHERE (`name` = 'Laois');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'LM' WHERE (`name` = 'Leitrim');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'LK' WHERE (`name` = 'Limerick');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'LD' WHERE (`name` = 'Longford');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'LH' WHERE (`name` = 'Louth');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'MO' WHERE (`name` = 'Mayo');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'MH' WHERE (`name` = 'Meath');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'MN' WHERE (`name` = 'Monaghan');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'OY' WHERE (`name` = 'Offaly');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'RN' WHERE (`name` = 'Roscommon');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'SO' WHERE (`name` = 'Sligo');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'TN' WHERE (`name` = 'Tipperary');
UPDATE `plugin_courses_counties` SET `country_code` = 'NIR', `code` = 'TE' WHERE (`name` = 'Tyrone');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'WD' WHERE (`name` = 'Waterford');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'WM' WHERE (`name` = 'Westmeath');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'WX' WHERE (`name` = 'Wexford');
UPDATE `plugin_courses_counties` SET `country_code` = 'IRL', `code` = 'WX' WHERE (`name` = 'Wicklow');
