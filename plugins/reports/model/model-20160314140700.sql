/*
ts:2016-03-14 14:05:00
*/

UPDATE `plugin_reports_reports`
SET    `widget_id` = NULL
WHERE  `widget_id` NOT IN (SELECT `id` FROM `plugin_reports_widgets`);
