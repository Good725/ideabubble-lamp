/*
ts:2020-07-08 01:57:00
*/

ALTER TABLE `engine_counties` ADD `code` VARCHAR(45) NULL DEFAULT NULL ;
ALTER TABLE `engine_counties` ADD `country_code` VARCHAR(45) NULL DEFAULT NULL ;

UPDATE `engine_counties` SET `country_code` = 'NIR', `code` = 'AM' WHERE (`name` = 'Antrim');
UPDATE `engine_counties` SET `country_code` = 'NIR', `code` = 'AH' WHERE (`name` = 'Armagh');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'CW' WHERE (`name` = 'Carlow');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'CN' WHERE (`name` = 'Cavan');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'CE' WHERE (`name` = 'Clare');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'CC' WHERE (`name` = 'Cork');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'DY' WHERE (`name` = 'Derry');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'DL' WHERE (`name` = 'Donegal');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'DN' WHERE (`name` = 'Down');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'DU' WHERE (`name` = 'Dublin');
UPDATE `engine_counties` SET `country_code` = 'NIR', `code` = 'FH' WHERE (`name` = 'Fermanagh');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'GY' WHERE (`name` = 'Galway');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'KY' WHERE (`name` = 'Kerry');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'KE' WHERE (`name` = 'Kildare');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'KK' WHERE (`name` = 'Kilkenny');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'LS' WHERE (`name` = 'Laois');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'LM' WHERE (`name` = 'Leitrim');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'LK' WHERE (`name` = 'Limerick');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'LD' WHERE (`name` = 'Longford');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'LH' WHERE (`name` = 'Louth');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'MO' WHERE (`name` = 'Mayo');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'MH' WHERE (`name` = 'Meath');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'MN' WHERE (`name` = 'Monaghan');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'OY' WHERE (`name` = 'Offaly');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'RN' WHERE (`name` = 'Roscommon');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'SO' WHERE (`name` = 'Sligo');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'TN' WHERE (`name` = 'Tipperary');
UPDATE `engine_counties` SET `country_code` = 'NIR', `code` = 'TE' WHERE (`name` = 'Tyrone');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'WD' WHERE (`name` = 'Waterford');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'WM' WHERE (`name` = 'Westmeath');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'WX' WHERE (`name` = 'Wexford');
UPDATE `engine_counties` SET `country_code` = 'IRL', `code` = 'WX' WHERE (`name` = 'Wicklow');
