/*
ts:2020-05-26 11:00:00
*/

-- Fill in empty created and updated dates
UPDATE `plugin_automations` SET `created_date` = CURRENT_TIMESTAMP WHERE `created_date` IS NULL/*1.1*/;
UPDATE `plugin_automations` SET `updated_date` = `created_date`    WHERE `updated_date` IS NULL/*1.1*/;


