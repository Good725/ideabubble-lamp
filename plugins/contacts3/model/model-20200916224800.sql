/*
ts:2020-09-16 22:48:00
*/

ALTER TABLE `plugin_contacts3_contact_type_columns`
    CHANGE COLUMN `table_column` `table_column` VARCHAR(512) NULL DEFAULT NULL ;


UPDATE  plugin_contacts3_contact_type_columns
SET table_column =
        'IF(`mobile`.`country_dial_code` IS NOT NULL AND `mobile`.`country_dial_code` != \'\' ,CONCAT_WS(\' \', \'+\', `mobile`.`country_dial_code`, `mobile`.`dial_code`, `mobile`.`value`), `mobile`.`value`)' WHERE `name` = 'mobile';