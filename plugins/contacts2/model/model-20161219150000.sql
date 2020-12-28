/*
ts:2016-12-19 15:00:00
*/
ALTER TABLE `plugin_contacts_contact`
  ADD COLUMN `country`     VARCHAR(100) NULL  AFTER `address4` ,
  ADD COLUMN `postcode`    VARCHAR(100) NULL  AFTER `country` ,
  ADD COLUMN `coordinates` VARCHAR(100) NULL  AFTER `postcode`
  ;

ALTER TABLE `plugin_contacts_contact`
  CHANGE COLUMN `country` `country_id` INT(11) NULL DEFAULT NULL;