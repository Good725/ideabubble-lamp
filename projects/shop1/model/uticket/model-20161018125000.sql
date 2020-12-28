/*
ts:2016-10-18 12:50:00
*/

-- Remove sparklines from these reports
UPDATE IGNORE `plugin_reports_sparklines` SET `deleted` = 1 WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Website Traffic');
UPDATE IGNORE `plugin_reports_sparklines` SET `deleted` = 1 WHERE `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Top Web Pages');
